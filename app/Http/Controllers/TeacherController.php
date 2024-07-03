<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Assessment;
use App\Models\ZoomMeeting;
use App\Models\Student;
use App\Models\Teacher;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Services\ZoomService;

class TeacherController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('Student') && Auth::user()->status === 'pending') {
            $completedLevelTest = LevelTestAssessment::where('user_id', Auth::id())->exists();

            if (!$completedLevelTest) {
                $levelTestQuestions = LevelTestQuestion::with('levelTest')->whereHas('levelTest', function ($query) {
                    $query->where('exam_type', 'student')->where('active', true);
                })->get();
                return view('dashboard.student.level_test', compact('levelTestQuestions'));
            }
        }

        $courses = Course::where('teacher_id', auth()->id())->get();
        return view('dashboard.teacher.dashboard', compact('courses'));
    }

    public function getStudentQuizResults($courseId)
    {
        $quizzes = Quiz::where('course_id', $courseId)->with(['assessments.user', 'assessments.quiz'])->get();
        return view('dashboard.teacher.quiz_results', compact('quizzes'));
    }

    public function getCourses()
    {
        $teacherId = Teacher::where('teacher_id', Auth::id())->first()->id;
        $courses = Course::where('teacher_id', $teacherId)->get();
        return view('dashboard.teacher.courses', compact('courses'));
    }

    public function getStudentProfiles(Request $request)
    {
        $user = Auth::user();
        $courseId = $request->get('course_id');

        if ($user->hasRole('Super Admin')) {
            if ($courseId) {
                $students = User::whereHas('courses', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })->get();
            } else {
                $students = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Student');
                })->get();
            }

            $courses = Course::all();
        } else {
            $teacherId = Teacher::where('teacher_id', $user->id)->first()->id;
            $courses = Course::where('teacher_id', $teacherId)->get();

            if ($courseId) {
                $students = User::whereHas('courses', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })->get();
            } else {
                $courseIds = $courses->pluck('id');
                $students = User::whereHas('courses', function ($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })->get();
            }
        }

        if ($request->ajax()) {
            return Datatables::of($students)
                ->addIndexColumn()
                ->addColumn('full_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('country_location', function ($row) {
                    return $row->country_location;
                })
                ->addColumn('age', function ($row) {
                    return \Carbon\Carbon::parse($row->date_of_birth)->diffInYears(now());
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('teacher.showStudentProfile', $row->id) . '" class="btn btn-primary btn-sm">Show Profile</a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('dashboard.teacher.student_profiles', compact('students', 'courses'));
    }

    public function showStudentProfile($id)
    {
        $student = User::findOrFail($id);
        return view('dashboard.teacher.show_student_profile', compact('student'));
    }

    public function scheduleZoomMeeting(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration' => 'required|integer',
        ]);

        $zoomService = new ZoomService();
        $meeting = $zoomService->createMeeting($request->topic, $request->start_time, $request->duration);

        ZoomMeeting::create([
            'teacher_id' => auth()->id(),
            'meeting_id' => $meeting->id,
            'topic' => $request->topic,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'join_url' => $meeting->join_url,
        ]);

        return redirect()->back()->with('success', 'Zoom meeting scheduled successfully.');
    }

    public function submit()
    {


    }


}