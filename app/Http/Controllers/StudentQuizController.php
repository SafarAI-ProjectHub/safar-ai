<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
// use  job
// use App\Jobs\ProcessQuizResponses
use App\Jobs\ProcessQuizResponses;
use App\Models\UserResponse;

class StudentQuizController extends Controller
{
    // Display the list of quizzes available to the student
    public function listQuizzes(Request $request)
    {
        if ($request->ajax()) {
            $quizzes = Quiz::with(['unit', 'unit.course', 'unit.students'])
                ->whereHas('unit.students', function ($query) {
                    $query->where('student_units.student_id', Auth::user()->student->id)
                        ->where('student_units.completed', true);
                })->get();
            // dd($quizzes);

            return DataTables::of($quizzes)
                ->addColumn('title', function ($quiz) {
                    return $quiz->title;
                })
                ->addColumn('unit', function ($quiz) {
                    return $quiz->unit->title;
                })
                ->addColumn('course', function ($quiz) {
                    return $quiz->unit->course->title;
                })
                ->addColumn('action', function ($quiz) {
                    $user = Auth::user();
                    $assessment = Assessment::where('quiz_id', $quiz->id)->where('user_id', $user->id)->first();
                    $actionButtons = '';

                    if ($assessment) {
                        $actionButtons .= '<a href="' . route('student.quiz.result', $quiz->id) . '" class="btn btn-primary btn-sm">Show Result</a> ';
                        $actionButtons .= '<a href="' . route('student.quiz.show', $quiz->id) . '" class="btn btn-warning btn-sm">Retake Activity</a>';
                    } else {
                        $actionButtons .= '<a href="' . route('student.quiz.show', $quiz->id) . '" class="btn btn-primary btn-sm">Take Activity</a>';
                    }

                    return $actionButtons;
                })
                ->rawColumns(['action'])
                ->make(true);

        }

        return view('dashboard.student.quizzes');
    }

    // Display the quiz result for the student
    public function showQuizResult($id)
    {
        $user = Auth::user();
        $assessment = Assessment::where('quiz_id', $id)
            ->where('user_id', $user->id)
            ->with(['quiz', 'quiz.unit.course', 'quiz.questions.choices', 'userResponses'])
            ->firstOrFail();
        return view('dashboard.student.quiz_result', compact('assessment'));
    }


    // Display the specific quiz for the student
    public function showQuiz($id)
    {
        $quiz = Quiz::with(['questions.choices', 'unit.course'])->findOrFail($id);
        return view('dashboard.student.quiz', compact('quiz'));
    }

    // Handle the quiz submission
    public function submitQuiz(Request $request, $id)
    {
        // dd($request->all());
        $user = Auth::user();
        $data = $request->all();
        $quiz = Quiz::with('questions')->findOrFail($id);

        // Create a new assessment record
        $assessment = Assessment::firstOrCreate(
            [
                'quiz_id' => $quiz->id,
                'user_id' => $user->id
            ],
            [
                'score' => 0,
                'ai_mark' => null,
                'teacher_mark' => null,
                'ai_notes' => null,
                'teacher_notes' => null,
                'ai_assessment' => false,
                'teacher_review' => false,
                'assessment_date' => now(),
                'response' => ''
            ]
        );

        foreach ($quiz->questions as $question) {
            $responseKey = 'question_' . $question->id;
            if (isset($data[$responseKey])) {
                $responseValue = $data[$responseKey];

                // Find or create a new user response record
                $userResponse = UserResponse::firstOrNew(
                    ['question_id' => $question->id, 'user_id' => $user->id, 'assessment_id' => $assessment->id]
                );

                if ($question->question_type === 'choice') {
                    $choice = $question->choices()->find($responseValue);
                    $userResponse->response = $choice->choice_text;
                    $userResponse->correct = $choice->is_correct;
                } elseif ($question->question_type === 'text') {
                    $userResponse->response = $responseValue;
                    $userResponse->correct = false; // To be determined after AI evaluation
                } elseif ($question->question_type === 'voice') {
                    $file = $request->file('question_' . $question->id);
                    if ($file) {
                        $filename = uniqid() . '.wav';
                        $path = $file->storeAs('audio_responses', $filename, 'public');
                        $userResponse->response = 'storage/' . $path;
                        $userResponse->correct = false; // To be determined after AI evaluation
                    } else {
                        $missingAudio = true;
                        $missingAudioQuestions[] = $question->id;
                    }
                }
                $userResponse->ai_review = null;
                $userResponse->save();
            }
        }

        ProcessQuizResponses::dispatch($user->id, $quiz->id);

        return response()->json(['success' => true]);
    }

}