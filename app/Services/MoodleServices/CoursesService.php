<?php

namespace App\Services\MoodleServices;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Course;

class CoursesService
{
    protected $url;
    protected $token;

    /**
     * تحميل قيم URL و TOKEN من env أو عبر config()
     */
    public function __construct()
    {
        $this->url   = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->token = config('app.moodle_wstoken');
    }

    /**
     * إنشاء دورة في Moodle: core_course_create_courses
     */
    public function createCourse(Course $course)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_create_courses',
            'moodlewsrestformat' => 'json',
            'courses' => [[
                'fullname'   => $course->title,
                'shortname'  => substr($course->title, 0, 10),
                // التصنيف في Moodle:
                'categoryid' => $course->moodle_category_id ?? 1,
                // 'summary' => $course->description,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data[0]['id'])) {
                // نجح الإنشاء وأعاد الـid
                return $data[0]['id']; // رقم المقرر (course ID) في Moodle
            }
        }

        Log::error('Failed to create course in Moodle.', ['response' => $response->body()]);
        return null;
    }

    /**
     * تحديث بيانات دورة في Moodle: core_course_update_courses
     */
    public function updateCourse(Course $course)
    {
        // لو ما في moodle_course_id، لا داعي للاستمرار
        if (!$course->moodle_course_id) {
            return;
        }

        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_update_courses',
            'moodlewsrestformat' => 'json',
            'courses' => [[
                'id'        => $course->moodle_course_id,
                'fullname'  => $course->title,
                'shortname' => substr($course->title, 0, 10),
                // 'summary' => $course->description,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }

    /**
     * حذف دورة من Moodle: core_course_delete_courses
     */
    public function deleteCourse($moodleCourseId)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_delete_courses',
            'moodlewsrestformat' => 'json',
            'courseids'          => [$moodleCourseId],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }
}
