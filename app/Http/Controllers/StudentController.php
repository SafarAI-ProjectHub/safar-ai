<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\UserMeeting;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\LevelTestAssessment;
use App\Models\LevelTest;
use App\Models\LevelTestQuestion;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('Student') && Auth::user()->status === 'pending') {
            $completedLevelTest = LevelTestAssessment::where('user_id', Auth::id())->exists();

            if (!$completedLevelTest) {
                $levelTestQuestions = LevelTestQuestion::with('levelTest')->whereHas('levelTest', function ($query) {
                    $query->where('exam_type', 'student')->where('active', true);
                })->get();
                return view('dashboard.student.level_test', compact('levelTestQuestions'));
            }
        }

        $user = Auth::user();
        $ageGroup = $user->getAgeGroup();

        $category = \DB::table('course_category')
            ->where('age_group', $ageGroup)
            ->first();

        if ($category) {
            $courses = \App\Models\Course::where('level', $user->english_proficiency_level)
                ->where('category_id', $category->id)
                ->get();
        } else {
            $courses = collect(); // Empty collection
        }

        return view('dashboard.student.dashboard', compact('courses'));
    }

    public function myMeetings()
    {
        return view('dashboard.student.meetings');
    }

    public function getMeetings(Request $request)
    {
        try {
            $user = Auth::user();
            $userMeetings = UserMeeting::where('user_id', $user->id)->with(['meeting', 'meeting.user'])->whereHas('meeting', function ($query) {
                $query->where('start_time', '>=', now());
            })
            ->get();

            $dataTable = DataTables::of($userMeetings)
                ->editColumn('meeting.start_time', function ($row) {
                    return \Carbon\Carbon::parse($row->meeting->start_time)->format('d-m-Y / h:i A');
                })
                ->editColumn('meeting.duration', function ($row) {
                    $hours = intdiv($row->meeting->duration, 60);
                    $minutes = $row->meeting->duration % 60;
                    return $hours > 0
                        ? ($hours . ' hour' . ($hours > 1 ? 's' : '') . ($minutes > 0 ? ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : ''))
                        : $row->meeting->duration . ' minute' . ($row->meeting->duration > 1 ? 's' : '');
                })
                ->addColumn('teacher_name', function ($row) {
                    return $row->meeting->user->full_name;
                })
                ->addColumn('join_url', function ($row) {
                    return '<a href="' . $row->meeting->join_url . '" class="btn btn-primary" target="_blank">Join Meeting</a>';
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('student.meetings.show', $row->meeting_id) . '" class="btn btn-info">View Details</a>';
                })
                ->rawColumns(['join_url', 'actions']);

            return $dataTable->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load meetings: ' . $e->getMessage()], 500);
        }
    }

    public function showMeeting($id)
    {
        $userMeeting = UserMeeting::where('user_id', Auth::id())->where('meeting_id', $id)->with(['meeting', 'meeting.user'])->firstOrFail();

        return view('dashboard.student.meeting-details', compact('userMeeting'));
    }

    /* 
    *
    *
    * Level test functions
    *
    *
    */

    public function submit(Request $request)
{
    $user = Auth::user();
    $data = $request->all();
    $assessments = [];
    $openAiRequests = [];
    $audioTranscriptions = [];

    // Map question keys to question IDs
    $questions = LevelTestQuestion::all()->keyBy('id');

    foreach ($data as $key => $value) {
        if (strpos($key, 'question_') !== false) {
            $questionId = explode('_', $key)[1];
            $question = $questions->get($questionId);

            if (!$question) {
                continue; // Skip if question does not exist
            }

            if ($question->question_type === 'choice') {
                $choices = $question->choices;
                $correctChoice = $choices->where('is_correct', true)->first();
                $userChoice = $choices->where('id', $value)->first();
                $correct = $userChoice->is_correct ? 1 : 0;

                $openAiRequests[] = [
                    'question' => $question->question_text,
                    'choices' => $choices->pluck('choice_text')->toArray(),
                    'correct_answer' => $correctChoice->choice_text,
                    'user_answer' => $userChoice->choice_text,
                    'question_id' => $question->id
                ];

                $assessments[] = [
                    'level_test_question_id' => $question->id,
                    'user_id' => $user->id,
                    'response' => $value,
                    'correct' => $correct,
                    'ai_review' => null,
                    'Admin_review' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            } elseif ($question->question_type === 'text') {
                $openAiRequests[] = [
                    'question' => $question->question_text,
                    'notes' => $question->sub_text,
                    'answer' => $value,
                    'question_id' => $question->id
                ];
                $assessments[] = [
                    'level_test_question_id' => $question->id,
                    'user_id' => $user->id,
                    'response' => $value,
                    'correct' => null,
                    'ai_review' => null,
                    'Admin_review' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            } elseif ($question->question_type === 'voice' && $request->hasFile("question_{$questionId}_audio")) {
                // Save audio file
                $path = $request->file("question_{$questionId}_audio")->store('audio_responses');
                $audioTranscriptions[] = [
                    'path' => $path,
                    'question_text' => $question->question_text,
                    'notes' => $question->sub_text,
                    'question_id' => $question->id
                ];
                $assessments[] = [
                    'level_test_question_id' => $question->id,
                    'user_id' => $user->id,
                    'response' => $path,
                    'correct' => null,
                    'ai_review' => null,
                    'Admin_review' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }
    }

    LevelTestAssessment::insert($assessments);

    // Process text and choice data with OpenAI
    if (!empty($openAiRequests)) {
        $textPrompt = $this->generatePrompt($openAiRequests, $user->age);
        $textResponse = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an English proficiency evaluator.'],
                ['role' => 'user', 'content' => $textPrompt]
            ],
            'max_tokens' => 1000
        ]);
        $textAiResults = $textResponse['choices'][0]['message']['content'];
    } else {
        $textAiResults = '';
    }
    dd($textAiResults);

    // Process audio transcriptions and send to OpenAI
    foreach ($audioTranscriptions as $audio) {
        $audioContent = Storage::get($audio['path']);
        $transcription = $this->transcribeAudio($audioContent);

        $openAiRequests[] = [
            'question' => $audio['question_text'],
            'notes' => $audio['notes'],
            'answer' => $transcription,
            'question_id' => $audio['question_id']
        ];
    }

    // Send audio transcriptions to OpenAI
    if (!empty($openAiRequests)) {
        $audioPrompt = $this->generatePrompt($openAiRequests, $user->age);
        $audioResponse = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an English proficiency evaluator.'],
                ['role' => 'user', 'content' => $audioPrompt]
            ],
            'max_tokens' => 1000
        ]);
        $audioAiResults = $audioResponse['choices'][0]['message']['content'];
    } else {
        $audioAiResults = '';
    }

    // Combine text and audio AI results
    $combinedAiResults = $textAiResults . "\n" . $audioAiResults;

    // Process AI results and update assessments
    $this->processAiResults($combinedAiResults, $assessments);

    // Update user's English proficiency level
    $user->student->english_proficiency_level = $this->determineProficiencyLevel($combinedAiResults);
    $user->save();

    return response()->json(['success' => true]);
}


    private function generatePrompt($requests, $age)
    {
        $prompt = "The following are responses from a non-native English speaking student aged $age. Please review and provide feedback:\n\n";
        foreach ($requests as $request) {
            $prompt .= "Question: {$request['question']}\n";
            if (isset($request['choices'])) {
                $prompt .= "Choices: " . implode(', ', $request['choices']) . "\n";
                $prompt .= "Correct Answer: {$request['correct_answer']}\n";
                $prompt .= "Student Answer: {$request['user_answer']}\n\n";
            } else {
                $prompt .= "Teacher Notes: {$request['notes']}\n";
                $prompt .= "Student Answer: {$request['answer']}\n\n";
            }
        }
        return $prompt;
    }

    private function processAiResults($aiResults, $assessments)
    {
        $lines = explode("\n", trim($aiResults));
        foreach ($assessments as $index => $assessment) {
            // Extract AI review and correctness from aiResults (assuming a simple line-based format)
            $lineIndex = $index * 2 + 1; // Adjusting index as per AI response format
            $aiReview = $lines[$lineIndex] ?? '';
            $correctness = strpos($aiReview, 'Correct') !== false;

            LevelTestAssessment::where('id', $assessment['id'])->update([
                'correct' => $correctness,
                'ai_review' => $aiReview
            ]);
        }
    }

    private function determineProficiencyLevel($aiResults)
    {
        $correctAnswers = substr_count($aiResults, 'Correct');
        if ($correctAnswers >= 5) {
            return 6; // Advanced
        } elseif ($correctAnswers >= 4) {
            return 5; // Upper-Intermediate
        } elseif ($correctAnswers >= 3) {
            return 4; // Intermediate
        } elseif ($correctAnswers >= 2) {
            return 3; // Pre-Intermediate
        } elseif ($correctAnswers >= 1) {
            return 2; // Elementary
        } else {
            return 1; // Beginner
        }
    }

    private function transcribeAudio($audioContent)
    {
        $response = OpenAI::transcriptions()->create([
            'model' => 'whisper-1',
            'file' => fopen($audioContent, 'r'),
            'language' => 'en'
        ]);

        return $response['text'];
    }
}
