<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\Assessment;
use App\Models\ZoomMeeting;
use App\Models\Student;
use App\Models\LevelTestQuestion;
use App\Models\LevelTestAssessment;
use App\Models\LevelTest;
use App\Models\Teacher;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Models\CourseCategory;
use Yajra\DataTables\DataTables;
use App\Models\User;
use App\Services\ZoomService;
use App\Models\LevelTestChoice;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Http\UploadedFile;

class TeacherController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Teacher')) {
            $completedLevelTest = LevelTestAssessment::where('user_id', $user->id)->exists();

            if (!$completedLevelTest) {
                $levelTestQuestions = LevelTestQuestion::with('levelTest')
                    ->whereHas('levelTest', function ($query) {
                        $query->where('exam_type', 'teacher')->where('active', true);
                    })->get();
                return view('dashboard.teacher.level_test', compact('levelTestQuestions'));
            }

            if (auth()->user()->hasRole('Teacher') && auth()->user()->teacher->approval_status == 'pending') {
                return view('dashboard.teacher.pending_approval');
            }

            return redirect()->route('teacher.courses');
        } else {
            // لو لم يكن المستخدم معلماً
            abort(403, 'Unauthorized action.');
        }
    }

    public function getStudentQuizResults($courseId)
    {
        $quizzes = Quiz::where('course_id', $courseId)
            ->with(['assessments.user', 'assessments.quiz'])
            ->get();

        return view('dashboard.teacher.quiz_results', compact('quizzes'));
    }

    /**
     * جلب قائمة الدورات/الوحدات التي يدرّسها المعلم.
     * ملاحظة: هذا الإجراء يعرض صفحة تحمل نفس الـBlade
     * الذي يستدعي متغيّر $blocks في الحقول (Block).
     */
    public function getCourses()
    {
        if (auth()->user()->hasRole('Teacher')) {
            if (auth()->user()->teacher->approval_status == 'pending') {
                return redirect()->route('teacher.dashboard');
            }

            // الحصول على معرّف المعلم
            $teacherId = Teacher::where('teacher_id', auth()->id())->first()->id;

            // جلب الكورسات (الوحدات) المرتبطة بالمعلم
            $courses = Course::where('teacher_id', $teacherId)->get();

            // جلب التصنيفات
            $categories = CourseCategory::all();

            // هنا نعرّف الـ blocks كي لا يكون المتغيّر غير معرّف في الـBlade
            // يمكنك بدلاً من ذلك جلبها من جدول "blocks" لو كان موجوداً:
            // $blocks = \App\Models\Block::all();
            // أو مثلاً:
            // $blocks = DB::table('blocks')->get();
            // حالياً سنكتفي بمصفوفة ثابتة كمثال:
            $blocks = [
                (object)['id' => 1, 'name' => 'Block A'],
                (object)['id' => 2, 'name' => 'Block B'],
                (object)['id' => 3, 'name' => 'Block C'],
            ];

            // نمرر المتغيّرات للعرض
            return view('dashboard.admin.courses', compact('courses', 'categories', 'blocks'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function getStudentProfiles(Request $request)
    {
        $user = Auth::user();
        $courseId = $request->get('course_id');

        if ($user->hasRole('Super Admin')) {
            if ($courseId) {
                $students = User::whereHas('courses', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })->get();
            } else {
                $students = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Student');
                })->get();
            }

            $courses = Course::all();
        } else {
            if (auth()->user()->teacher->approval_status == 'pending') {
                return redirect()->route('teacher.dashboard');
            }
            $teacherId = Teacher::where('teacher_id', $user->id)->first()->id;
            $courses = Course::where('teacher_id', $teacherId)->get();

            if ($courseId) {
                $students = User::whereHas('courses', function ($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })->get();
            } else {
                $courseIds = $courses->pluck('id');
                $students = User::whereHas('courses', function ($query) use ($courseIds) {
                    $query->whereIn('course_id', $courseIds);
                })->get();
            }
        }

        if ($request->ajax()) {
            return Datatables::of($students)
                ->addIndexColumn()
                ->addColumn('full_name', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('country_location', function ($row) {
                    return $row->country_location;
                })
                ->addColumn('age', function ($row) {
                    return \Carbon\Carbon::parse($row->date_of_birth)->diffInYears(now());
                })
                ->addColumn('actions', function ($row) {
                    return '<a href="' . route('teacher.showStudentProfile', $row->id) . '" class="btn btn-primary btn-sm">Show Profile</a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('dashboard.teacher.student_profiles', compact('students', 'courses'));
    }

    public function showStudentProfile($id)
    {
        $student = User::findOrFail($id);
        return view('dashboard.teacher.show_student_profile', compact('student'));
    }

    public function scheduleZoomMeeting(Request $request)
    {
        $request->validate([
            'topic' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration' => 'required|integer',
        ]);

        $zoomService = new ZoomService();
        $meeting = $zoomService->createMeeting($request->topic, $request->start_time, $request->duration);

        ZoomMeeting::create([
            'teacher_id' => auth()->id(),
            'meeting_id' => $meeting->id,
            'topic' => $request->topic,
            'start_time' => $request->start_time,
            'duration' => $request->duration,
            'join_url' => $meeting->join_url,
        ]);

        return redirect()->back()->with('success', 'Zoom meeting scheduled successfully.');
    }

    /*
     * ========================
     * Level Test Assessment
     * ========================
     */

    public function submit(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $assessments = [];
        $openAiRequests = [];
        $audioTranscriptions = [];

        \Log::info("before foreach data" . json_encode($data));

        // جلب جميع الأسئلة مع الخيارات
        $questions = LevelTestQuestion::with('choices')->get()->keyBy('id');

        foreach ($data as $key => $value) {
            if (strpos($key, 'question_') !== false) {
                $parts = explode('_', $key);
                $questionId = $parts[1];
                $question = $questions->get($questionId);
                if (!$question) {
                    continue;
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
                        'notes' => $question->sub_text,
                        'user_answer' => $value,
                        'question_id' => $question->id,
                        'question_type' => 'text'
                    ];
                } elseif ($question->question_type === 'voice') {
                    // نتوقع أن $value عبارة عن ملف (UploadedFile)
                    $customFileName = sha1($value->getClientOriginalName()) . '.wav';
                    $path = $value->storeAs('audio_responses', $customFileName, 'public');
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

                $assessment->correct = false;
                $assessment->save();
                $assessments[] = $assessment;
            }
        }

        // مراجعة الذكاء الاصطناعي
        $aiResponse = $this->reviewWithAI($openAiRequests);

        // حفظ تقييم الذكاء الاصطناعي
        if (isset($aiResponse['questions']) && is_array($aiResponse['questions'])) {
            foreach ($aiResponse['questions'] as $review) {
                $assessment = LevelTestAssessment::where('level_test_question_id', $review['question_id'])
                    ->where('user_id', $user->id)
                    ->first();
                if ($assessment) {
                    $assessment->ai_review = $review['ai_review'] ?? '';
                    $assessment->correct = !empty($review['is_correct']);
                    $assessment->save();
                }
            }
        }

        return response()->json(['success' => true]);
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

            \Log::info("Converted audio file path: " . $wavPath);
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
            \Log::info("Transcription response: " . json_encode($response));
            return $response['text'];
        } else {
            return null;
        }
    }

    private function reviewWithAI($requests)
    {
        $prompt = $this->generatePrompt($requests);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI assistant helping to evaluate teacher assessments for an educational platform...'
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
        ]);

        \Log::info("response: " . json_encode($response));
        $responseContent = $response->choices[0]->message->content ?? '';
        $jsonString = $this->extractJsonString($responseContent);
        $aiResponse = json_decode($jsonString, true);
        \Log::info("aiResponse: " . json_encode($aiResponse));

        return $aiResponse ?: ['questions' => []];
    }

    private function extractJsonString($responseContent)
    {
        $jsonStart = strpos($responseContent, '{');
        $jsonEnd = strrpos($responseContent, '}');
        if ($jsonStart === false || $jsonEnd === false) {
            return '{}';
        }
        return trim(substr($responseContent, $jsonStart, $jsonEnd - $jsonStart + 1));
    }

    private function generatePrompt($requests)
    {
        $prompt = "
    This is a quiz to assess the teachers applying to our website...
    Evaluate the teacher's responses and return the results in the following JSON structure:

    {
        \"questions\": [
            {
                \"question_id\": 1,
                \"is_correct\": 1,
                \"ai_review\": \"The answer is correct.\"
            }
        ]
    }

    Here are the questions and answers provided by the teachers:\n\n";

        foreach ($requests as $request) {
            $prompt .= "Question ID: {$request['question_id']}\n";
            $prompt .= "Question: {$request['question']}\n";

            if (isset($request['choices'])) {
                $prompt .= "Choices: " . implode(', ', $request['choices']) . "\n";
                $prompt .= "Correct Answer: {$request['correct_answer']}\n";
                $prompt .= "Teacher Answer: {$request['user_answer']}\n\n";
            } elseif ($request['question_type'] === 'text') {
                $prompt .= "Teacher Notes: {$request['notes']}\n";
                $prompt .= "Teacher Answer: {$request['user_answer']}\n\n";
            } elseif ($request['question_type'] === 'voice') {
                $prompt .= "Transcription: {$request['transcription']}\n\n";
            }
        }

        \Log::info("Generated prompt: " . $prompt);
        return $prompt;
    }
}
