<?php

namespace App\Services;

use FFMpeg\FFMpeg;

class VideoToAudioService
{
    protected $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = app('ffmpeg');
    }

    public function convert($videoPath, $outputPath)
    {
        try {
            \Log::info('Converting video to audio...');
            $video = $this->ffmpeg->open($videoPath);
            \Log::info('Video opened successfully');
            $video->save(new \FFMpeg\Format\Audio\Mp3(), $outputPath);
            \Log::info('Video converted to audio successfully');
            return $outputPath;


        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return $e->getMessage();
        }

    }
}