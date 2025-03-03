<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MoodleService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url = env('MOODLE_URL') . '/webservice/rest/server.php';
        $this->token = env('MOODLE_API_TOKEN');
    }

    public function getCourses()
    {
        $response = Http::get($this->url, [
            'wstoken' => $this->token,
            'wsfunction' => 'core_course_get_courses',
            'moodlewsrestformat' => 'json',
        ]);

        return $response->json();
    }
}
