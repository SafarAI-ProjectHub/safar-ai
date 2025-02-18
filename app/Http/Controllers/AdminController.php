<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Jobs\ProcessUnitAI;
use App\Models\Course;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\CourseCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\LevelTestAssessment;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\VideoToAudioService;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    protected $videoToAudioService;

    public function __construct(VideoToAudioService $videoToAudioService)
    {
        $this->videoToAudioService = $videoToAudioService;
    }

    public function index()
    {
        return view('dashboard.index');
    }

    /*
     * -------------- ADMIN MANAGEMENT --------------
     */

    public function listAdmin(Request $request)
    {
        if ($request->ajax()) {
            $admins = User::whereHas('roles', function ($query) {
                $query->where('name', 'Admin');
            })->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Super Admin');
            })->select([
                'id', 
                'first_name', 
                'last_name', 
                'email', 
                'phone_number', 
                'date_of_birth', 
                'country_location', 
                'status'
            ])->get();

            return DataTables::of($admins)
                ->addColumn('action', function ($admin) {
                    return '<div class="d-flex justify-content-around gap-2" >
                                <a href="' . route('admin.edit', $admin->id) . '" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteAdmin(' . $admin->id . ')">Delete</button>
                            </div>';
                })
                ->editColumn('date_of_birth', function ($admin) {
                    return $admin->date_of_birth 
                        ? with(new Carbon($admin->date_of_birth))->format('Y-m-d') 
                        : '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('dashboard.admin.list_admin');
    }

    public function createAdmin()
    {
        return view('dashboard.admin.create_admin');
    }

    public function storeAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'date_of_birth'=> 'required|date',
            'country_code' => 'required|string|max:5',
            'password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'phone_number'     => $request->country_code . $request->phone_number,
            'date_of_birth'    => $request->date_of_birth,
            'password'         => Hash::make($request->password),
            'country_location' => $request->country_location,
            // عند الإنشاء الافتراضي ممكن تختار أي حالة تريدها
            'status'           => 'active', 
        ]);

        $user->assignRole('Admin');

        return response()->json(['message' => 'Admin created successfully']);
    }

    public function editAdmin($id)
    {
        $user = User::findOrFail($id);

        // جلب جميع القيم المميزة (distinct) من عمود status في جدول users
        // وذلك لجعل القائمة المنسدلة ديناميكية
        $statuses = User::select('status')->distinct()->pluck('status');

        return view('dashboard.admin.edit_admin', compact('user', 'statuses'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:15',
            'date_of_birth'=> 'required|date',
            'country_code' => 'required|string|max:5',
            'status'       => 'required|string',  // تحقق من أن status مطلوب
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($id);

        $user->update([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'phone_number'     => $request->country_code . $request->phone_number,
            'date_of_birth'    => $request->date_of_birth,
            'country_location' => $request->country_location,
            // حفظ الحالة المختارة من الـSelect
            'status'           => $request->status,
        ]);

        if ($request->password) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['message' => 'Admin updated successfully']);
    }

    public function deleteAdmin($id)
    {
        $user = User::findOrFail($id);
        if ($user) {
            if ($user->timeLogs()->exists()) {
                $user->timeLogs()->delete();
            }
        }

        $user->delete();
        return response()->json(['message' => 'Admin deleted successfully']);
    }




    /*
     * -------------- TEACHERS --------------
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
                return 'Teacher';
            })
            ->addColumn('cv_link', function ($teacher) {
                return $teacher->cv_link;
            })
            ->addColumn('exam_result', function ($teacher) {
                if ($teacher->user->levelTestAssessments()->exists()) {
                    return '<button class="btn btn-primary btn-sm view-assessment" data-id="' . $teacher->user->id . '">View Result</button>';
                } else {
                    return 'No attempt yet';
                }
            })
            ->rawColumns(['cv_link', 'exam_result'])
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
            $data = Teacher::with('user', 'user.contract')->where('approval_status', 'approved')->get();

            return DataTables::of($data)
                ->addColumn('id', function ($row) {
                    return $row->id;
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
                ->addColumn('phone_number', function ($row) {
                    return $row->user->phone_number;
                })
                ->addColumn('salary', function ($row) {
                    if ($row->user->contract) {
                        return $row->user->contract->salary . ' per ' . $row->user->contract->salary_period;
                    } else {
                        return 'No Contract Yet';
                    }
                })
                ->addColumn('cv_link', function ($row) {
                    return $row->cv_link ? '<a href="' . asset($row->cv_link) . '" class="view-cv" target="_blank">View CV</a>' : 'No CV';
                })
                ->addColumn('exam_result', function ($teacher) {
                    if ($teacher->user->levelTestAssessments()->exists()) {
                        return '<button class="btn btn-primary btn-sm view-assessment" data-id="' . $teacher->user->id . '">View Result</button>';
                    } else {
                        return 'No attempt yet';
                    }
                })
                ->addColumn('actions', function ($row) {
                    if ($row->user->contract) {
                        return '<div class="d-flex justify-content-around gap-2">
                                    <a href="#" class="btn btn-info edit-contract text-white" data-id="' . $row->user->contract->id . '">Edit Contract</a>
                                    <button class="btn btn-warning btn-sm edit-teacher" data-id="' . $row->id . '">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-teacher" data-id="' . $row->id . '">Delete</button>
                                </div>';
                    } else {
                        return '<div class="d-flex justify-content-around gap-2">
                                    <a href="#" class="btn btn-primary create-contract" data-id="' . $row->user->id . '">Create Contract</a>
                                    <button class="btn btn-warning btn-sm edit-teacher" data-id="' . $row->id . '">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-teacher" data-id="' . $row->id . '">Delete</button>
                                </div>';
                    }
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
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|string|email|max:255',
            'phone_number'      => 'required|string|max:20',
            'country_location'  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teacher = Teacher::findOrFail($id);
        $user = $teacher->user;

        $user->update([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'email'            => $request->email,
            'phone_number'     => $request->phone_number,
            'country_location' => $request->country_location,
        ]);

        return response()->json(['message' => 'Teacher updated successfully']);
    }

    public function deleteTeacher($id)
    {
        try {
            $teacher = Teacher::findOrFail($id);
            $user = $teacher->user;

            if ($user) {
                if ($user->timeLogs()->exists()) {
                    $user->timeLogs()->delete();
                }
            }
            $teacher->user()->delete();
            $teacher->delete();

            return response()->json(['message' => 'Teacher deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting teacher', 'message' => $e->getMessage()], 422);
        }
    }


    /*
     * -------------- COURSES --------------
     */
    public function courses()
    {
        $categories = CourseCategory::all();
        $teachers   = Teacher::with('user')->get();
        $blocks     = \App\Models\Block::all();

        return view('dashboard.admin.courses', compact('categories', 'teachers','blocks'));
    }

    public function getCourses(Request $request)
    {
        if ($request->ajax()) {
            $query = Course::with(['category', 'teacher.user', 'block']);

            if (Auth::user()->hasRole('Teacher')) {
                $techerid = Teacher::where('teacher_id', Auth::id())->first()->id;
                $query->where('teacher_id', $techerid);
            }

            $data = $query->get();

            return DataTables::of($data)
                ->addColumn('category', function ($row) {
                    return 'Age Range: ' . $row->category->age_group;
                })
                ->addColumn('block_name', function ($row) {
                    return optional($row->block)->name ?? 'No Block';
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
                ->addColumn('completed', function ($row) {
                    return $row->completed;
                })
                ->addColumn('actions', function ($row) {
                    $actions = '';
                    if (Auth::user()->hasAnyRole(['Super Admin', 'Admin'])) {
                        $assignButton      = '<a href="#" class="btn btn-primary btn-sm assign-teacher-btn" data-course-id="' . $row->id . '"><i class="bx bx-user-plus"></i> ' . ($row->teacher ? 'Change Teacher' : 'Assign Teacher') . '</a>';
                        $showUnitsButton   = '<a href="' . url('courses') . '/' . $row->id . '/units" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> Show Lessons</a>';
                        $viewCourseButton  = '<a href="' . url('courses') . '/' . $row->id . '/show" class="btn btn-primary btn-sm"><i class="bx bx-detail"></i> View Unit</a>';
                        $deleteCouresButton= '<a class="btn btn-sm btn-outline-dark btn-danger delete-btn p-2" data-id="' . $row->id . '"><i class="bx bx-trash"></i>Delete</a>';
                        $actions = '<div class="d-flex justify-content-around gap-2">' 
                                . $assignButton 
                                . $showUnitsButton
                                . $viewCourseButton
                                . $deleteCouresButton
                                . '</div>';
                    }
                    if (Auth::user()->hasRole('Teacher')) {
                        $viewCourseButton = '<a href="' . url('/courses') . '/' . $row->id . '/show" class="btn btn-primary btn-sm"><i class="bx bx-detail"></i> View Unit</a>';
                        $showUnitsButton  = '<a href="' . url('courses') . '/' . $row->id . '/units" class="btn btn-primary btn-sm"><i class="bx bx-show-alt"></i> Show Lessons</a>';
                        $actions = '<div class="d-flex justify-content-around gap-2">'
                                . $viewCourseButton
                                . $showUnitsButton
                                . '</div>';
                    }
                    return $actions;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return null;
    }

    public function toggleComplete(Request $request, Course $course)
    {
        $course->completed = $request->completed;
        $course->save();

        return response()->json(['success' => true]);
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'block_id'     => 'required|exists:blocks,id',
            'title'        => 'required|string|max:255',
            'description'  => 'required|string',
            'category_id'  => 'required|exists:course_categories,id',
            'level'        => 'required|integer|min:1|max:6',
            'type'         => 'required|in:weekly,intensive',
            'image'        => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $imagePath = 'storage/' . $path;
        }

        $course = new Course();
        $course->block_id    = $request->block_id;
        $course->title       = $request->title;
        $course->description = $request->description;
        $course->category_id = $request->category_id;
        $course->level       = $request->level;
        $course->type        = $request->type;
        $course->image       = $imagePath ?? null;

        if (Auth::user()->hasRole('Teacher')) {
            $course->teacher_id = Auth::user()->teacher->id;
        }

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
            'course_id'  => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $course = Course::find($request->course_id);
        $course->teacher_id = $request->teacher_id;
        $course->save();

        return response()->json(['success' => 'Teacher assigned successfully!']);
    }

    public function deleteCourse($courseId)
    {
        try {
            $course = Course::findOrFail($courseId);
            $course->certificates()->delete();
            $course->students()->detach();
            $course->units()->each(function ($unit) {
                $unit->quizzes()->each(function ($quiz) {
                    $quiz->assessments()->each(function ($assessment) {
                        $assessment->userResponses()->delete();
                        $assessment->delete();
                    });
                    $quiz->questions()->each(function ($question) {
                        $question->choices()->delete();
                        $question->delete();
                    });
                    $quiz->delete();
                });
                $unit->delete();
            });
            $course->rates()->delete();
            $course->courseStudents()->delete();
            $course->delete();

            return response()->json(['success' => true, 'message' => 'Course deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting course: ' . $e->getMessage()], 500);
        }
    }

    /*
     * -------------- UNITS (Lessons) --------------
     */
    public function showUnits($courseId)
    {
        $course = Course::with('units')->findOrFail($courseId);

        // إن أردت التحقق من صلاحية المعلم
        if (Auth::user()->hasAnyRole(['Teacher'])) {
            if (Auth::id() != $course->teacher->teacher_id) {
                abort(403, 'Unauthorized action.');
            }
        }

        return view('dashboard.admin.units', compact('course'));
    }

    // (Add) Lesson
    public function storeUnit(Request $request)
    {
        \Log::info('Store Unit Request:', $request->all());

        $request->validate([
            'course_id'    => 'required|exists:courses,id',
            'title'        => 'required|string|max:255',
            'subtitle'     => 'nullable|string|max:255',
            'content_type' => 'required|in:video,text,youtube',
            'content'      => 'required_if:content_type,text',
            'video'        => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20000',
            'youtube'      => 'nullable|string',
        ]);

        $unit = new Unit();
        $unit->course_id    = $request->course_id;
        $unit->title        = $request->title;
        $unit->subtitle     = $request->subtitle;
        $unit->content_type = $request->content_type;

        if($request->content_type === 'text'){
            $unit->content = $request->content;
        } elseif($request->content_type === 'youtube'){
            $videoId = $this->extractVideoId($request->youtube);
            $checkUrl = $this->checkUrl($videoId);
            if ($checkUrl['status'] === 'error') {
                return response()->json(['error' => $checkUrl['error']], 422);
            }
            $unit->content = $videoId;
            $unit->script  = $request->youtube; // نحتفظ بالرابط الأصلي في الحقل script
        } elseif($request->content_type === 'video' && $request->hasFile('video')){
            $file = $request->file('video');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $unit->content = 'storage/' . $path;
        }

        $unit->save();

        // معالجته لاحقًا بالـ AI
        ProcessUnitAI::dispatch($unit->id, $this->videoToAudioService);

        return response()->json(['success' => 'Lesson added successfully']);
    }

    // (Read) for DataTables
    public function getUnits($courseId)
    {
        $units = Unit::where('course_id', $courseId)
                     ->select('id', 'title', 'subtitle', 'content_type')
                     ->get();

        return DataTables::of($units)
            ->addColumn('actions', function($row){
                return '
                    <div class="d-flex justify-content-around gap-2">
                        <button class="btn btn-success btn-sm view-lesson" data-id="'.$row->id.'">View Lesson</button>
                        <button class="btn btn-primary btn-sm edit-unit" data-id="'.$row->id.'">Edit</button>
                        <button class="btn btn-info btn-sm show-script" data-id="'.$row->id.'">Show Script</button>
                        <button class="btn btn-danger btn-sm delete-unit" data-id="'.$row->id.'">Delete</button>
                    </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    // (Show single lesson) - AJAX
    public function showUnit($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    // (Edit) - AJAX Get
    public function editUnit($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json($unit);
    }

    // (Update) Lesson
    public function updateUnit(Request $request, $id)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'subtitle'     => 'nullable|string|max:255',
            'content_type' => 'required|in:video,text,youtube',
            'content'      => 'required_if:content_type,text,youtube',
            'video'        => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20000',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->title        = $request->title;
        $unit->subtitle     = $request->subtitle;
        $unit->content_type = $request->content_type;

        if ($request->content_type == 'text') {
            $unit->content = $request->content;
        } elseif ($request->content_type == 'video' && $request->hasFile('video')) {
            // حذف القديم إن وجد
            if ($unit->content && Storage::disk('public')->exists(str_replace('storage/', '', $unit->content))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $unit->content));
            }
            // رفع الجديد
            $file = $request->file('video');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');
            $unit->content = 'storage/' . $path;
        } elseif ($request->content_type == 'youtube') {
            $videoId = $this->extractVideoId($request->youtube);
            $checkUrl = $this->checkUrl($videoId);
            if ($checkUrl['status'] == 'error') {
                return response()->json(['error' => $checkUrl['error']], 422);
            }
            $unit->content = $videoId;
            $unit->script  = $request->youtube;
        }

        $unit->save();

        // إعادة معالجة بالـ AI
        ProcessUnitAI::dispatch($unit->id, $this->videoToAudioService);

        return response()->json(['success' => 'Lesson updated successfully']);
    }

    // (Delete) Lesson
    public function destroyUnit(Request $request, $id)
    {
        try {
            $unit = Unit::with(['quizzes.assessments'])->findOrFail($id);
            $unitsIds = [$unit->id];

            // حذف أي Quiz/Assessments تابعة
            foreach ($unit->quizzes as $quiz) {
                foreach ($quiz->assessments as $assessment) {
                    foreach ($assessment->userResponses as $response) {
                        $response->delete();
                    }
                    $assessment->delete();
                }
                $quiz->delete();
            }

            DB::table('student_units')->whereIn('unit_id', $unitsIds)->delete();
            $unit->delete();

            return response()->json(['success' => 'Lesson and all related quizzes and assessments deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting Lesson', 'message' => $e->getMessage()], 422);
        }
    }

    // get and update script
    public function getScript($id)
    {
        $unit = Unit::findOrFail($id);
        return response()->json(['script' => $unit->script]);
    }

    public function updateScript(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $unit->script = $request->script;
        $unit->save();

        return response()->json(['success' => 'Script updated successfully!']);
    }

    /*
     * -------------- STUDENTS --------------
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
                    return '<div class="d-flex justify-content-between gap-2">
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
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => [
                'required',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'unique:users,email,' . $student->user->id
            ],
            'phone_number'            => 'required|string|max:15',
            'country_location'        => 'required|string|max:255',
            'country_code'            => 'required|string|max:5',
            'english_proficiency_level' => 'required|string|max:255',
            'subscription_status'     => 'required|string|max:255',
            'status'                  => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $student->update([
            'english_proficiency_level' => $request->english_proficiency_level,
            'subscription_status'       => $request->subscription_status,
        ]);
        $user = $student->user;

        $user->update([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone_number'      => $request->country_code . ltrim($request->phone_number, '0'),
            'country_location'  => $request->country_location,
            'status'            => $request->status,
        ]);

        return response()->json(['message' => 'Student updated successfully']);
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        if ($user) {
            if ($user->timeLogs()->exists()) {
                $user->timeLogs()->delete();
            }
            if ($user->userSubscriptions()->exists()) {
                $user->userSubscriptions()->delete();
            }
            $user->delete();
        }
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }


    /*
     * -------------- TEACHER LEVEL TEST ASSESSMENTS --------------
     */
    public function teacherAssessments()
    {
        return view('dashboard.admin.teacher_level_test_assessment');
    }

    public function getTeachersWithAssessments(DataTables $dataTables)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'Teacher');
        })
            ->whereHas('levelTestAssessments')
            ->with(['levelTestAssessments.question', 'teacher'])
            ->select('users.*');

        return $dataTables->eloquent($query)
            ->addColumn('full_name', function ($teacher) {
                return $teacher->first_name . ' ' . $teacher->last_name;
            })
            ->addColumn('years_of_experience', function ($teacher) {
                return $teacher->teacher->years_of_experience;
            })
            ->addColumn('actions', function ($teacher) {
                return '<button class="btn btn-sm btn-primary view-assessment" data-id="' . $teacher->id . '">View Assessment</button>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getTeacherAssessments($id)
    {
        $teacher = User::with(['levelTestAssessments.question.choices'])
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Teacher');
            })
            ->findOrFail($id);
        return response()->json([
            'assessments' => $teacher->levelTestAssessments,
        ]);
    }

    public function updateTeacherAssessment(Request $request, $teacherId, $assessmentId)
    {
        $assessment = LevelTestAssessment::where('user_id', $teacherId)->findOrFail($assessmentId);
        $assessment->correct = $request->input('correct') ? 1 : 0;
        $assessment->Admin_review = $request->input('admin_review');
        $assessment->save();

        return response()->json(['success' => true]);
    }


    /*
     * -------------- STUDENT LEVEL TEST ASSESSMENTS --------------
     */
    public function studentAssessments()
    {
        return view('dashboard.admin.student_level_test_assessment');
    }

    public function getStudentsWithAssessments(DataTables $dataTables)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('name', 'Student');
        })
            ->whereHas('levelTestAssessments')
            ->with(['levelTestAssessments.question', 'student'])
            ->select('users.*');

        return $dataTables->eloquent($query)
            ->addColumn('full_name', function ($student) {
                return $student->first_name . ' ' . $student->last_name;
            })
            ->addColumn('level', function ($student) {
                return $student->student->english_proficiency_level;
            })
            ->addColumn('actions', function ($student) {
                return '<button class="btn btn-sm btn-primary view-assessment" data-id="' . $student->id . '">View Assessment</button>';
            })
            ->addColumn('age', function ($student) {
                return \Carbon\Carbon::parse($student->date_of_birth)->diffInYears(now());
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function getStudentAssessments($id)
    {
        $student = User::with(['levelTestAssessments.question.choices', 'student'])
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Student');
            })
            ->findOrFail($id);

        return response()->json([
            'assessments' => $student->levelTestAssessments,
            'student'     => $student->student
        ]);
    }

    public function updateStudentAssessment(Request $request, $studentId, $assessmentId)
    {
        $assessment = LevelTestAssessment::where('user_id', $studentId)->findOrFail($assessmentId);
        $assessment->correct = $request->input('correct') ? 1 : 0;
        $assessment->admin_review = $request->input('admin_review');
        $assessment->save();

        if ($request->has('english_proficiency_level')) {
            $student = User::findOrFail($studentId);
            $student->student->english_proficiency_level = $request->input('english_proficiency_level');
            $student->student->save();
        }

        return response()->json(['success' => true]);
    }

    /*
     * -------------- Helpers --------------
     */
    private function extractVideoId($url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $queryParams);
        return $queryParams['v'] ?? null;
    }

    private function checkUrl($videoId)
    {
        $apiKey = env('YOUTUBE_API_KEY');
        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&key={$apiKey}&part=snippet,statistics";

        $response = \Illuminate\Support\Facades\Http::get($apiUrl);
        if ($response->successful() && !empty($response->json()['items'])) {
            return [
                'status' => 'success',
                'message' => 'Correct YouTube Video URL',
            ];
        }

        return [
            'status' => 'error',
            'error' => 'Invalid YouTube Video URL'
        ];
    }
}
