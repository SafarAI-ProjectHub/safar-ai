<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Choice;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\Assessment;
use App\Models\Student;

class QuizController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        if ($user->hasRole('Admin|Super Admin')) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = Course::where('teacher_id', $user->id)->get();
        } else {
            // abort 
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.quiz.quizzes', compact('courses'));
    }

    /**
     * Display a Add Quiz Page.
     *
     * @return \Illuminate\Http\Response
     */

    public function addQuizPage()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        if ($user->hasRole('Admin|Super Admin')) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = Course::where('teacher_id', $user->teacher->id)->get();
        } else {
            // abort 
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.quiz.add-quiz', compact('courses'));
    }

    /**
     * Fetch and return units without quizzes based on the selected course.
     *
     * @param int $courseId
     * @return \Illuminate\Http\Response
     */
    public function getUnits($courseId)
    {
        $units = Unit::where('course_id', $courseId)
            ->whereDoesntHave('quizzes')
            ->get();

        if ($units->isEmpty()) {
            return response()->json(['message' => 'All units have been assigned activities or there are no units available.'], 404);
        }

        return response()->json($units);
    }

    /**
     * Get data for the quizzes data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataTable(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Admin|Super Admin')) {
            $quizzes = Quiz::with('unit.course')->get();
        } else {

            $quizzes = Quiz::whereHas('unit.course', function ($query) use ($user) {
                $query->where('teacher_id', $user->teacher->id);
            })->with('unit.course')->get();
        }

        return DataTables::of($quizzes)
            ->addColumn('actions', function ($quiz) {
                return '<div class="d-flex justify-content-around gap-2">
                <a href="/quizzes/' . $quiz->id . '/results" class="btn btn-info btn-sm">Show Results</a>
                <button class="btn btn-warning btn-sm edit-quiz" data-id="' . $quiz->id . '">Edit</button>
                <button class="btn btn-danger btn-sm delete-quiz" data-id="' . $quiz->id . '">Delete</button>
            </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function showResults(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        if (Auth::user()->hasRole(['Teacher'])) {
            if (Auth::user()->teacher->courses->where('id', $quiz->unit->course->id)->count() == 0) {
                abort(403, 'YOU ARE NOT AUTHORIZED TO VIEW THIS PAGE');
            }
        }


        return view('dashboard.quiz.results', compact('quiz'));
    }

    public function resultsDataTable(Request $request, $quizId)
    {
        $assessments = Assessment::with('user.student')
            ->where('quiz_id', $quizId)
            ->get();

        $data = $assessments->map(function ($assessment) {
            $student = $assessment->user->student;
            return [
                'name' => $assessment->user->full_name,
                'status' => $assessment ? 'Submitted' : 'Not Submitted',
                'ai_mark' => $assessment->ai_mark ?? '-',
                'teacher_mark' => $assessment->teacher_mark ?? '-',
                'score' => $assessment->score ?? '-',
                'actions' => '<a href="' . route('quizResults.show', $assessment->id) . '" class="btn btn-info btn-sm">View Result</a>'
            ];
        });

        return DataTables::of($data)->rawColumns(['actions'])->make(true);
    }

    public function getStudentResponse($assessmentId)
    {
        $assessment = Assessment::with('quiz.questions.choices', 'user')->findOrFail($assessmentId);
        return response()->json($assessment);
    }


    /**
     * Store a newly created quiz and its questions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeQuiz(Request $request)
    {
        $validatedData = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'pass_mark' => 'required|integer',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.sub_text' => 'nullable|string',
            'questions.*.question_type' => 'required|string|in:text,voice,choice',
            'questions.*.mark' => 'required|integer|min:1',
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.text' => 'required_if:questions.*.question_type,choice|string',
            'questions.*.choices.*.is_correct' => 'required_if:questions.*.question_type,choice|boolean',
        ]);

        $quiz = Quiz::create([
            'unit_id' => $request->unit_id,
            'title' => $request->title,
            'pass_mark' => $request->pass_mark,
        ]);

        foreach ($validatedData['questions'] as $question) {
            $createdQuestion = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $question['text'],
                'sub_text' => $question['sub_text'],
                'question_type' => $question['question_type'],
                'mark' => $question['mark'],
            ]);

            if ($question['question_type'] === 'choice') {
                foreach ($question['choices'] as $choice) {
                    Choice::create([
                        'question_id' => $createdQuestion->id,
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'units and questions added successfully', 'quiz' => $quiz]);
    }

    /**
     *  Display the Edit page for a quiz.
     *
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function editQuiz($quizId)
    {
        $quiz = Quiz::with('questions.choices')->findOrFail($quizId);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        if ($user->hasRole('Admin|Super Admin')) {
            $courses = Course::all();
        } elseif ($user->hasRole('Teacher')) {
            $courses = Course::where('teacher_id', $user->teacher->id)->get();
        } else {
            // abort 
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.quiz.edit-quiz', compact('quiz', 'courses'));
    }

    /**
     * Update an existing quiz and its questions.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function updateQuiz(Request $request, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);

        $validatedData = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'pass_mark' => 'required|integer',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.sub_text' => 'nullable|string',
            'questions.*.question_type' => 'required|string|in:text,voice,choice',
            'questions.*.mark' => 'required|integer|min:1',
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.text' => 'required_if:questions.*.question_type,choice|string',
            'questions.*.choices.*.is_correct' => 'required_if:questions.*.question_type,choice|boolean',
        ]);

        $quiz->update([
            'unit_id' => $request->unit_id,
            'title' => $request->title,
            'pass_mark' => $request->pass_mark,
        ]);
        
        // Delete existing questions and choices
        foreach ($quiz->questions as $question) {
            if ($question->userResponses()->exists()) {
                $question->userResponses()->delete();
            }
            $question->choices()->delete();
            $question->delete();
        }

        // Add new questions and choices
        foreach ($validatedData['questions'] as $question) {
            $createdQuestion = Question::create([
                'quiz_id' => $quiz->id,
                'question_text' => $question['text'],
                'sub_text' => $question['sub_text'],
                'question_type' => $question['question_type'],
                'mark' => $question['mark'],
            ]);

            if ($question['question_type'] === 'choice') {
                foreach ($question['choices'] as $choice) {
                    Choice::create([
                        'question_id' => $createdQuestion->id,
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Activity and questions updated successfully', 'quiz' => $quiz]);
    }

    /**
     * Delete a quiz and its questions.
     *
     * @param int $quizId
     * @return \Illuminate\Http\Response
     */
    public function deleteQuiz($quizId)
{
    $quiz = Quiz::findOrFail($quizId);

    // Delete related assessments and their user responses
    $quiz->assessments()->each(function ($assessment) {
        $assessment->userResponses()->delete();
        
        $assessment->delete();
    });

    foreach ($quiz->questions as $question) {
        if ($question->choices()->exists()) {
            $question->choices()->delete();
        }
        $question->delete();
    }
    $quiz->delete();

    return response()->json(['message' => 'Activity deleted successfully']);
}

}