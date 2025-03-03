<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Laravel\Socialite\Facades\Socialite;
use App\Socialite\MoodleProvider;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // إذا كانت 'app.url' == 'fallback' فهذا يعني أن APP_URL فارغة.
        if (config('app.url') === 'fallback') {
            // إن كنت تريدين في السيرفر استخدام HTTPS دومًا:
            // URL::forceScheme('https');
    
            // التقط الدومين الحالي من الطلب:
            config(['app.url' => url('')]);
        }
        Socialite::extend('moodle', function ($app) {
            $config = $app['config']['services.moodle'];
            return Socialite::buildProvider(MoodleProvider::class, $config);
        });
    }
}