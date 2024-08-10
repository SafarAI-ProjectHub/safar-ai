<?php

namespace App\Http\Controllers\levelTest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LevelTest;
use App\Models\LevelTestQuestion;
use App\Models\LevelTestChoice;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Models\CourseCategory;
use App\Jobs\ProcessLevelTestQuestions;

class StudentTestController extends Controller
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

        if ($user->hasRole('Admin')) {
            $levelTests = LevelTest::where('exam_type', 'student')->get();
        } else {
            // Adjust this logic based on your requirement to fetch tests for students
            $levelTests = LevelTest::where('exam_type', 'student')->get();
        }

        return view('dashboard.level_test.student_tests', compact('levelTests'));
    }

    /**
     * Display an Add Level Test Page.
     *
     * @return \Illuminate\Http\Response
     */
    public function addTestPage()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $ageGroups = CourseCategory::all();
        return view('dashboard.level_test.add_student_test', compact('ageGroups'));
    }

    /**
     * Get data for the level tests data table.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataTable(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            $levelTests = LevelTest::where('exam_type', 'student')->get();
        } else {
            // Adjust this logic based on your requirement to fetch tests for students
            $levelTests = LevelTest::where('exam_type', 'student')->get();
        }

        return DataTables::of($levelTests)
            ->addColumn('age_group', function ($test) {
                return $test->ageGroup->age_group;
            })
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
            'age_group_id' => 'required|exists:course_categories,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'nullable|string',
            'questions.*.sub_text' => 'nullable|string',
            'questions.*.type' => 'required|string|in:text,voice,choice',
            'questions.*.question_type_switch' => 'required|string|in:audio,text',
            'questions.*.audio' => 'required_if:questions.*.question_type_switch,audio|mimes:mp3,wav|max:5000',
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.text' => 'required_if:questions.*.type,choice|string',
            'questions.*.choices.*.is_correct' => 'required_if:questions.*.type,choice|boolean',
        ]);

        // Create the Level Test
        $levelTest = LevelTest::create([
            'title' => $request->title,
            'description' => $request->description,
            'exam_type' => 'student',
            'active' => false,
            'age_group_id' => $request->age_group_id,
        ]);

        foreach ($validatedData['questions'] as $question) {
            // Initialize variables for media_type and media_url
            $mediaType = null;
            $mediaUrl = null;

            // Handle audio file if the question is audio-based
            if ($question['question_type_switch'] === 'audio' && isset($question['audio'])) {
                // Store the audio file in the public directory
                $audioPath = $question['audio']->store('leveltest/audios', 'public');

                // Set media_type and media_url for this question
                $mediaType = 'audio';
                $mediaUrl = 'storage/' . $audioPath;
            }

            // Create the question with the appropriate media_type and media_url
            $createdQuestion = LevelTestQuestion::create([
                'level_test_id' => $levelTest->id,
                'question_text' => $question['question_type_switch'] === 'text' ? $question['text'] : "Please listen to the audio, then answer the question",
                'sub_text' => $question['sub_text'],
                'question_type' => $question['type'],
                'media_type' => $mediaType,
                'media_url' => $mediaUrl,
            ]);

            // Handle choices if the question type is choice
            if ($question['type'] === 'choice') {
                foreach ($question['choices'] as $choice) {
                    LevelTestChoice::create([
                        'level_test_question_id' => $createdQuestion->id,
                        'choice_text' => $choice['text'],
                        'is_correct' => $choice['is_correct'],
                    ]);
                }
            }
            ProcessLevelTestQuestions::dispatch($createdQuestion->id);
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
        $ageGroups = CourseCategory::all();
        return view('dashboard.level_test.edit_student_test', compact('levelTest', 'ageGroups'));
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

        // Validate the request data with custom logic for audio
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'age_group_id' => 'required|exists:course_categories,id',
            'questions' => 'required|array|min:1',
            'questions.*.text' => 'nullable|string',
            'questions.*.sub_text' => 'nullable|string',
            'questions.*.question_type' => 'required|string|in:text,voice,choice',
            'questions.*.question_type_switch' => 'required|string|in:audio,text',
            'questions.*.audio' => [
                'mimes:mp3,wav',
                'max:5000',
                function ($attribute, $value, $fail) use ($request) {
                    $questionIndex = explode('.', $attribute)[1];
                    $existingQuestion = isset($request->questions[$questionIndex]['id'])
                        ? LevelTestQuestion::find($request->questions[$questionIndex]['id'])
                        : null;

                    if ($request->questions[$questionIndex]['question_type_switch'] === 'audio') {
                        if (!$value && !$existingQuestion?->media_url) {
                            $fail('The ' . $attribute . ' is required.');
                        }
                    }
                }
            ],
            'questions.*.choices' => 'nullable|array',
            'questions.*.choices.*.text' => 'required_if:questions.*.question_type,choice|string',
            'questions.*.choices.*.is_correct' => 'required_if:questions.*.question_type,choice|boolean',
        ]);

        // Update the level test data
        $levelTest->update([
            'title' => $request->title,
            'description' => $request->description,
            'age_group_id' => $request->age_group_id,
        ]);

        // Delete existing questions and choices
        foreach ($levelTest->questions as $question) {
            $question->choices()->delete();
            $question->delete();
        }

        // Add or update questions and choices
        foreach ($validatedData['questions'] as $question) {
            $mediaType = null;
            $mediaUrl = null;

            if ($question['question_type_switch'] === 'audio' && isset($question['audio'])) {
                $audioPath = $question['audio']->store('leveltest/audios', 'public');
                $mediaType = 'audio';
                $mediaUrl = 'storage/' . $audioPath;
            }

            $createdQuestion = LevelTestQuestion::updateOrCreate(
                ['id' => $question['id'] ?? null],
                [
                    'level_test_id' => $levelTest->id,
                    'question_text' => $question['question_type_switch'] === 'text' ? $question['text'] : "Please listen to the audio, then answer the question",
                    'sub_text' => $question['sub_text'],
                    'question_type' => $question['question_type'],
                    'media_type' => $mediaType,
                    'media_url' => $mediaUrl,
                ]
            );

            if ($question['question_type'] === 'choice') {
                foreach ($question['choices'] as $choice) {
                    LevelTestChoice::updateOrCreate(
                        ['id' => $choice['id'] ?? null],
                        [
                            'level_test_question_id' => $createdQuestion->id,
                            'choice_text' => $choice['text'],
                            'is_correct' => $choice['is_correct'],
                        ]
                    );
                }
            }
            ProcessLevelTestQuestions::dispatch($createdQuestion->id);
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
            // Get the age group ID of the test to be activated
            $ageGroupId = $levelTest->age_group_id;

            // Deactivate all tests in the same age group
            LevelTest::where('exam_type', 'student')
                ->where('age_group_id', $ageGroupId)
                ->update(['active' => false]);

            // Activate the selected test
            $levelTest->update(['active' => true]);

            return response()->json(['status' => true, 'message' => 'Test status updated successfully']);
        } else {
            // Ensure at least one test is active per age group
            $ageGroupId = $levelTest->age_group_id;

            // Count the number of active tests in the same age group
            $activeTestCount = LevelTest::where('exam_type', 'student')
                ->where('age_group_id', $ageGroupId)
                ->where('active', true)
                ->count();

            if ($activeTestCount <= 1) {
                return response()->json(['status' => false, 'message' => 'You cannot deactivate all tests in this age group. At least one test must be active.'], 400);
            }

            // Deactivate the selected test
            $levelTest->update(['active' => false]);

            return response()->json(['status' => true, 'message' => 'Test deactivated successfully']);
        }
    }


}