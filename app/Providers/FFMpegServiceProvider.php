<?php

namespace App\Providers;

use FFMpeg\FFMpeg;
use Illuminate\Support\ServiceProvider;

class FFMpegServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ffmpeg', function ($app) {
            return FFMpeg::create([
                'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout' => 3600,
                'ffmpeg.threads' => 12,
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}