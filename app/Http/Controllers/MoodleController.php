<?php

namespace App\Http\Controllers;

use App\Services\MoodleService;
use Illuminate\Http\Request;

class MoodleController extends Controller
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    // عرض قائمة الدورات
    public function showCourses()
    {
        $courses = $this->moodleService->getCourses();
        return view('moodle.courses', compact('courses'));
    }

    // عرض تفاصيل الدورة (الكويزات وأنشطة H5P)
    public function showCourseDetails($courseId)
    {
        $quizzes = $this->moodleService->getQuizzesByCourse($courseId);
        $h5pactivities = $this->moodleService->getH5PActivitiesByCourse($courseId);
        return view('moodle.course-details', compact('courseId', 'quizzes', 'h5pactivities'));
    }

    // تشغيل كويز محدد (عرض صفحة الكويز عبر iframe)
    public function runQuiz($courseId, $quizId)
    {
        // الحصول على بيانات الكويز من Moodle باستخدام معرف الدورة
        // هنا نفترض أن getQuizzesByCourse يعيد مصفوفة من الكويزات
        $quizzes = $this->moodleService->getQuizzesByCourse($courseId);
        $quiz = collect($quizzes['quizzes'] ?? [])->firstWhere('id', (int)$quizId);
        if (!$quiz) {
            abort(404, 'Quiz not found');
        }
        return view('moodle.run-quiz', compact('quiz'));
    }

    // تشغيل نشاط H5P محدد (عرض صفحة النشاط عبر iframe)
    public function runH5P($courseId, $activityId)
    {
        $activities = $this->moodleService->getH5PActivitiesByCourse($courseId);
        $activity = collect($activities['h5pactivities'] ?? [])->firstWhere('id', (int)$activityId);
        if (!$activity) {
            abort(404, 'H5P Activity not found');
        }
        return view('moodle.run-h5p', compact('activity'));
    }
    
    // الدوال الخاصة بواجهات API (للاختبار)
    public function getCourses()
    {
        $courses = $this->moodleService->getCourses();
        return response()->json($courses);
    }

    public function getQuizzes(Request $request, $courseId)
    {
        $quizzes = $this->moodleService->getQuizzesByCourse($courseId);
        return response()->json($quizzes);
    }

    public function getH5PActivities(Request $request, $courseId)
    {
        $activities = $this->moodleService->getH5PActivitiesByCourse($courseId);
        return response()->json($activities);
    }
}
