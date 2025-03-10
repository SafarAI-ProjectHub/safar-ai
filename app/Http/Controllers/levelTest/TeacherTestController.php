<?php

namespace App\Http\Controllers\levelTest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LevelTest;
use App\Models\LevelTestQuestion;
use App\Models\LevelTestChoice;
use App\Models\LevelTestAssessment;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class TeacherTestController extends Controller
{
    /**
     * Display a listing of the level tests.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        if ($user->hasRole('Super Admin| Admin')) {
            $levelTests = LevelTest::where('exam_type', 'teacher')->get();
        } else {
            abort(403, 'Unauthorized action.');
        }

        return view('dashboard.level_test.teacher_tests', compact('levelTests'));
    }

    /**
     * Display a Add Level Test Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function addTestPage()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        return view('dashboard.level_test.add_teacher_test');
    }

    /**
     * Get data for the level tests data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataTable(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin| Admin')) {
            $levelTests = LevelTest::where('exam_type', 'teacher')->get();
        } else {
            abort(403, 'Unauthorized action.');
        }

        return DataTables::of($levelTests)
            ->addColumn('actions', function ($test) {
                return '<div class="d-flex justify-content-around gap-2">
                <button class="btn btn-sm btn-warning btn-sm edit-test" data-id="' . $test->id . '">Edit</button>
                <button class="btn btn-sm btn-danger btn-sm delete-test" data-id="' . $test->id . '">Delete</button>
            </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Store a newly created level test and its questions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeTest(Request $request)
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.sub_text' => 'nullable|string',
            'questions.*.question_type' => 'required|string|in:text,voice,choice',
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.text' => 'required_if:questions.*.question_type,choice|string',
            'questions.*.choices.*.is_correct' => 'required_if:questions.*.question_type,choice|boolean',
        ]);

        $levelTest = LevelTest::create([
            'title' => $request->title,
            'description' => $request->description,
            'exam_type' => 'teacher',
            'active' => false,
        ]);

        foreach ($validatedData['questions'] as $question) {
            $createdQuestion = LevelTestQuestion::create([
                'level_test_id' => $levelTest->id,
                'question_text' => $question['text'],
                'sub_text' => $question['sub_text'],
                'question_type' => $question['question_type'],
                // 'media_url' => $question['media_url'],
                // 'media_type' => $question['media_type'],
            ]);

            if ($question['question_type'] === 'choice') {
                foreach ($question['choices'] as $choice) {
                    LevelTestChoice::create([
                        'level_test_question_id' => $createdQuestion->id,
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Level test and questions added successfully', 'levelTest' => $levelTest]);
    }

    /**
     * Display the Edit page for a level test.
     *
     * @param int $testId
     * @return \Illuminate\Http\Response
     */
    public function editTest($testId)
    {
        $levelTest = LevelTest::with('questions.choices')->findOrFail($testId);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        return view('dashboard.level_test.edit_teacher_test', compact('levelTest'));
    }

    /**
     * Update an existing level test and its questions.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $testId
     * @return \Illuminate\Http\Response
     */
    public function updateTest(Request $request, $testId)
    {
        $levelTest = LevelTest::findOrFail($testId);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'required|string',
            'questions.*.sub_text' => 'nullable|string',
            'questions.*.question_type' => 'required|string|in:text,voice,choice',
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.text' => 'required_if:questions.*.question_type,choice|string',
            'questions.*.choices.*.is_correct' => 'required_if:questions.*.question_type,choice|boolean',
        ]);

        $levelTest->update([
            'title' => $request->title,
            'description' => $request->description,
            'active' => $levelTest->active,
        ]);

        // Delete existing questions and choices
        foreach ($levelTest->questions as $question) {
            $question->choices()->delete();
            $question->delete();
        }

        // Add new questions and choices
        foreach ($validatedData['questions'] as $question) {
            $createdQuestion = LevelTestQuestion::create([
                'level_test_id' => $levelTest->id,
                'question_text' => $question['text'],
                'sub_text' => $question['sub_text'],
                'question_type' => $question['question_type'],
                // 'media_url' => $question['media_url'],
                // 'media_type' => $question['media_type'],
            ]);

            if ($question['question_type'] === 'choice') {
                foreach ($question['choices'] as $choice) {
                    LevelTestChoice::create([
                        'level_test_question_id' => $createdQuestion->id,
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Level test and questions updated successfully', 'levelTest' => $levelTest]);
    }

    /**
     * Delete a level test and its questions.
     *
     * @param int $testId
     * @return \Illuminate\Http\Response
     */
    public function deleteTest($testId)
    {
        $levelTest = LevelTest::findOrFail($testId);

        // Delete questions and choices
        foreach ($levelTest->questions as $question) {
            $question->choices()->delete();
            $question->delete();
        }

        $levelTest->delete();

        return response()->json(['message' => 'Level test deleted successfully']);
    }

    /**
     * Activate or deactivate a level test.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $testId
     * @return \Illuminate\Http\Response
     */
    public function activateTest(Request $request, $testId)
    {
        $levelTest = LevelTest::findOrFail($testId);

        // Convert the 'active' parameter to a boolean
        $isActive = filter_var($request->input('active'), FILTER_VALIDATE_BOOLEAN);

        if ($isActive) {
            // Activate the selected test
            LevelTest::where('exam_type', 'teacher')->update(['active' => false]);
            $levelTest->update(['active' => true]);
            return response()->json(['status' => true, 'message' => 'Test status updated successfully']);
        } else {
            // Deactivate the selected test
            $activeTestCount = LevelTest::where('exam_type', 'teacher')->where('active', true)->count();

            if ($activeTestCount <= 1) {
                return response()->json(['status' => false, 'message' => 'You cannot deactivate all tests. At least one test must be active.'], 400);
            }

            $levelTest->update(['active' => false]);
            return response()->json(['status' => true, 'message' => 'Test deactivated successfully']);
        }
    }

}