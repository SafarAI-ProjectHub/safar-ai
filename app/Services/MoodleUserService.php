<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Notifications\SyncFailed;

class MoodleUserService
{
    protected $moodleUrl;
    protected $moodleToken;

    public function __construct()
    {
        $this->moodleUrl = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->moodleToken = config('app.moodle_wstoken');
    }

    /**
     * إنشاء مستخدم جديد في Moodle
     */
    public function createUser($user, $roleId = null)
    {
        if ($user->moodle_id) {
            Log::warning("⚠️ المستخدم {$user->email} لديه بالفعل حساب في Moodle.");
            return $user->moodle_id;
        }
    
        // ✅ تحقق مما إذا كان المستخدم موجودًا مسبقًا في Moodle قبل محاولة إنشائه
        $existingUser = $this->getUserByEmail($user->email);
        if ($existingUser) {
            Log::info("✅ المستخدم {$user->email} موجود مسبقًا في Moodle برقم معرف: {$existingUser}");
            return $existingUser;
        }
    
        $email = strtolower(trim($user->email));
        $username = str_replace('@safarai.com', '', $email);
        $roleId = $roleId ?? $this->getMoodleRoleId($user);
        $password = 'DefaultPass123!';
    
        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_user_create_users',
            'moodlewsrestformat' => 'json',
            'users'              => [[
                'username'        => $username,
                'password'        => $password,
                'firstname'       => trim($user->first_name),
                'lastname'        => trim($user->last_name),
                'email'           => $email,
                'auth'            => 'manual',
                'country'         => 'JO',
                'city'            => 'Amman',
                'phone1'          => $user->phone_number,
                'preferences'     => [['type' => 'auth_forcepasswordchange', 'value' => 0]]
            ]]
        ];
    
        Log::info('🔍 طلب إنشاء مستخدم في Moodle:', ['request' => json_encode($postData)]);
        $responseData = $this->retryRequest($postData);
    
        if (!$responseData || isset($responseData['exception'])) {
            $this->sendSyncFailureNotification($user, "فشل تسجيل المستخدم في Moodle.");
            return null;
        }
    
        if (!empty($responseData) && isset($responseData[0]['id'])) {
            $moodleUserId = $responseData[0]['id'];
            $this->assignRole($moodleUserId, $roleId);
            return $moodleUserId;
        }
    
        $this->sendSyncFailureNotification($user, "لم يتم تسجيل المستخدم في Moodle.");
        return null;
    }
    

    /**
     * تحديث بيانات المستخدم في Moodle
     */
    public function updateUser($user)
    {
        if (!$user->moodle_id) {
            Log::warning("⚠️ لا يمكن تحديث المستخدم {$user->email} لأنه غير موجود في Moodle.");
            return false;
        }

        $profileImageUrl = $user->profile_image ? asset('storage/' . $user->profile_image) : '';

        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_user_update_users',
            'moodlewsrestformat' => 'json',
            'users'              => [[
                'id'              => $user->moodle_id,
                'username'        => strtolower(trim($user->email)),
                'firstname'       => trim($user->first_name),
                'lastname'        => trim($user->last_name),
                'email'           => strtolower(trim($user->email)),
                'auth'            => 'manual',
                'country'         => 'JO',
                'city'            => 'Amman',
                'phone1'          => $user->phone_number,
            ]]
        ];

        Log::info('🔍 تحديث بيانات المستخدم في Moodle:', ['request' => json_encode($postData)]);
        return $this->retryRequest($postData) && !isset($responseData['exception']);
    }

    /**
     * تحديث كلمة المرور في Moodle
     */
    public function updatePassword($user, $newPassword)
    {
        if (!$user->moodle_id || !$this->checkUserExists($user->moodle_id)) {
            Log::warning("⚠️ لا يمكن تحديث كلمة المرور للمستخدم {$user->email} لأنه غير موجود في Moodle.");
            return false;
        }

        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_user_update_users',
            'moodlewsrestformat' => 'json',
            'users'              => [[
                'id'       => $user->moodle_id,
                'password' => $newPassword
            ]]
        ];

        Log::info('🔍 تحديث كلمة المرور في Moodle:', ['request' => json_encode($postData)]);
        return $this->retryRequest($postData) && !isset($responseData['exception']);
    }

    /**
     * حذف المستخدم من Moodle
     */
    public function deleteUser($moodleUserId)
    {
        if (!$moodleUserId || !$this->checkUserExists($moodleUserId)) {
            Log::warning("⚠️ لا يمكن حذف مستخدم غير موجود في Moodle.");
            return false;
        }

        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_user_delete_users',
            'moodlewsrestformat' => 'json',
            'userids'            => [$moodleUserId]
        ];

        Log::info('🔍 حذف المستخدم من Moodle:', ['request' => json_encode($postData)]);
        return $this->retryRequest($postData) && !isset($responseData['exception']);
    }

    /**
     * إعادة المحاولة عند فشل الطلب
     */
   

    /**
     * التحقق مما إذا كان المستخدم موجودًا في Moodle
     */
    public function checkUserExists($moodleUserId)
    {
        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_user_get_users_by_field',
            'moodlewsrestformat' => 'json',
            'field'              => 'id',
            'values'             => [$moodleUserId]
        ];

        $response = Http::asForm()->post($this->moodleUrl, $postData)->json();
        return !empty($response);
    }

   
    

/**
 * جلب معرف الدور في Moodle بناءً على دور المستخدم في Laravel
 */
public function getMoodleRoleId($user)
{
    return match (true) {
        $user->hasRole('Admin')   => 1,  // Moodle Admin
        $user->hasRole('Teacher') => 3,  // Moodle Teacher
        default                   => 2,  // Moodle Student
    };
}



    public function logUserActivity($moodleUserId, $action)
{
    if (!$moodleUserId) {
        Log::warning("⚠️ لا يمكن تسجيل نشاط لمستخدم غير موجود في Moodle.");
        return false;
    }

    $postData = [
        'wstoken'            => $this->moodleToken,
        'wsfunction'         => 'core_user_add_user_private_files',
        'moodlewsrestformat' => 'json',
        'userid'             => $moodleUserId,
        'component'          => 'user',
        'filearea'           => 'private',
        'filepath'           => '/',
        'filename'           => "activity_{$action}.txt",
        'filecontent'        => now()->toDateTimeString() . " - User {$action}",
    ];

    Log::info("📌 تسجيل نشاط {$action} للمستخدم Moodle ID: {$moodleUserId}");

    $response = Http::asForm()->post($this->moodleUrl, $postData);
    $responseData = $response->json();

    Log::info('🔍 استجابة Moodle عند تسجيل النشاط:', ['response' => json_encode($responseData)]);

    return !isset($responseData['exception']);
}


 /**
     * إرسال إشعار عند فشل التزامن
     */
    protected function sendSyncFailureNotification($user, $message)
    {
        if ($user->id == 1) { // إرسال الإشعار إلى المدير فقط (مثال)
            User::find(1)->notify(new SyncFailed("❌ فشل تزامن المستخدم {$user->email}: $message"));
        }
    }
    public function assignRole($moodleUserId, $roleId)
    {
        if (!$moodleUserId || !$roleId) {
            Log::warning("⚠️ تعيين الدور فشل: بيانات المستخدم أو الدور غير صحيحة.");
            return false;
        }
    
        // تحديد `contextid` المناسب لكل دور
        $contextId = match ($roleId) {
            1 => 1,  // Admin على مستوى النظام
            3 => 50, // Teacher على مستوى الدورة
            2 => 50, // Student على مستوى الدورة
            default => 1,
        };
    
        $postData = [
            'wstoken'            => $this->moodleToken,
            'wsfunction'         => 'core_role_assign_roles',
            'moodlewsrestformat' => 'json',
            'assignments'        => [[
                'roleid'    => $roleId,
                'userid'    => $moodleUserId,
                'contextid' => $contextId
            ]]
        ];
    
        Log::info('🔍 تعيين دور المستخدم في Moodle:', ['request' => json_encode($postData)]);
        
        // ✅ هنا المشكلة: تأكد من إرسال `$postData` فقط وليس `$this->moodleUrl`
        $responseData = $this->retryRequest($postData);
    
        if ($responseData && !isset($responseData['exception'])) {
            return true;
        }
    
        Log::error("❌ فشل تعيين الدور {$roleId} للمستخدم Moodle ID: {$moodleUserId}");
        return false;
    }
    
/**
 * إعادة المحاولة عند فشل الطلب
 */
protected function retryRequest($data, $maxAttempts = 3)
{
    $attempt = 0;
    while ($attempt < $maxAttempts) {
        // ✅ تأكد من تمرير البيانات فقط وليس URL
        $response = Http::asForm()->post($this->moodleUrl, $data);
        $responseData = $response->json();

        if (!isset($responseData['exception'])) {
            return $responseData;
        }

        Log::error("❌ Moodle خطأ عند تنفيذ الطلب:", ['error' => $responseData]);

        $attempt++;
        sleep(2);
    }

    Log::error("❌ فشل الطلب إلى Moodle بعد {$maxAttempts} محاولات.", ['data' => $data]);
    return null;
}

}
