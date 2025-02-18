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
use App\Models\YoutubeVideo;
use App\Models\Block;
use App\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // إذا لم يكن لديه سجل طالب من قبل, ننشئ له واحدًا
        if (!$user->student && $user->hasRole('Student')) {
            $newStudent = new Student();
            $newStudent->student_id = $user->id;
            $newStudent->english_proficiency_level = 1; 
            $newStudent->save();
            $user->refresh();
        }

        $ageGroup = $user->getAgeGroup();

        // إذا عمره بين 1-5, نعّده مشترك مبدئيًا بدون اختبارات
        if ($ageGroup === '1-5') {
            $user->status = 'active';
            $user->save();
            if ($user->student) {
                $user->student->updateProficiencyLevel(1);
            }
        } else {
            // التأكد من أنهى اختبار المستوى أم لا
            if ($user->hasRole('Student') && $user->status === 'pending') {
                $completedLevelTest = LevelTestAssessment::where('user_id', Auth::id())->exists();
                if (!$completedLevelTest) {
                    $levelTestQuestions = LevelTestQuestion::with('levelTest')
                        ->whereHas('levelTest', function ($query) use ($ageGroup) {
                            $query->where('exam_type', 'student')
                                  ->where('active', true)
                                  ->whereHas('ageGroup', function ($q) use ($ageGroup) {
                                      $q->where('age_group', $ageGroup);
                                  });
                        })
                        ->get();

                    if ($levelTestQuestions->isEmpty()) {
                        // لا يوجد أسئلة => نفعّل الطالب مباشرة
                        $user->status = 'active';
                        $user->save();
                        if ($user->student) {
                            $user->student->updateProficiencyLevel(1);
                        }
                    } else {
                        return view('dashboard.student.level_test', compact('levelTestQuestions'));
                    }
                }
            }
        }

        // تكملة المنطق ...
        $dateOfBirth = $user->date_of_birth;
        $age = Carbon::parse($dateOfBirth)->age;

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

        $englishLevel = $user->student ? $user->student->english_proficiency_level : 1;

        if ($category) {
            $courses = Course::where('level', $englishLevel)
                ->where('category_id', $category->id)
                ->get();

            foreach ($courses as $course) {
                $course->number_of_students = CourseStudent::where('course_id', $course->id)->count();
            }
        } else {
            $courses = collect();
        }

        $videoAgeGroup = $user->getAgeGroup();
        $videos = YoutubeVideo::where('age_group', $videoAgeGroup)->paginate(12);

        if ($request->ajax()) {
            if ($request->has('video_id')) {
                $video = YoutubeVideo::findOrFail($request->video_id);
                return response()->json($video);
            }
            $videos = YoutubeVideo::where('age_group', $videoAgeGroup)->paginate(12);
            return response()->json($videos);
        }

        $subscription = UserSubscription::where('user_id', Auth::id())->first();
        $enrolledCourseIds = Auth::user()->courses ? Auth::user()->courses->pluck('id')->toArray() : [];
        $planDetails = \App\Models\Subscription::where('is_active', 1)->first();

        return view('dashboard.student.dashboard', compact(
            'courses',
            'planDetails',
            'subscription',
            'enrolledCourseIds',
            'videos'
        ));
    }
    public function levelTest()
    {
        $completedLevelTest = LevelTestAssessment::where('user_id', Auth::id())->exists();
        if (!$completedLevelTest) {
            $levelTestQuestions = LevelTestQuestion::with('levelTest')
                ->whereHas('levelTest', function ($query) {
                    $query->where('exam_type', 'student')->where('active', true);
                })
                ->get();
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

        if (CourseStudent::where('course_id', $courseId)->where('student_id', $userId)->exists()) {
            return response()->json([
                'error' => 'You are already enrolled in this course.'
            ], 400);
        }

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

    /**
     * يعرض جميع البلوكات والكورسات والدروس في صفحة واحدة (myCourses.blade)
     */
    public function myCourses(Request $request)
    {
        $blocksAll = Block::with('courses')->get(); 

        $blockId  = $request->get('block_id');
        $unitId   = $request->get('unit_id');
        $lessonId = $request->get('lesson_id'); 

        // عرض تفاصيل الدرس عند وجود lesson_id
        if ($lessonId) {
            $lesson = \App\Models\Unit::find($lessonId);
            if (!$lesson) {
                return redirect()->route('student.myCourses')->with('error', 'Lesson not found!');
            }
            return view('dashboard.student.myCourses', [
                'stage'   => 'lesson_details',
                'lesson'  => $lesson,
                'blocks'  => collect(),
                'courses' => collect(),
                'units'   => collect(),
                'lessons' => collect(),
            ]);
        }

        // عرض قائمة البلوكات عند عدم وجود أي من block_id, unit_id
        if (!$blockId && !$unitId) {
            return view('dashboard.student.myCourses', [
                'stage'   => 'blocks',
                'blocks'  => $blocksAll,
                'courses' => collect(),
                'units'   => collect(),
                'lessons' => collect(),
            ]);
        }

        // عند وجود block_id فقط => عرض الوحدات (الكورسات في هذا البلوك)
        if ($blockId && !$unitId) {
            $selectedBlock = $blocksAll->where('id', $blockId)->first();
            if (!$selectedBlock) {
                return redirect()->route('student.myCourses')->with('error', 'Block not found!');
            }

            $filteredCourses = $selectedBlock->courses;

            return view('dashboard.student.myCourses', [
                'stage'   => 'units',
                'blocks'  => collect(),
                'courses' => $filteredCourses,
                'units'   => collect(),
                'lessons' => collect(),
            ]);
        }

        // عند وجود unit_id => عرض الدروس
        if ($unitId) {
            $foundCourse = null;
            foreach ($blocksAll as $block) {
                $courseInBlock = $block->courses->where('id', $unitId)->first();
                if ($courseInBlock) {
                    $foundCourse = $courseInBlock;
                    break;
                }
            }

            if (!$foundCourse) {
                return redirect()->route('student.myCourses')->with('error', 'Course not found!');
            }

            $lessons = $foundCourse->units ?? collect();

            return view('dashboard.student.myCourses', [
                'stage'   => 'lessons',
                'blocks'  => collect(),
                'courses' => collect(),
                'units'   => collect(),
                'lessons' => $lessons,
            ]);
        }
    }

    public function myMeetings()
    {
        return view('dashboard.student.meetings');
    }

    public function getMeetings(Request $request)
    {
        try {
            $user = Auth::user();
            $userMeetings = UserMeeting::select('meeting_id', \DB::raw('MAX(id) as id'))
                ->where('user_id', $user->id)
                ->with(['meeting', 'meeting.user'])
                ->whereHas('meeting', function ($query) {
                    $query->where('start_time', '>=', now());
                })
                ->groupBy('meeting_id')
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
        $userMeeting = UserMeeting::where('user_id', Auth::id())
            ->where('meeting_id', $id)
            ->with(['meeting', 'meeting.user'])
            ->firstOrFail();

        return view('dashboard.student.meeting-details', compact('userMeeting'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        if (!$user->student && $user->hasRole('Student')) {
            $newStudent = new Student();
            $newStudent->student_id = $user->id;
            $newStudent->english_proficiency_level = 1;
            $newStudent->save();
            $user->refresh();
        }

        $data = $request->all();
        $assessments = [];
        $openAiRequests = [];
        $audioTranscriptions = [];

        $questions = LevelTestQuestion::with('choices')->get()->keyBy('id');

        foreach ($data as $key => $value) {
            if (strpos($key, 'question_') !== false) {
                $questionId = explode('_', $key)[1];
                $question = $questions->get($questionId);
                if (!$question) {
                    continue;
                }

                $assessment = new LevelTestAssessment();
                $assessment->level_test_question_id = $questionId;
                $assessment->user_id = $user->id;
                $script = null;

                if ($question->question_type === 'choice') {
                    $choice = LevelTestChoice::find($value);
                    $correctAnswer = $question->choices->where('is_correct', true)->first();
                    $assessment->response = $choice->choice_text;
                    if ($question->media_type === 'audio') {
                        $script = $question->script;
                    }
                    $openAiRequests[] = [
                        'question' => $question->question_text,
                        'sub_text' => $question->sub_text,
                        'choices' => $question->choices->pluck('choice_text')->toArray(),
                        'correct_answer' => $correctAnswer ? $correctAnswer->choice_text : null,
                        'user_answer' => $choice->choice_text,
                        'question_id' => $question->id,
                        'question_type' => 'choice',
                        'script' => $script ?? null
                    ];
                } elseif ($question->question_type === 'text') {
                    $assessment->response = $value;
                    if ($question->media_type === 'audio') {
                        $script = $question->script;
                    }
                    $openAiRequests[] = [
                        'question' => $question->question_text,
                        'sub_text' => $question->sub_text,
                        'user_answer' => $value,
                        'question_id' => $question->id,
                        'question_type' => 'text',
                        'script' => $script ?? null
                    ];
                } elseif ($question->question_type === 'voice') {
                    if ($question->media_type === 'audio') {
                        $script = $question->script;
                    }
                    $customFileName = sha1($value->getClientOriginalName()) . '.wav';
                    $path = $value->storeAs('audio_responses', $customFileName);
                    $transcription = $this->transcribeAudio(storage_path('app/public/' . $path));
                    $assessment->response = $path;
                    $audioTranscriptions[$questionId] = $transcription;
                    $openAiRequests[] = [
                        'question' => $question->question_text,
                        'transcription' => $transcription,
                        'question_id' => $question->id,
                        'question_type' => 'voice',
                        'sub_text' => $question->sub_text,
                        'script' => $script ?? null
                    ];
                }

                $assessment->correct = false; 
                $assessment->save();
                $assessments[] = $assessment;
            }
        }

        $aiResponse = $this->reviewWithAI($openAiRequests);

        foreach ($aiResponse['questions'] as $review) {
            $assessment = LevelTestAssessment::where('level_test_question_id', $review['question_id'])
                ->where('user_id', $user->id)
                ->first();
            if ($assessment) {
                $assessment->ai_review = $review['ai_review'];
                $assessment->correct   = $review['is_correct'];
                $assessment->save();
            }
        }

        $user->status = 'active';
        $user->save();

        if ($user->student) {
            $user->student->updateProficiencyLevel($aiResponse['english_proficiency_level']);
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
            dd("Invalid audio content provided.");
        }
    }

    private function reviewWithAI($requests)
    {
        $user = Auth::user();
        $age = Carbon::parse($user->date_of_birth)->age;

        $prompt = $this->generatePrompt($requests, $age);

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o', // أو gpt-3.5-turbo
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are an AI assistant helping to evaluate student assessments for an educational platform...'
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
        ]);

        $responseContent = $response->choices[0]->message->content;
        $jsonString = $this->extractJsonString($responseContent);

        $aiResponse = json_decode($jsonString, true);
        return $aiResponse;
    }

    private function extractJsonString($responseContent)
    {
        $jsonStart = strpos($responseContent, '{');
        $jsonEnd   = strrpos($responseContent, '}') + 1;
        $jsonString = substr($responseContent, $jsonStart, $jsonEnd - $jsonStart);
        $jsonString = trim($jsonString, " \t\n\r\0\x0B");
        return $jsonString;
    }

    private function generatePrompt($requests, $age)
    {
        $prompt = "
        The following are responses from a non-native English speaking student aged $age. Please review and provide feedback in JSON format with the following fields: 
        - id (question id)
        - correct (0 or 1)
        - review (brief comment on what the student should work on)
        
        Additionally, provide the overall English proficiency level from 1 to 6 based on the student's answers.
        
        Format example:
        {
          \"questions\": [
            {
              \"question_id\": 1,
              \"is_correct\": true,
              \"ai_review\": \"The answer is correct.\"
            }
          ],
          \"english_proficiency_level\": 3
        }
        
        Evaluate the following questions:\n\n";

        foreach ($requests as $request) {
            $prompt .= "Question ID: {$request['question_id']}\n";
            $prompt .= "Question: {$request['question']}\n";
            if (isset($request['sub_text'])) {
                $prompt .= "Additional Information: {$request['sub_text']}\n";
            }
            if (isset($request['script'])) {
                $prompt .= "Audio Script: {$request['script']}\n";
            }
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
