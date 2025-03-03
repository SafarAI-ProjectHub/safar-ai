<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoodleService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->token = config('app.moodle_wstoken');
    }



    // دالة لاسترجاع الدورات من Moodle
    public function getCourses()
    {
        $response = Http::get($this->url, [
            'wstoken' => $this->token,
            'wsfunction' => 'core_course_get_courses',
            'moodlewsrestformat' => 'json',
        ]);

        return $response->json();
    }

    // دالة لاسترجاع الكويزات الخاصة بدورة معينة
    public function getQuizzesByCourse($courseId)
    {
        $response = Http::get($this->url, [
            'wstoken' => $this->token,
            'wsfunction' => 'mod_quiz_get_quizzes_by_courses',
            'moodlewsrestformat' => 'json',
            // تمرير معرف الدورة كعنصر في مصفوفة
            'courseids' => [$courseId]
        ]);

        return $response->json();
    }

    // دالة لاسترجاع أنشطة H5P الخاصة بدورة معينة
    public function getH5PActivitiesByCourse($courseId)
    {
        $response = Http::get($this->url, [
            'wstoken' => $this->token,
            'wsfunction' => 'mod_h5pactivity_get_h5pactivities_by_courses',
            'moodlewsrestformat' => 'json',
            // تمرير معرف الدورة كعنصر في مصفوفة
            'courseids' => [$courseId]
        ]);

        return $response->json();
    }
}
