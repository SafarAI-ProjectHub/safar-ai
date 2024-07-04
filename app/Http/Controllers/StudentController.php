<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Support\Facades\DB;
use App\Models\UserMeeting;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\LevelTestAssessment;
use App\Models\LevelTest;
use Illuminate\Http\UploadedFile;
use App\Models\UserSubscription;
use Carbon\Carbon;
use App\Models\LevelTestQuestion;
use OpenAI\Laravel\Facades\OpenAI;
use App\Models\CourseStudent;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use App\Models\User;
use App\Models\Teacher;
use Spatie\Permission\Models\Role;
use App\Models\LevelTestChoice;


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

        // if (Auth::user()->status == 'pending') {
        //     Auth::user()->status = 'active';
        //     Auth::user()->save();
        // }


        $user = Auth::user();

        // Calculate the user's age
        $dateOfBirth = $user->date_of_birth;
        $age = Carbon::parse($dateOfBirth)->age;

        // Determine the age group
        if ($age >= 6 && $age < 10) {
            $ageGroup = '6-10';
        } elseif ($age >= 10 && $age < 14) {
            $ageGroup = '10-14';
        } elseif ($age >= 14 && $age < 18) {
            $ageGroup = '14-18';
        } else {
            $ageGroup = '18+';
        }

        $category = DB::table('course_categories')
            ->where('age_group', $ageGroup)
            ->first();

        if ($category) {
            $courses = Course::where('level', $user->student->english_proficiency_level)
                ->where('category_id', $category->id)
                ->get();
        } else {
            $courses = collect();
        }
        // $courses = Course::all();
        $subscription = UserSubscription::where('user_id', Auth::id())->first();
        $enrolledCourseIds = Auth::user()->courses->pluck('id')->toArray();
        $planDetails = \App\Models\Subscription::where('is_active', 1)->first();
        return view('dashboard.student.dashboard', compact('courses', 'planDetails', 'subscription', 'enrolledCourseIds'));
    }

    public function levelTest()
    {
        $completedLevelTest = LevelTestAssessment::where('user_id', Auth::id())->exists();

        if (!$completedLevelTest) {
            $levelTestQuestions = LevelTestQuestion::with('levelTest')->whereHas('levelTest', function ($query) {
                $query->where('exam_type', 'student')->where('active', true);
            })->get();
            return view('dashboard.student.level_test', compact('levelTestQuestions'));
        }
    }

    public function getCourseDetails(Request $request)
    {
        $courseId = $request->input('course_id');
        $course = Course::find($courseId);

        if ($course) {
            return response()->json([
                'course' => [
                    'title' => $course->title,
                    'description' => $course->description,
                    'teacher_name' => $course->teacher ? optional($course->teacher->user)->full_name : 'N/A',
                    'years_of_experience' => $course->teacher ? $course->teacher->years_of_experience : 'N/A',
                ]
            ]);
        }

        return response()->json([
            'error' => 'Course not found.'
        ], 404);
    }

    public function enroll(Request $request)
    {
        $courseId = $request->input('course_id');
        $userId = Auth::id();

        // Check if the user is already enrolled
        if (CourseStudent::where('course_id', $courseId)->where('student_id', $userId)->exists()) {
            return response()->json([
                'error' => 'You are already enrolled in this course.'
            ], 400);
        }

        // Enroll the user in the course
        CourseStudent::create([
            'course_id' => $courseId,
            'student_id' => $userId,
            'enrollment_date' => now(),
            'progress' => 0,
        ]);

        return response()->json([
            'success' => 'You have been enrolled in the course successfully.'
        ]);
    }



    public function myCourses()
    {
        $courses = Auth::user()->courses;
        return view('dashboard.student.myCourses', compact('courses'));
    }

    /*
     *
     *
     *   meeting section    
     *
     *
     */


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
            })->get();

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
     *   level test section   
     *
     */
    public function submit(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $assessments = [];
        $openAiRequests = [];
        $audioTranscriptions = [];
        \Log::info("before foreach data" . json_encode($data));

        // Map question keys to question IDs
        $questions = LevelTestQuestion::with('choices')->get()->keyBy('id');

        foreach ($data as $key => $value) {
            if (strpos($key, 'question_') !== false) {
                $questionId = explode('_', $key)[1];
                $question = $questions->get($questionId);

                if (!$question) {
                    continue; // Skip if question does not exist
                }

                $assessment = new LevelTestAssessment();
                $assessment->level_test_question_id = $questionId;
                $assessment->user_id = $user->id;

                if ($question->question_type === 'choice') {
                    $choice = LevelTestChoice::find($value);
                    $correctAnswer = $question->choices->where('is_correct', true)->first();
                    $assessment->response = $choice->choice_text;
                    $openAiRequests[] = [
                        'question' => $question->question_text,
                        'choices' => $question->choices->pluck('choice_text')->toArray(),
                        'correct_answer' => $correctAnswer ? $correctAnswer->choice_text : null,
                        'user_answer' => $choice->choice_text,
                        'question_id' => $question->id,
                        'question_type' => 'choice'
                    ];
                } elseif ($question->question_type === 'text') {
                    $assessment->response = $value;
                    $openAiRequests[] = [
                        'question' => $question->question_text,
                        'sub_text' => $question->sub_text,
                        'user_answer' => $value,
                        'question_id' => $question->id,
                        'question_type' => 'text'
                    ];
                } elseif ($question->question_type === 'voice') {
                    $customFileName = sha1($value->getClientOriginalName()) . '.wav';
                    $path = $value->storeAs('audio_responses', $customFileName);
                    $transcription = $this->transcribeAudio(storage_path('app/public/' . $path));
                    $assessment->response = $path;
                    $audioTranscriptions[$questionId] = $transcription;
                    $openAiRequests[] = [
                        'question' => $question->question_text,
                        'transcription' => $transcription,
                        'question_id' => $question->id,
                        'question_type' => 'voice'
                    ];
                }

                $assessment->correct = false; // Needs manual or AI review later
                $assessment->save();
                $assessments[] = $assessment;
            }
        }

        // Send data to OpenAI for review
        $aiResponse = $this->reviewWithAI($openAiRequests);

        \Log::info("AI response: " . json_encode($aiResponse));

        // Update assessments with AI response
        foreach ($aiResponse['questions'] as $review) {
            $assessment = LevelTestAssessment::where('level_test_question_id', $review['question_id'])
                ->where('user_id', $user->id)
                ->first();

            if ($assessment) {
                $assessment->ai_review = $review['ai_review'];
                $assessment->correct = $review['is_correct'];
                $assessment->save();
            }
        }

        $user->status = 'active';
        $user->save();
        // Update the user's English proficiency level
        $user->student->updateProficiencyLevel($aiResponse['english_proficiency_level']);

        return response()->json(['success' => true]);
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
            dd("Invalid audio content provided.");
        }
    }

    private function reviewWithAI($requests)
    {
        $user = Auth::user();
        $age = Carbon::parse($user->date_of_birth)->age;

        $prompt = $this->generatePrompt($requests, $age);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI assistant helping to evaluate student assessments for an educational platform. Your task is to review each question and the corresponding answer provided by the students. For each answer, determine if it is correct, and provide detailed feedback. Include suggestions for improvement, focusing on areas such as understanding of the subject matter, clarity of communication, and accuracy. Additionally, consider the overall quality of the answers in terms of their completeness and relevance. Also, provide an overall English proficiency level from 1 to 6 based on the students answers.'
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
        ]);

        \Log::info("response: " . json_encode($response));

        // Extract and clean the JSON part of the response
        $responseContent = $response->choices[0]->message->content;
        $jsonString = $this->extractJsonString($responseContent);

        $aiResponse = json_decode($jsonString, true);
        \Log::info("aiResponse: " . json_encode($aiResponse));

        return $aiResponse;
    }

    private function extractJsonString($responseContent)
    {
        $jsonStart = strpos($responseContent, '{');
        $jsonEnd = strrpos($responseContent, '}') + 1;
        $jsonString = substr($responseContent, $jsonStart, $jsonEnd - $jsonStart);

        // Further clean-up if necessary
        $jsonString = trim($jsonString, " \t\n\r\0\x0B");

        return $jsonString;
    }

    private function generatePrompt($requests, $age)
    {
        $prompt = "
        The following are responses from a non-native English speaking student aged $age. Please review and provide feedback in JSON format with the following fields: 
        - id (question id)
        - correct (0 or 1)
        - review (brief comment on what the student should work on to improve: syntax, grammar, misunderstanding the question, etc.)
        
        Additionally, provide the overall English proficiency level from 1 to 6 based on the student's answers.
        
        Here is the JSON structure you will receive:
        - question_id: the ID of the question
        - question: the text of the question
        - answer: the user's answer
        - question_type: the type of question ('text', 'choice', 'voice')
        - sub_text: additional information related to the question (optional)
        - correct_answer: the correct answer for the question (only for 'choice' type questions)
        - transcription: the text transcription of the voice response (only for 'voice' type questions) + any words that were not transcribed correctly that mainly would be from wrong spillings so that the Student can correct them.
        - choices: array of possible choices (only for 'choice' type questions)
        - is_correct: whether the user's answer is correct (only for 'choice' type questions)
        - ai_review: a review of the user's answer. If the answer is incorrect, include a brief comment on what the student should work on to improve (syntax, grammar, misunderstanding the question, etc.)
        
        Evaluate the user's responses and return the results in the following JSON structure:
        
        {
          \"questions\": [
            {
              \"question_id\": 1,
              \"is_correct\": true,
              \"ai_review\": \"The answer is correct.\"
            },
            {
              \"question_id\": 2,
              \"is_correct\": false,
              \"ai_review\": \"The answer is incorrect. The student misunderstood the question.\"
            }
          ],
          \"english_proficiency_level\": 3
        }
        
        Evaluate the following questions:\n\n";

        foreach ($requests as $request) {
            $prompt .= "Question ID: {$request['question_id']}\n";
            $prompt .= "Question: {$request['question']}\n";

            if (isset($request['choices'])) {
                $prompt .= "Choices: " . implode(', ', $request['choices']) . "\n";
                $prompt .= "Correct Answer: {$request['correct_answer']}\n";
                $prompt .= "Student Answer: {$request['user_answer']}\n\n";
            } elseif ($request['question_type'] === 'text') {
                $prompt .= "Student Answer: {$request['user_answer']}\n\n";
            } elseif ($request['question_type'] === 'voice') {
                $prompt .= "Transcription: {$request['transcription']}\n\n";
            }
        }

        return $prompt;
    }

}