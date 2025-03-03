<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleUserService
{
    protected $moodleUrl;
    protected $moodleToken;

    public function __construct()
    {
        $this->moodleUrl = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->moodleToken = config('app.moodle_wstoken');
    }

    public function createUser($user)
    {
        $postData = [
            'wstoken' => $this->moodleToken,
            'wsfunction' => 'core_user_create_users',
            'moodlewsrestformat' => 'json',
            'users' => [
                [
                    'username' => $user->email,
                    'password' => 'P@ssw0rd!',
                    'firstname' => $user->first_name,
                    'lastname' => $user->last_name,
                    'email' => $user->email,
                    'auth' => 'manual',
                    'city' => 'Amman',
                    'country' => 'JO',
                ]
            ]
        ];


        $response = Http::asForm()->post($this->moodleUrl, $postData);
        $responseData = $response->json();


        if (!empty($responseData) && isset($responseData[0]['id'])) {
            return $responseData[0]['id'];
        }
        return null;
    }
}

