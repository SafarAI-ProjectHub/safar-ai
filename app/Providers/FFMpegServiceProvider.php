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
       $ffmpegPath = env('FFMPEG_BINARIES', PHP_OS_FAMILY === 'Windows' ? 'C:/ffmpeg/bin/ffmpeg.exe' : '/usr/bin/ffmpeg');
        $ffprobePath = env('FFPROBE_BINARIES', PHP_OS_FAMILY === 'Windows' ? 'C:/ffmpeg/bin/ffprobe.exe' : '/usr/bin/ffprobe');

        $this->app->singleton('ffmpeg', function ($app) use ($ffmpegPath, $ffprobePath) {
            return FFMpeg::create([
                'ffmpeg.binaries' => realpath($ffmpegPath),
                'ffprobe.binaries' => realpath($ffprobePath),
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
