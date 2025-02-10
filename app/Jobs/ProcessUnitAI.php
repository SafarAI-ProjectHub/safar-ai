<?php

namespace App\Jobs;

use App\Models\Unit;
use App\Models\CronTracker;
use Illuminate\Http\UploadedFile;
use OpenAI\Laravel\Facades\OpenAI;
use App\Services\VideoToAudioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use FFMpeg\FFMpeg;

class ProcessUnitAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $unitId;
    protected $videoToAudioService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($unitId, VideoToAudioService $videoToAudioService)
    {
        $this->unitId = $unitId;
        $this->videoToAudioService = $videoToAudioService;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cronTracker = CronTracker::create([
            'command' => 'job->ai:script unit_id=' . $this->unitId,
            'status' => 'Running',
            'started_at' => now(),
        ]);

        try {
            $this->processAiScript($this->unitId);

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

            Log::error('Failed to process AI script: ' . $e->getMessage());
        }
    }

    public function processAiScript($unitId)
    {
        $unit = Unit::find($unitId);
        \Log::info("unit: " . json_encode($unit));

        if (!$unit) {
            throw new \Exception("Unit not found.");
        }
        \Log::info("unit: " . json_encode($unit));

        $unit->script = 'running...';
        $unit->save();
        \Log::info("unit: " . json_encode($unit));
        if ($unit->content_type == 'text') {
            $aiResponse = $this->scriptAi($unit->content, 'text');
            $unit->script = $aiResponse['script'] ?? 'Error: No script returned';
        } elseif ($unit->content_type == 'video') {
            $unit->script = 'video Running...';

            $unit->save();
            $videoPath = storage_path('app/public/' . str_replace('storage/', '', $unit->content));
            $audioFilename = uniqid() . '.mp3';
            $audioPath = storage_path('app/public/uploads/' . $audioFilename);

            Log::info('Converting video to audio:', ['videoPath' => $videoPath, 'audioPath' => $audioPath]);

            $this->videoToAudioService->convert($videoPath, $audioPath);

            Log::info('Audio conversion completed.');

            $transcription = $this->transcribeAudio($audioPath);
            Log::info('Transcription completed:', ['transcription' => $transcription]);

            $aiResponse = $this->scriptAi($transcription, 'video');
            $unit->script = $aiResponse['script'] ?? 'Error: No script returned';
        } elseif ($unit->content_type == 'youtube') {
            $unit->script = 'https://www.youtube.com/watch?v=' . $unit->content;
        }

        $unit->save();
    }


    private function scriptAi($prompt, $type = 'text')
    {
        if ($type == 'text') {
            $text = 'You are an AI assistant tasked with turning the content of a course unit into a detailed script. This script will be used when the user takes a quiz for this unit. Please generate a script based strictly on the provided content. Ensure that the script explains the content thoroughly and comprehensively without adding any new details or elements. The script should clearly describe the original  content. Return the response in JSON format with the key "script". note: the script will not be for the Student it will be for the ai so just explain in a way the ai will understand the subject that the quastions and answerss revlove around but also insure that the content will be at the end of the script so the ai will be able to answer the questions and know what answers are wrong and what are corect fro example if there names or place could the quastio by about them and return in teh json script as a keyand even if the contetn is short and simple as hello  and do not need explanation then just removeing the html tags and return it as it is   and the value should not be multi line keep it on the same line to not get wrong json .';
        } elseif ($type == 'video') {
            // unit type is vedio and we need to tell that to the ai that the text is taken from the vedio audio
            $text = 'You are an AI assistant tasked with turning the audio content of a course unit video into a detailed script. This script will be used when the user takes a quiz for this unit. Please generate a script based strictly on the provided audio content. Ensure that the script explains the content thoroughly and comprehensively without adding any new details or elements. The script should clearly describe the original content. Return the response in JSON format with the key "script". Note that the script will not be for the student, but for the AI, so explain the subject in a way that the AI will understand the questions and answers. Ensure the content is at the end of the script so the AI can answer the questions correctly. If there are names or places, the questions could be about them. The JSON should have the "script" key, and the value should be a single line to avoid incorrect JSON formatting. If the content is short and simple like "hello", return it as it is. Additionally, note that the text has been extracted from the video, so if some words do not make sense, correct them in the best way based on the context of the video.';

        }
        // dd('test');
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $text
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0,
        ]);
        // dd($response);
        \Log::info("response: ai-text-unit::::::::::::::: " . json_encode($response));

        // Extract and clean the JSON part of the response
        $responseContent = $response->choices[0]->message->content;
        $jsonString = $this->extractJsonString($responseContent);

        $aiResponse = json_decode($jsonString, true);
        \Log::info("aiResponse: " . json_encode($aiResponse));

        return $aiResponse;
    }
    private function extractJsonString($responseContent)
    {
        // Try decoding JSON directly
        $decodedJson = json_decode($responseContent, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            \Log::info("Direct JSON decoding successful.");
            return $responseContent;
        }
    
        // Step 2: Extract JSON enclosed in backticks or braces
        if (
            preg_match('/```json\s*(\{[\s\S]*?\})\s*```/s', $responseContent, $matches) ||
            preg_match('/(\{[\s\S]*\})/', $responseContent, $matches)
        ) {
            // Clean the JSON string
            $jsonString = trim($matches[1]);
            $jsonString = str_replace(['“', '”', '‘', '’'], '"', $jsonString); // Replace non-standard quotes
            $jsonString = preg_replace('/[\r\n]+/', ' ', $jsonString); // Remove line breaks
    
            \Log::info("Extracted JSON string before validation: " . $jsonString);
    
            // Validate cleaned JSON
            $decodedJson = json_decode($jsonString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                \Log::info("JSON extraction and validation successful.");
                return $jsonString;
            } else {
                \Log::error("Invalid JSON after extraction. Error: " . json_last_error_msg());
            }
        } else {
            \Log::error("Failed to locate JSON pattern in response content.");
        }
    
        // If all attempts fail, log the issue and return null
        \Log::error("Final JSON extraction failure from response: " . $responseContent);
        return null;
    }
    

    private function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function transcribeAudio($audioPath)
    {
        \Log::info("inside transcribeAudio");
        \Log::info("Transcribing audio file at path: " . $audioPath);
        $extension = pathinfo($audioPath, PATHINFO_EXTENSION);

        if ($extension === 'webm' && is_string($audioPath) && file_exists($audioPath)) {
            // Convert the webm file to wav format using laravel-ffmpeg
            $wavPath = str_replace('.webm', '.wav', $audioPath);
            FFMpeg::fromDisk('local')
                ->open($audioPath)
                ->export()
                ->toDisk('local')
                ->inFormat(new \FFMpeg\Format\Audio\Wav)
                ->save($wavPath);

            \Log::info("Converted audio file path: " . $wavPath);

            // Use the wav file for transcription
            $audioContent = new UploadedFile($wavPath, basename($wavPath));
        } else {
            $audioContent = new UploadedFile($audioPath, basename($audioPath));
        }

        // Ensure $audioContent is an instance of UploadedFile
        if ($audioContent instanceof UploadedFile) {
            \Log::info("File details - MimeType: " . $audioContent->getMimeType() . ", Size: " . $audioContent->getSize() . ", Original Name: " . $audioContent->getClientOriginalName() . ", Extension: " . $audioContent->getClientOriginalExtension());

            // Check if the file type is supported by the transcription service

            $response = OpenAI::audio()->translate([
                'model' => 'whisper-1',
                'file' => fopen($audioContent->getRealPath(), 'r'),
                'language' => 'en',
                'temperature' => 0, // Set temperature to 0 for deterministic output
            ]);

            \Log::info("Transcription response: " . json_encode($response));
            return $response['text'];
        } else {
            \Log::info("fail on line 338: ");
        }
    }

}