<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;

class MoodleService
{
    protected $url;
    protected $token;

    /**
     * تحميل قيم URL و TOKEN من ملف env أو config
     */
    public function __construct()
    {
        $this->url   = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->token = config('app.moodle_wstoken');
    }

    public function getCourses()
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_courses',
            'moodlewsrestformat' => 'json',
        ];

        $response = Http::get($this->url, $params);
        return $response->json();
    }

    /**
     * مثال: mod_quiz_get_quizzes_by_courses
     */
    public function getQuizzesByCourse($courseId)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'mod_quiz_get_quizzes_by_courses',
            'moodlewsrestformat' => 'json',
            'courseids'          => [$courseId],
        ];

        $response = Http::get($this->url, $params);
        return $response->json();
    }

    /**
     * مثال: mod_h5pactivity_get_h5pactivities_by_courses
     */
    public function getH5PActivitiesByCourse($courseId)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'mod_h5pactivity_get_h5pactivities_by_courses',
            'moodlewsrestformat' => 'json',
            'courseids'          => [$courseId],
        ];

        $response = Http::get($this->url, $params);
        return $response->json();
    }

    /**
     * إنشاء دورة جديدة في Moodle: core_course_create_courses
     */
    public function createCourseInMoodle(Course $course)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_create_courses',
            'moodlewsrestformat' => 'json',
            'courses' => [[
                'fullname'   => $course->title,
                'shortname'  => substr($course->title, 0, 10),
                'categoryid' => $course->moodle_category_id ?? 1,
                //  'summary' => $course->description,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data[0]['id'])) {
                return $data[0]['id']; // رقم المقرر في Moodle
            }
        }

        Log::error('Failed to create course in Moodle.', ['response' => $response->body()]);
        return null;
    }

    /**
     * تحديث بيانات دورة في Moodle: core_course_update_courses
     */
    public function updateCourseInMoodle(Course $course)
    {
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
                // 'summary'   => $course->description,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }

    /**
     * حذف دورة من Moodle: core_course_delete_courses
     */
    public function deleteCourseInMoodle($moodleCourseId)
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

    /**
     * تسجيل طالب في دورة Moodle: enrol_manual_enrol_users
     */
    public function enrollStudentInMoodle(User $user, Course $course)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'enrol_manual_enrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments' => [[
                'roleid'   => 5, // دور الطالب في Moodle)
                'userid'   => $user->moodle_id,
                'courseid' => $course->moodle_course_id,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }

    /**
     * إنشاء مستخدم جديد في Moodle: core_user_create_users
     */
    public function createMoodleUser(User $user)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_user_create_users',
            'moodlewsrestformat' => 'json',
            'users' => [[
                'username'  => $user->email,
                'email'     => $user->email,
                'firstname' => $user->first_name,
                'lastname'  => $user->last_name,
                'password'  => $user->moodle_password ?? 'P@ssw0rd',
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data[0]['id'])) {
                return $data[0]['id']; // معرف المستخدم في Moodle
            }
        }

        Log::error('Failed to create user in Moodle.', ['response' => $response->body()]);
        return null;
    }

    /**
     * تحديث بيانات مستخدم في Moodle: core_user_update_users
     */
    public function updateMoodleUser(User $user)
    {
        if (!$user->moodle_id) {
            return null;
        }

        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_user_update_users',
            'moodlewsrestformat' => 'json',
            'users' => [[
                'id'        => $user->moodle_id,
                'username'  => $user->email,
                'email'     => $user->email,
                'firstname' => $user->first_name,
                'lastname'  => $user->last_name,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }

    /**
     * حذف مستخدم من Moodle: core_user_delete_users
     */
    public function deleteMoodleUser($moodleUserId)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_user_delete_users',
            'moodlewsrestformat' => 'json',
            'userids'            => [$moodleUserId],
        ];

        $response = Http::asForm()->post($this->url, $params);
        if ($response->successful()) {
            return true;
        }
        Log::error('Failed to delete user from Moodle.', ['response' => $response->body()]);
        return false;
    }

    /**
     * جلب درجات الطالب من Moodle: gradereport_user_get_grade_items
     */
    public function getUserGrades($moodleUserId)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'gradereport_user_get_grade_items',
            'moodlewsrestformat' => 'json',
            'userid'             => $moodleUserId,
        ];

        $response = Http::get($this->url, $params);
        return $response->json();
    }

    /**
     * إنشاء قسم (Section) في Moodle يطابق الـUnit: core_courseformat_update_course
     */
    public function createSectionForUnit(Unit $unit)
    {
        $course = $unit->course;
        if (!$course || !$course->moodle_course_id) {
            Log::warning("لا يمكن إنشاء Section لأن الدورة ليست مرتبطة بـ moodle_course_id.");
            return null;
        }
    
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_courseformat_update_course',
            'moodlewsrestformat' => 'json',
    
            'courseid' => $course->moodle_course_id,
    
            // نحدد الصيغة Weekly, وعدد الأقسام 5 (يمكن تغييره حسب حاجتك)
            'courseformatoptions' => [
                [
                    'name'  => 'format',
                    'value' => 'weeks'
                ],
                [
                    'name'  => 'numsections',
                    'value' => '5'
                ],
            ],
            'sections' => [[
                // القسم الثاني (General = صفر, أول قسم=1, ثاني قسم=2, ...)
                'sectionnum'    => 2,
                'name'          => $unit->title,
                'summary'       => $unit->subtitle ?? '',
                'summaryformat' => 1,
                'visible'       => 1
            ]],
        ];
    
        $response = Http::asForm()->post($this->url, $params);
        if ($response->successful()) {
            $data = $response->json();
            Log::info("Update course format response", $data);
    
            // قد لا ترجع Moodle معرف القسم الجديد بشكل واضح
            // ممكن نستعمل 'sectionnum' 2 كقيمة افتراضية
            return 2;
        }
    
        Log::error('Failed to create section in Moodle.', ['response' => $response->body()]);
        return null;
    }
    
    
    /**
     * وضع علامة اكتمال على Section/Activity في Moodle
     * تستخدم  core_completion_override_activity_completion_status
     */
    public function markSectionCompletion($moodleUserId, $moodleCourseId, $sectionId)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_completion_override_activity_completion_status',
            'moodlewsrestformat' => 'json',
            'cmid'               => $sectionId,
            'completed'          => 1,
            'userid'             => $moodleUserId,
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }
}
