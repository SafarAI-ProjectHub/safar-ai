<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class MoodleProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * يستخدم Moodle فاصل المسافات للفواصل بين الصلاحيات.
     */
    protected $scopeSeparator = ' ';

    /**
     * استرجاع قيمة من التكوين.
     */
    protected function getConfig($key)
    {
        return $this->config[$key] ?? null;
    }

    /**
     * إعداد رابط المصادقة.
     */
    protected function getAuthUrl($state)
    {
        // نفترض أن Moodle يوفر رابط OAuth على /login/oauth2/authorize
        return $this->buildAuthUrlFromBase($this->getBaseUrl().'/login/oauth2/authorize', $state);
    }

    /**
     * إعداد رابط الحصول على التوكن.
     */
    protected function getTokenUrl()
    {
        return $this->getBaseUrl().'/login/oauth2/token';
    }

    /**
     * إرجاع الـ Base URL الخاص بـ Moodle (يأخذ من إعدادات config/services.php).
     */
    protected function getBaseUrl()
    {
        return rtrim($this->getConfig('base_url'), '/');
    }

    /**
     * استرجاع بيانات المستخدم باستخدام التوكن.
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getBaseUrl().'/login/oauth2/userinfo', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * تحويل بيانات المستخدم إلى كائن User.
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'] ?? null,
            'nickname' => null,
            'name'     => $user['name'] ?? null,
            'email'    => $user['email'] ?? null,
            'avatar'   => $user['picture'] ?? null,
        ]);
    }
    
    /**
     * إعداد حقول الطلب للحصول على التوكن.
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}
