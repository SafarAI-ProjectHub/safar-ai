<?php

namespace App\Jobs;

use App\Models\LevelTestQuestion;
use App\Models\CronTracker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class ProcessLevelTestQuestions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $questionId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($questionId)
    {
        $this->questionId = $questionId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $cronTracker = CronTracker::create([
            'command' => 'job->process:level_test_question question_id=' . $this->questionId,
            'status' => 'Running',
            'started_at' => now(),
        ]);

        try {
            $question = LevelTestQuestion::find($this->questionId);

            if (!$question) {
                throw new \Exception("Question not found: " . $this->questionId);
            }

            // Process the question based on its media type
            if ($question->media_type === 'audio') {
                $this->processAudio($question);

                $cronTracker->update([
                    'status' => 'Success',
                    'finished_at' => now(),
                    'duration' => now()->diffInSeconds($cronTracker->started_at),
                ]);

                Log::info("Successfully processed question: " . $this->questionId);
            }
        } catch (\Exception $e) {
            $cronTracker->update([
                'status' => 'Failed',
                'failed_at' => now(),
                'duration' => now()->diffInSeconds($cronTracker->started_at),
                'error' => $e->getMessage(),
            ]);

            Log::error("Failed to process question: " . $e->getMessage());
        }
    }

    private function processAudio(LevelTestQuestion $question)
    {
        // Logic for processing the audio file associated with this question
        Log::info("Processing audio for question: " . $question->id);

        // Transcribe the audio file to text
        $audioPath = public_path($question->media_url);
        $transcription = $this->transcribeAudio($audioPath);
        Log::info('Transcription completed:', ['transcription' => $transcription]);

        // Store the transcription directly as the script
        $question->script = $transcription ?? 'Error: No transcription returned';
        $question->save();
    }

    private function transcribeAudio($audioPath)
    {
        // Transcription logic using Whisper model
        Log::info("Transcribing audio file at path: " . $audioPath);

        // Use a service like OpenAI's Whisper or any other transcription service
        $response = OpenAI::audio()->translate([
            'model' => 'whisper-1',
            'file' => fopen($audioPath, 'r'),
            'language' => 'en',
            'temperature' => 0,
        ], [
            'timeout' => 120 // Increase timeout to 120 seconds
        ]);

        return $response['text'];
    }
}