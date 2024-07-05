<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Unit;
use App\Models\CronTracker;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\UploadedFile;
use App\Services\VideoToAudioService;

class ProcessAIScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:script {unit_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process AI script for a unit';

    protected $videoToAudioService;

    public function __construct(VideoToAudioService $videoToAudioService)
    {
        parent::__construct();
        $this->videoToAudioService = $videoToAudioService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $unitId = $this->argument('unit_id');

        $cronTracker = CronTracker::create([
            'command' => 'ai:script unit_id=' . $unitId,
            'status' => 'Running',
            'started_at' => now(),
        ]);

        try {
            $this->processAiScript($unitId);

            $cronTracker->update([
                'status' => 'Success',
                'finished_at' => now(),
                'duration' => now()->diffInSeconds($cronTracker->started_at),
            ]);
        } catch (\Exception $e) {
            $cronTracker->update([
                'status' => 'Failed',
                'failed_at' => now(),
                'duration' => now()->diffInSeconds($cronTracker->started_at),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function processAiScript($unitId)
    {
        $unit = Unit::find($unitId);

        if (!$unit) {
            $this->error("Unit not found.");
            return;
        }

        if ($unit->content_type == 'text') {
            $aiResponse = $this->scriptAi($unit->content, 'text');
            $unit->script = $aiResponse['script'];
        } else if ($unit->content_type == 'video') {
            $videoPath = storage_path('app/public/' . str_replace('storage/', '', $unit->content));
            $audioFilename = uniqid() . '.mp3';
            $audioPath = storage_path('app/public/uploads/' . $audioFilename);

            $this->videoToAudioService->convert($videoPath, $audioPath);

            $transcription = $this->transcribeAudio($audioPath);
            $aiResponse = $this->scriptAi($transcription, 'video');
            $unit->script = $aiResponse['script'];
        }

        $unit->save();
    }

    private function scriptAi($prompt, $type = 'text')
    {
        if ($type == 'text') {
            $text = 'You are an AI assistant tasked with turning the content of a course unit into a detailed script. This script will be used when the user takes a quiz for this unit. Please generate a script based strictly on the provided content. Ensure that the script explains the content thoroughly and comprehensively without adding any new details or elements. The script should clearly describe the original content. Return the response in JSON format with the key "script". Note: the script will not be for the student but for the AI, so explain in a way the AI will understand the subject that the questions and answers revolve around, but also ensure that the content is at the end of the script so the AI will be able to answer the questions and know what answers are wrong and what are correct. For example, if there are names or places, the questions could be about them. Return the JSON script as a key and ensure the value is not multi-line to avoid incorrect JSON. If the content is short and simple, like "hello", return it as it is.';
        } elseif ($type == 'video') {
            $text = 'You are an AI assistant tasked with turning the audio content of a course unit video into a detailed script. This script will be used when the user takes a quiz for this unit. Please generate a script based strictly on the provided audio content. Ensure that the script explains the content thoroughly and comprehensively without adding any new details or elements. The script should clearly describe the original content. Return the response in JSON format with the key "script". Note: the script will not be for the student but for the AI, so explain the subject in a way that the AI will understand the questions and answers. Ensure the content is at the end of the script so the AI can answer the questions correctly. If there are names or places, the questions could be about them. The JSON should have the "script" key, and the value should be a single line to avoid incorrect JSON formatting. If the content is short and simple like "hello", return it as it is. Additionally, note that the text has been extracted from the video, so if some words do not make sense, correct them in the best way based on the context of the video.';
        }

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $text
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0,
        ]);

        $responseContent = $response->choices[0]->message->content;
        $jsonString = $this->extractJsonString($responseContent);

        $aiResponse = json_decode($jsonString, true);
        return $aiResponse;
    }

    private function transcribeAudio($audioPath)
    {
        $extension = pathinfo($audioPath, PATHINFO_EXTENSION);

        if ($extension === 'webm' && is_string($audioPath) && file_exists($audioPath)) {
            $wavPath = str_replace('.webm', '.wav', $audioPath);
            FFMpeg::fromDisk('local')
                ->open($audioPath)
                ->export()
                ->toDisk('local')
                ->inFormat(new \FFMpeg\Format\Audio\Wav)
                ->save($wavPath);

            $audioContent = new UploadedFile($wavPath, basename($wavPath));
        } else {
            $audioContent = new UploadedFile($audioPath, basename($audioPath));
        }

        if ($audioContent instanceof UploadedFile) {
            $response = OpenAI::audio()->translate([
                'model' => 'whisper-1',
                'file' => fopen($audioContent->getRealPath(), 'r'),
                'language' => 'en',
                'temperature' => 0,
            ]);

            return $response['text'];
        } else {
            throw new \Exception("Invalid audio content provided.");
        }
    }

    private function extractJsonString($responseContent)
    {
        if (preg_match('/```json\s*(\{[\s\S]*?\})\s*```/s', $responseContent, $matches)) {
            $jsonString = $matches[1];
            $jsonString = trim($jsonString);
            $jsonString = str_replace(['“', '”', '“”', '""', '""""', '"""', '““', '””'], '', $jsonString);
            if ($this->isValidJson($jsonString)) {
                return $jsonString;
            } else {
                throw new \Exception("Invalid JSON string extracted.");
            }
        } else {
            throw new \Exception("Failed to extract JSON string from response content.");
        }
    }

    private function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}