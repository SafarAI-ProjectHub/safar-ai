<?php

namespace App\Observers;

use App\Models\Course;
use App\Services\MoodleService;

class CourseObserver
{
    protected $moodleService;

    public function __construct()
    {
        $this->moodleService = app(MoodleService::class);
    }

    /**
     * يُستدعى عند إنشاء كورس جديد في Laravel
     */
    public function created(Course $course)
    {
        // إنشاء الدورة في Moodle
        $moodleCourseId = $this->moodleService->createCourseInMoodle($course);
        if ($moodleCourseId) {
            $course->moodle_course_id = $moodleCourseId;
            $course->save();
        }
    }

    /**
     * يُستدعى عند تحديث كورس في Laravel
     */
    public function updated(Course $course)
    {
        // تحديث الدورة في Moodle لو لها moodle_course_id
        if ($course->moodle_course_id) {
            $this->moodleService->updateCourseInMoodle($course);
        }
    }

    /**
     * يُستدعى عند حذف كورس في Laravel
     */
    public function deleted(Course $course)
    {
        // لو لها moodle_course_id نحذفها من Moodle
        if ($course->moodle_course_id) {
            $this->moodleService->deleteCourseInMoodle($course->moodle_course_id);
        }
    }
}
