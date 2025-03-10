<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Teacher;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Services\MoodleService; // مهم

class CourseController extends Controller
{
    public function courses()
    {
        $categories = CourseCategory::all();
        $teachers   = Teacher::with('user')->get();
        return view('dashboard.admin.courses', compact('categories', 'teachers'));
    }

    /**
     * DataTables
     */
    public function getCourses(Request $request)
{
    if ($request->ajax()) {
        // التصفية حسب category_id إن وُجد
        $categoryId = $request->input('category_id');

        $query = Course::with(['category', 'teacher.user']);

        // إذا كان المستخدم Teacher فقط، نفلتر حسب المعلّم
        if ($request->user()->hasRole('Teacher')) {
            $teacher = Teacher::where('teacher_id', $request->user()->id)->first();
            if ($teacher) {
                $query->where('teacher_id', $teacher->id);
            }
        }

        // الفلترة حسب التصنيف
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // لا تستخدم ->get() هنا، بل مرّر $query إلى DataTables مباشرةً
        return DataTables::of($query)
            ->addColumn('category', function ($row) {
                return optional($row->category)->name ?? 'No Category';
            })
            ->addColumn('teacher', function ($row) {
                return $row->teacher
                    ? ($row->teacher->user->first_name . ' ' . $row->teacher->user->last_name)
                    : 'N/A';
            })
            ->addColumn('completed', function ($row) {
                return (int) $row->completed;
            })
            ->addColumn('actions', function ($row) {
                $actions = '';
                if (auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
                    $assignBtn = '<button class="btn btn-primary btn-sm assign-teacher-btn"
                                            data-course-id="' . $row->id . '">
                                            Assign Teacher
                                        </button>';
                    $editBtn   = '<button class="btn btn-secondary btn-sm edit-btn"
                                            data-id="' . $row->id . '">
                                            Edit
                                    </button>';
                    $delBtn    = '<button class="btn btn-danger btn-sm delete-btn"
                                            data-id="' . $row->id . '">
                                            Delete
                                    </button>';
                    $actions = "<div class='d-flex gap-2'>{$assignBtn}{$editBtn}{$delBtn}</div>";
                }
                return $actions;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
    return null;
}


    /**
     * تخزين كورس جديد
     */
    public function storeCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:course_categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'level'       => 'required|integer|min:1|max:6',
            'type'        => 'required|in:weekly,intensive',
            'image'       => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // حفظ الصورة
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            $fname = uniqid() . '.' . $file->getClientOriginalExtension();
            $path  = $file->storeAs('uploads', $fname, 'public');
            $imagePath = 'storage/' . $path;
        }

        // إنشاء الكورس
        $course = new Course();
        $course->category_id = $request->category_id;
        $course->title       = $request->title;
        $course->description = $request->description;
        $course->level       = $request->level;
        $course->type        = $request->type;
        $course->image       = $imagePath;

        // لو المستخدم Teacher
        if (Auth::user()->hasRole('Teacher')) {
            $teacher = Teacher::where('teacher_id', Auth::id())->first();
            if ($teacher) {
                $course->teacher_id = $teacher->id;
            }
        }

        $course->save();

        // الـ Observer هو الذي سينشئ الكورس في Moodle
        return response()->json(['success' => 'Course added successfully!']);
    }

    /**
     * جلب بيانات الكورس للتحرير
     */
    public function editCourse($id)
    {
        return response()->json(Course::findOrFail($id));
    }

    /**
     * تحديث الكورس
     */
    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:course_categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'level'       => 'required|integer|min:1|max:6',
            'type'        => 'required|in:weekly,intensive',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course->category_id = $request->category_id;
        $course->title       = $request->title;
        $course->description = $request->description;
        $course->level       = $request->level;
        $course->type        = $request->type;

        if ($request->hasFile('image')) {
            if ($course->image && Storage::disk('public')->exists(str_replace('storage/', '', $course->image))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $course->image));
            }
            $file  = $request->file('image');
            $fname = uniqid() . '.' . $file->getClientOriginalExtension();
            $path  = $file->storeAs('uploads', $fname, 'public');
            $course->image = 'storage/' . $path;
        }

        $course->save();
        return response()->json(['success' => true, 'message' => 'Course updated successfully']);
    }

    /**
     * تبديل حالة completed
     */
    public function toggleComplete(Request $request, Course $course)
    {
        $course->completed = $request->input('completed', 0);
        $course->save();

        return response()->json(['success' => true]);
    }

    /**
     * إسناد معلّم
     */
    public function assignTeacherToCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id'  => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $course = Course::findOrFail($request->course_id);
        $course->teacher_id = $request->teacher_id;
        $course->save();

        return response()->json(['success' => 'Teacher assigned successfully!']);
    }

    /**
     * جلب قائمة المعلمين لعرضها في الـ select
     */
    public function getTeachersForAssignment()
    {
        $teachers = Teacher::with('user')->get();
        return response()->json($teachers);
    }

    /**
     * حذف الكورس
     */
    public function deleteCourse($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete(); // سيستدعي CourseObserver->deleted()
            return response()->json(['success' => true, 'message' => 'Course deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * دالة لجلب/مزامنة الكورسات من Moodle عند الضغط على زر في الواجهة
     */
    public function syncFromMoodle(MoodleService $moodleService)
    {
        $moodleService->syncCoursesFromMoodle();
        return response()->json(['message' => 'Courses synced from Moodle successfully!']);
    }
}
