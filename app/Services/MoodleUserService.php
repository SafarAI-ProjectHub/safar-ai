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
        // جلب بيانات Moodle من ملف الـconfig
        $this->moodleUrl   = config('services.moodle.base_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->moodleToken = config('services.moodle.token');
    }

    /**
     * إنشاء مستخدم جديد في Moodle
     *
     * @param  \App\Models\User  $user
     * @param  int|null          $roleId   (يمكن تمريها يدويًا، وإلا ستُحدد تلقائيًا حسب دور المستخدم)
     * @return int|null          $moodleUserId أو null
     */
    public function createUser($user, $roleId = null)
    {
        // تحويل الإيميل لحروف صغيرة
        $email = strtolower(trim($user->email));

        // إزالة الدومين من اليوزرنيم (مثلاً @safarai.com) لمنع رفضه في Moodle (اختياري)
        $username = str_replace('@safarai.com', '', $email);

        // تحديد الدور في Moodle لو لم يُرسل
        if (!$roleId) {
            $roleId = $this->getMoodleRoleId($user);
        }

        // تجهيز بيانات الإرسال لإنشاء المستخدم
        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_user_create_users',
            'moodlewsrestformat' => 'json',
            'users'              => [
                [
                    // يمكن تعديل الـusername لو عندك شرط محدد
                    'username'  => $username,
                    'password'  => 'P@ssw0rd123!', // أو أي باسورد مناسب لسياسة Moodle
                    'firstname' => trim($user->first_name),
                    'lastname'  => trim($user->last_name),
                    'email'     => $email,
                    'auth'      => 'manual',
                    'country'   => 'JO',      // كود الدولة (جوردن كمثال)
                    'city'      => 'Amman',   // حقل city أحيانًا يكون مطلوب في Moodle
                    'preferences' => [
                        [
                            'type'  => 'auth_forcepasswordchange',
                            'value' => 0
                        ]
                    ]
                ]
            ]
        ];

        Log::info('🔍 طلب الإرسال إلى Moodle:', ['request' => json_encode($postData)]);

        $response = Http::asForm()->post($this->moodleUrl, $postData);
        $responseData = $response->json();

        Log::info('🔍 استجابة Moodle:', ['response' => json_encode($responseData)]);

        // فشل في إنشاء المستخدم
        if (isset($responseData['exception'])) {
            Log::warning('⚠️ لم يتم تسجيل المستخدم في Moodle، تحقق من الاستجابة:', ['response' => $responseData]);
            return null;
        }

        // نجاح العملية
        if (!empty($responseData) && isset($responseData[0]['id'])) {
            $moodleUserId = $responseData[0]['id'];

            // تعيين الدور
            $this->assignRole($moodleUserId, $roleId);

            return $moodleUserId;
        }

        // فشل لأي سبب آخر
        Log::warning('⚠️ لم يتم تسجيل المستخدم في Moodle، تحقق من الاستجابة:', ['response' => $responseData]);
        return null;
    }

    /**
     * تعيين دور للمستخدم في Moodle
     */
    public function assignRole($moodleUserId, $roleId)
    {
        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_role_assign_roles',
            'moodlewsrestformat' => 'json',
            'assignments'        => [
                [
                    'roleid'    => $roleId,
                    'userid'    => $moodleUserId,
                    'contextid' => 1 // عادةً 1 يكون للسستم ككل. تأكد أنك تملك صلاحية تعيين الدور على Level النظام.
                ]
            ]
        ];

        Log::info('🔍 تعيين دور للمستخدم في Moodle:', ['request' => json_encode($postData)]);

        $response = Http::asForm()->post($this->moodleUrl, $postData);
        $responseData = $response->json();

        Log::info('🔍 استجابة Moodle عند تعيين الدور:', ['response' => json_encode($responseData)]);

        if (isset($responseData['exception'])) {
            Log::warning('⚠️ فشل تعيين الدور للمستخدم في Moodle:', ['response' => $responseData]);
            return false;
        }

        return true;
    }

    /**
     * جلب الـRoleId حسب دور المستخدم في Laravel
     */
    protected function getMoodleRoleId($user)
    {
        
        if ($user->hasRole('Admin')) {
            return 1; // roleid للأدمن
        } elseif ($user->hasRole('Teacher')) {
            return 3; // roleid للمعلم
        } else {
            return 2; // roleid للطالب
        }
    }
}
