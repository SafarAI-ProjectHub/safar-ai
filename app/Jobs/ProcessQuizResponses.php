<?php

namespace App\Jobs;

use App\Models\Assessment;
use App\Models\Quiz;
use App\Models\CronTracker;
use App\Models\User;
use App\Models\UserResponse;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use Illuminate\Http\UploadedFile;

class ProcessQuizResponses implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $quizId;

    public function __construct($userId, $quizId)
    {
        $this->userId = $userId;
        $this->quizId = $quizId;
    }

    public function handle()
    {
        $cronTracker = CronTracker::create([
            'command' => 'job->process_quiz_responses user_id=' . $this->userId . ' quiz_id=' . $this->quizId,
            'status' => 'Running',
            'started_at' => now(),
        ]);

        try {
            $this->processResponses();

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

            Log::error('Failed to process quiz responses: ' . $e->getMessage());
        }
    }

    private function processResponses()
    {
        $quiz = Quiz::with('unit')->find($this->quizId);
        $assessment = Assessment::where('quiz_id', $this->quizId)
            ->where('user_id', $this->userId)
            ->first();

        if (!$assessment) {
            return; // If no assessment found, exit the function
        }

        $userResponses = UserResponse::where('assessment_id', $assessment->id)->get();
        $openAiRequests = [];
        $unitScript = $quiz->unit->script;

        foreach ($userResponses as $userResponse) {
            $question = $userResponse->question;
            if ($question->question_type === 'voice') {
                $audioPath = storage_path('app/public/' . str_replace('storage/', '', $userResponse->response));
                $transcription = $this->transcribeAudio($audioPath);

                $openAiRequests[] = [
                    'question' => $question->question_text,
                    'transcription' => $transcription,
                    'question_id' => $question->id,
                    'question_type' => 'voice'
                ];
            } elseif ($question->question_type === 'choice') {
                $correctAnswer = $question->choices->where('is_correct', true)->first()->choice_text;
                $openAiRequests[] = [
                    'question' => $question->question_text,
                    'choices' => $question->choices->pluck('choice_text')->toArray(),
                    'correct_answer' => $correctAnswer,
                    'user_answer' => $userResponse->response,
                    'question_id' => $question->id,
                    'question_type' => 'choice'
                ];
            } else {
                $openAiRequests[] = [
                    'question' => $question->question_text,
                    'response' => $userResponse->response,
                    'question_id' => $question->id,
                    'question_type' => $question->question_type
                ];
            }
        }

        $prompt = $this->generatePrompt($openAiRequests, $unitScript);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    "content" => "You are an AI assistant tasked with evaluating student quiz responses. Evaluate each response based on the provided questions and the script of the unit. Ensure the evaluation is thorough and accurate. Return the results in a JSON format as specified, in a single line even if it is long. Make sure your review of each question is accurate and detailed, addressing the student directly in your review. 

  For each question:
  1. Provide marks for both writing and voice components, even if the answer is incorrect.
  2. For the voice part, ensure the transcription is correct and the student pronounces words correctly.
  3. Offer detailed feedback for writing, focusing on writing skills and text accuracy.
  4. Identify and advise on any mispronunciations or words not related to the unit script.
  5. For multiple-choice questions, explain why the student's answer is incorrect and why the correct answer is correct. 
  6. fro the text quastions provide feedback on the content, grammar, and structure of the response.
  7. Write notes as if you are speaking directly to the student.
  8. Provide a mark out of 100 fro overall performance in the quiz,even if the student has made mistakes and the answer end to be incorrect but the answer if it is related to has to be marked.  
  General feedback:
  - Provide overall feedback in the 'overall_notes' section.
  - Mention any extra marks awarded in the 'overall_mar' for good performance in specific areas.
  Make sure to give the student constructive advice to help them improve both their writing and pronunciation skills."

                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
        ]);
        \Log::info($response);

        $responseContent = $response->choices[0]->message->content;
        $jsonString = $this->extractJsonString($responseContent);
        $aiResponse = json_decode($jsonString, true);

        foreach ($aiResponse['questions'] as $review) {
            $userResponse = UserResponse::where('assessment_id', $assessment->id)
                ->where('question_id', $review['question_id'])
                ->first();

            if ($userResponse) {
                $userResponse->ai_review = $review['ai_review'];
                $userResponse->correct = $review['is_correct'];
                $userResponse->save();
            }
        }

        // Update the assessment's AI mark and notes if needed
        $assessment->ai_assessment = true;
        $assessment->ai_notes = $aiResponse['overall_notes'] ?? null; // Assuming 'overall_notes' is part of the AI response
        $assessment->ai_mark = $aiResponse['overall_mark'] ?? null; // Assuming 'overall_mark' is part of the AI response
        $assessment->save();
    }

    private function generatePrompt($requests, $unitScript)
    {
        $prompt = "Evaluate the following student quiz responses based on the provided unit script. The questions are based on this script. Return the results in a JSON format with the following structure: {\"questions\": [{\"question_id\": 1, \"is_correct\": 1, \"ai_review\": \"The answer is correct.\"}], \"overall_notes\": \"General feedback here\", \"overall_mark\": 85.5}. Ensure the JSON is in a single line even if the response is long.\n\n";
        $prompt .= "Unit Script: $unitScript\n\n";
        foreach ($requests as $request) {
            $prompt .= "Question ID: {$request['question_id']}\n";
            $prompt .= "Question: {$request['question']}\n";

            if ($request['question_type'] === 'voice') {
                $prompt .= "Transcription: {$request['transcription']}\n\n";
            } elseif ($request['question_type'] === 'choice') {
                $prompt .= "Choices: " . implode(', ', $request['choices']) . "\n";
                $prompt .= "Correct Answer: {$request['correct_answer']}\n";
                $prompt .= "User Answer: {$request['user_answer']}\n\n";
            } else {
                $prompt .= "Response: {$request['response']}\n\n";
            }
        }
        \Log::info($prompt);
        return $prompt;
    }

    private function extractJsonString($responseContent)
    {
        $jsonStart = strpos($responseContent, '{');
        $jsonEnd = strrpos($responseContent, '}') + 1;
        $jsonString = substr($responseContent, $jsonStart, $jsonEnd - $jsonStart);
        $jsonString = trim($jsonString, " \t\n\r\0\x0B");
        \Log::info($jsonString);
        return $jsonString;
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
            \Log::info($response);
            return $response['text'];
        }

        throw new \Exception("Invalid audio content provided.");
    }
}