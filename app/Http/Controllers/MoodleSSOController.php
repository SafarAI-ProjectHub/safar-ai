<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Firebase\JWT\JWT;

class MoodleSSOController extends Controller
{
    /**
     * إعادة التوجيه إلى Moodle لبدء عملية المصادقة.
     */
    public function redirectToMoodle()
    {
        // هنا نفترض أن هناك Driver مُعرف باسم "moodle" في Socialite
        // إذا لم يكن موجودًا، يمكنك إنشاء موفر مخصص أو استخدام driver عام.
        return Socialite::driver('moodle')->redirect();
    }

    /**
     * استقبال رد المصادقة من Moodle ومعالجة بيانات المستخدم.
     */
    public function handleMoodleCallback(Request $request)
    {
        try {
            // استرجاع بيانات المستخدم من Moodle بعد المصادقة
            $moodleUser = Socialite::driver('moodle')->user();
        } catch (\Exception $e) {
            // في حالة وجود خطأ في المصادقة، إعادة التوجيه مع رسالة خطأ
            return redirect('/login')->withErrors('Authentication failed.');
        }

        // البحث عن المستخدم في قاعدة بيانات Laravel أو إنشاؤه إذا لم يكن موجودًا
        $user = User::firstOrCreate(
            ['email' => $moodleUser->getEmail()],
            [
                'name' => $moodleUser->getName(),
                // يمكنك إضافة حقول إضافية إذا لزم الأمر
            ]
        );

        // تسجيل دخول المستخدم في Laravel
        auth()->login($user);

        // توليد JWT للتكامل مع Moodle
        $jwtToken = $this->createJWTForUser($user);

        // إنشاء رابط SSO لمودل
        // نفترض هنا أن Moodle يستقبل التوكن على رابط مخصص مثل: /local/sso/login.php
        $ssoLink = env('MOODLE_BASE_URL') . "/local/sso/login.php?token=" . $jwtToken;

        // إعادة توجيه المستخدم إلى Moodle باستخدام الرابط الذي يحتوي على التوكن
        return redirect($ssoLink);
    }

    /**
     * توليد JWT لمستخدم Laravel.
     */
    protected function createJWTForUser($user)
    {
        // إعداد الحمولة (payload) التي سيتم تضمينها في التوكن
        $payload = [
            'sub'   => $user->id,
            'email' => $user->email,
            'name'  => $user->name,
            'iat'   => time(),
            'exp'   => time() + 3600, // صلاحية التوكن ساعة واحدة
        ];

        // استخدم مفتاحًا سريًا، يمكنك استخدام APP_KEY أو مفتاح مخصص آخر
        $jwtSecret = env('APP_KEY');
        return JWT::encode($payload, $jwtSecret, 'HS256');
    }
}
