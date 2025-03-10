<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;

use App\Services\MoodleServices\CourseCategoryService;
use App\Services\MoodleServices\CoursesService;

class MoodleService
{
    protected $url;
    protected $token;

    protected $courseCategoryService;
    protected $coursesService;

    /**
     * تحميل قيم URL و TOKEN من env + استقبال الخدمات المرتبطة
     */
    public function __construct(
        CourseCategoryService $courseCategoryService,
        CoursesService $coursesService
    ) {
        $this->url   = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->token = config('app.moodle_wstoken');

        $this->courseCategoryService = $courseCategoryService;
        $this->coursesService        = $coursesService;
    }

    /*
     |---------------------------------
     | تعامل مع تصنيفات الكورسات (Categories)
     |---------------------------------
     */
    public function createCategory(array $data)
    {
        return $this->courseCategoryService->createCategory($data);
    }

    public function updateCategory($category, array $data)
    {
        return $this->courseCategoryService->updateCategory($category, $data);
    }

    public function deleteCategory($category)
    {
        return $this->courseCategoryService->deleteCategory($category);
    }

    public function syncCategoriesFromMoodle()
    {
        return $this->courseCategoryService->syncCategoriesFromMoodle();
    }

    /*
     |---------------------------------
     | تعامل مع الكورسات (Courses)
     |---------------------------------
     */
    // إنشاء دورة جديدة في Moodle
    public function createCourseInMoodle(Course $course)
    {
        return $this->coursesService->createCourse($course);
    }

    // تحديث دورة في Moodle
    public function updateCourseInMoodle(Course $course)
    {
        return $this->coursesService->updateCourse($course);
    }

    // حذف دورة من Moodle
    public function deleteCourseInMoodle($moodleCourseId)
    {
        return $this->coursesService->deleteCourse($moodleCourseId);
    }

    /**
     * جلب الكورسات من Moodle
     */
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
     * مزامنة الكورسات من Moodle إلى Laravel
     */
    public function syncCoursesFromMoodle()
    {
        $moodleCourses = $this->getCourses();

        // أحيانًا Moodle يعيد مصفوفة فيها "exception" عند الخطأ، تأكّد أنّها ليست خطأ.
        if (!is_array($moodleCourses)) {
            Log::error("syncCoursesFromMoodle: Unexpected response from Moodle", ['response' => $moodleCourses]);
            return;
        }

        // نمرّ على كل كورس جلبناه من Moodle:
        foreach ($moodleCourses as $mcourse) {
            // أحيانًا يكون الـid = 1 هو الصفحة الرئيسية في Moodle فتجاهلها (حسب الضبط)
            if (isset($mcourse['id']) && $mcourse['id'] == 1) {
                continue;
            }

            // ابحث إن كان موجودًا في قاعدة بيانات Laravel:
            $localCourse = Course::where('moodle_course_id', $mcourse['id'])->first();

            if (!$localCourse) {
                // غير موجود -> أنشئه
                $localCourse = new Course();
                $localCourse->moodle_course_id = $mcourse['id'];
            }

            // حدّث البيانات
            $localCourse->title       = $mcourse['fullname'] ?? 'No Title';
            $localCourse->description = $mcourse['summary'] ?? '';
            // يمكنك وضع قيم إضافية حسب حاجتك
            $localCourse->save();
        }

        Log::info("syncCoursesFromMoodle: Successfully synced courses from Moodle.");
    }

    /*
     |---------------------------------
     | تعامل مع الكويزات/أنشطة H5P كمثال
     |---------------------------------
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

    /*
     |---------------------------------
     | تعامل مع تسجيل الطلاب في Moodle
     |---------------------------------
     */
    public function enrollStudentInMoodle(User $user, Course $course)
    {
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'enrol_manual_enrol_users',
            'moodlewsrestformat' => 'json',
            'enrolments' => [[
                'roleid'   => 5, // Student role
                'userid'   => $user->moodle_id,
                'courseid' => $course->moodle_course_id,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        return $response->json();
    }

    /*
     |---------------------------------
     | تعامل مع المستخدمين
     |---------------------------------
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
                return $data[0]['id']; // moodle_user_id
            }
        }

        Log::error('Failed to create user in Moodle.', ['response' => $response->body()]);
        return null;
    }

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

    /*
     |---------------------------------
     | أمثلة أخرى: إنشاء Section للوحدة (Unit)
     |---------------------------------
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
            return 2; // كمثال نفترض أنّ القسم #2
        }

        Log::error('Failed to create section in Moodle.', ['response' => $response->body()]);
        return null;
    }

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
