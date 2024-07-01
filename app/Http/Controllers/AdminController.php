<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Models\Unit;
use Illuminate\Support\Facades\Storage;
use App\Models\CourseCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function index()
    {
        return view('dashboard.index');
    }

    /* 
     *
     *
     *Teacher functions 
     *
     *
     */

    public function applicationsIndex()
    {
        return view('dashboard.admin.applications');
    }

    public function getApplicationsIndex()
    {
        $pending = Teacher::with('user')
            ->where("approval_status", 'pending')
            ->get();

        return DataTables::of($pending)
            ->addColumn('full_name', function ($teacher) {
                return $teacher->user->getFullNameAttribute();
            })
            ->addColumn('email', function ($teacher) {
                return $teacher->user->email;
            })
            ->addColumn('position', function ($teacher) {
                return 'Teacher'; // You can customize this as needed
            })
            ->addColumn('cv_link', function ($teacher) {
                return '<a href="../' . $teacher->cv_link . '" class="view-cv" target="_blank">CV Link</a>';
            })
            ->rawColumns(['cv_link'])
            ->make(true);
    }

    public function updateTeacherStatus(Request $request)
    {
        $teacher = Teacher::find($request->teacher_id);
        if ($teacher) {
            $teacher->approval_status = $request->approval_status;
            $teacher->save();
            return response()->json(['success' => 'Status updated successfully']);
        }
        return response()->json(['error' => 'Teacher not found'], 404);
    }
    public function teachers()
    {
        return view('dashboard.admin.teachers');
    }

    public function getTeachers(Request $request)
    {
        if ($request->ajax()) {
            $data = Teacher::with('user')->where('approval_status', 'approved')->get();

            return DataTables::of($data)
                ->addColumn('full_name', function ($row) {
                    return $row->user->first_name . ' ' . $row->user->last_name;
                })
                ->addColumn('country_location', function ($row) {
                    return $row->user->country_location;
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email;
                })
                ->addColumn('phone_number', function ($row) {
                    return $row->user->phone_number;
                })
                ->addColumn('cv_link', function ($row) {
                    return $row->cv_link ? '<a href="' . asset($row->cv_link) . '" class="view-cv" target="_blank">View CV</a>' : 'No CV';
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex justify-content-around gap-2" ><button class="btn btn-warning btn-sm edit-teacher" data-id="' . $row->id . '">Edit</button>' .
                        '<button class="btn btn-danger btn-sm delete-teacher" data-id="' . $row->id . '">Delete</button> </div>';
                })
                ->rawColumns(['cv_link', 'actions'])
                ->make(true);
        }

        return null;
    }

    public function editTeacher($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return response()->json($teacher);
    }

    public function updateTeacher(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:15',
            'country_location' => 'required|string|max:255',
            // 'cv_link' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country_location' => $request->country_location,
        ]);

        // $teacher->update([
        //     'cv_link' => $request->cv_link,
        // ]);

        return response()->json(['message' => 'Teacher updated successfully']);
    }

    public function deleteTeacher($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $teacher->user()->delete(); // Assuming the user relationship is defined in the Teacher model
            $teacher->delete(); // Deletes the teacher

            return response()->json(['message' => 'Teacher deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting teacher', 'message' => $e->getMessage()], 422);
        }
    }



    /* 
     *
     *
     *Courses functions 
     *
     *
     */

    public function courses()
    {
        $categories = CourseCategory::all();
        $teachers = Teacher::with('user')->get();
        return view('dashboard.admin.courses', compact('categories', 'teachers'));
    }

    public function getCourses(Request $request)
    {
        if ($request->ajax()) {
            $query = Course::with(['category', 'teacher.user']);

            if (Auth::user()->hasRole('Teacher')) {
                $techerid = Teacher::where('teacher_id', Auth::id())->first()->id;
                $query->where('teacher_id', $techerid);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addColumn('category', function ($row) {
                    return 'Age Range: ' . $row->category->age_group;
                })
                ->addColumn('level', function ($row) {
                    return $row->level;
                })
                ->addColumn('type', function ($row) {
                    return ucfirst($row->type);
                })
                ->addColumn('teacher', function ($row) {
                    return $row->teacher ? $row->teacher->user->first_name . ' ' . $row->teacher->user->last_name : 'N/A';
                })
                ->addColumn('actions', function ($row) {
                    $actions = '';

                    if (Auth::user()->hasAnyRole(['Super Admin', 'Admin'])) {
                        $assignButton = '<a href="#" class="btn btn-primary btn-sm assign-teacher-btn" data-course-id="' . $row->id . '"><i class="bx bx-user-plus"></i> ' . ($row->teacher ? 'Change Teacher' : 'Assign Teacher') . '</a>';
                        $showUnitsButton = '<a href="' . url('courses') . '/' . $row->id . '/units" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> Show Units</a>';
                        $viewCourseButton = '<a href="' . url('courses') . '/' . $row->id . '/show" class="btn btn-primary btn-sm"><i class="bx bx-detail"></i> View Course</a>';
                        $actions = '<div class="d-flex justify-content-around gap-2">' . $assignButton . $showUnitsButton . $viewCourseButton . '</div>';
                    }

                    if (Auth::user()->hasRole('Teacher')) {
                        $viewCourseButton = '<a href="' . url('/courses') . '/' . $row->id . '/show" class="btn btn-primary btn-sm"><i class="bx bx-detail"></i> View Course</a>';
                        $showUnitsButton = '<a href="' . url('courses') . '/' . $row->id . '/units" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> Show Units</a>';
                        $actions = '<div class="d-flex justify-content-around gap-2">' . $viewCourseButton . $showUnitsButton . '</div>';
                    }

                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return null;
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:course_categories,id',
            'level' => 'required|integer|min:1|max:6',
            'type' => 'required|in:weekly,intensive',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Store the image
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $imagePath = 'storage/' . $path;
        }

        // Create the course
        $course = new Course();
        $course->title = $request->title;
        $course->description = $request->description;
        $course->category_id = $request->category_id;
        $course->level = $request->level;
        $course->type = $request->type;
        $course->image = $imagePath;
        $course->save();

        return response()->json(['success' => 'Course added successfully']);
    }

    public function getTeachersForAssignment()
    {
        $teachers = Teacher::with('user')->get();
        return response()->json($teachers);
    }

    public function assignTeacherToCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $course = Course::find($request->course_id);
        $course->teacher_id = $request->teacher_id;
        $course->save();

        return response()->json(['success' => 'Teacher assigned successfully!']);
    }


    /* 
     *
     *
     *Units functions 
     *
     *
     */

    public function showUnits($courseId)
    {

        $course = Course::with('units')->findOrFail($courseId);
        if (Auth::user()->hasAnyRole(['Teacher'])) {
            if (Auth::id() != $course->teacher->teacher_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('dashboard.admin.units', compact('course'));
    }

    public function getUnits($courseId)
    {
        $units = Unit::where('course_id', $courseId)->get();

        return DataTables::of($units)
            ->addColumn('actions', function ($row) {
                return '<button class="btn btn-warning btn-sm edit-unit" data-id="' . $row->id . '">Edit</button>' .
                    '<button class="btn btn-primary btn-sm update-status" data-id="' . $row->id . '" data-status="' . $row->approval_status . '">Update Status</button> ' .
                    '<button class="btn btn-danger btn-sm delete-unit" data-id="' . $row->id . '">Delete</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function storeUnit(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content_type' => 'required|in:video,text',
            'content' => 'required_if:content_type,text',
            // 'video' => 'required_if:content_type,video|file|mimes:mp4,mov,ogg,qt,avi|max:20000'
        ]);

        $unit = new Unit();
        $unit->course_id = $request->course_id;
        $unit->title = $request->title;
        $unit->subtitle = $request->subtitle;
        $unit->content_type = $request->content_type;

        if ($request->content_type == 'text') {
            $unit->content = $request->content;
        } else if ($request->content_type == 'video' && $request->hasFile('video')) {
            $file = $request->file('video');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $unit->content = 'storage/' . $path;
        }

        $unit->save();

        return response()->json(['success' => 'Unit added successfully']);
    }


    public function editUnit($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    public function updateUnit(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content_type' => 'required|in:video,text',
            'content' => 'required_if:content_type,text',
            'video' => 'required_if:content_type,video|file|mimes:mp4,mov,ogg,qt|max:20000'
        ]);

        $unit = Unit::findOrFail($id);
        $unit->title = $request->title;
        $unit->subtitle = $request->subtitle;
        $unit->content_type = $request->content_type;

        if ($request->content_type == 'text') {
            $unit->content = $request->content;
        } else if ($request->content_type == 'video' && $request->hasFile('video')) {
            // Delete the old video file if it exists
            if ($unit->content && Storage::disk('public')->exists($unit->content)) {
                Storage::disk('public')->delete($unit->content);
            }

            // Store the new video file
            $file = $request->file('video');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $unit->content = 'storage/' . $path;
        }

        $unit->save();

        return response()->json(['success' => 'Unit updated successfully']);
    }

    public function destroyUnit(Request $request, $id)
    {
        try {
            $unit = Unit::findOrFail($id);
            $unit->delete(); // Deletes the unit

            return response()->json(['success' => 'Unit deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting unit', 'message' => $e->getMessage()], 422);
        }
    }

    /*
     *
     * Students functions
     *
     */
    public function showStudents()
    {
        return view('dashboard.admin.students');
    }

    public function getStudents(Request $request)
    {
        if ($request->ajax()) {
            $data = Student::with('user')->get();

            return DataTables::of($data)
                ->addColumn('student_id', function ($row) {
                    return $row->id;
                })
                ->addColumn('user_id', function ($row) {
                    return $row->user->id;
                })
                ->addColumn('full_name', function ($row) {
                    return $row->user->first_name . ' ' . $row->user->last_name;
                })
                ->addColumn('country_location', function ($row) {
                    return $row->user->country_location;
                })
                ->addColumn('email', function ($row) {
                    return $row->user->email;
                })
                ->addColumn('english_proficiency_level', function ($row) {
                    return $row->english_proficiency_level;
                })
                ->addColumn('subscription_status', function ($row) {
                    return $row->subscription_status;
                })
                ->addColumn('phone_number', function ($row) {
                    return $row->user->phone_number;
                })
                ->addColumn('age', function ($row) {
                    return \Carbon\Carbon::parse($row->user->date_of_birth)->diffInYears(now());
                })
                ->addColumn('status', function ($row) {
                    return $row->user->status;
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="d-flex justify-content-between">
                                <button class="btn btn-warning btn-sm edit-student" data-id="' . $row->id . '">Edit</button>
                                <button class="btn btn-danger btn-sm delete-student" data-id="' . $row->id . '">Delete</button>
                            </div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return null;
    }

    public function editStudent($id)
    {
        $student = Student::with('user')->findOrFail($id);

        return response()->json($student);
    }

    public function updateStudent(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone_number' => 'required|string|max:15',
            'country_location' => 'required|string|max:255',
            'country_code' => 'required|string|max:5',
            'english_proficiency_level' => 'required|string|max:255',
            'subscription_status' => 'required|string|max:255',
            'status' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student = Student::findOrFail($id);

        $student->update([
            'english_proficiency_level' => $request->english_proficiency_level,
            'subscription_status' => $request->subscription_status,
        ]);
        $user = $student->user;

        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->country_code . ltrim($request->phone_number, '0'),
            'country_location' => $request->country_location,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Student updated successfully']);
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->user()->delete();
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}