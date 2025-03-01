<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
    }
}