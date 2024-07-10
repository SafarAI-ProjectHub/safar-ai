<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Quiz;
use App\Models\Assessment;
use App\Models\UserResponse;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class QuizResultController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Assessment::with(['quiz.unit.course', 'user']);

            if ($request->course_id) {
                $query->whereHas('quiz.unit.course', function ($q) use ($request) {
                    $q->where('id', $request->course_id);
                });
            }

            if ($request->unit_id) {
                $query->whereHas('quiz.unit', function ($q) use ($request) {
                    $q->where('id', $request->unit_id);
                });
            }

            if (Auth::user()->hasRole(['Admin', 'Super Admin'])) {
                $quizzes = $query->get();
            } else {
                $teacherCourses = Auth::user()->teacher->courses->pluck('id');
                $quizzes = $query->whereHas('quiz.unit.course', function ($q) use ($teacherCourses) {
                    $q->whereIn('id', $teacherCourses);
                })->get();
            }

            return DataTables::of($quizzes)
                ->addColumn('course_title', function ($assessment) {
                    return $assessment->quiz->unit->course->title;
                })
                ->addColumn('unit_title', function ($assessment) {
                    return $assessment->quiz->unit->title;
                })
                ->addColumn('ai_mark', function ($assessment) {
                    return $assessment->ai_mark;
                })
                ->addColumn('score', function ($assessment) {
                    return $assessment->score;
                })
                ->addColumn('action', function ($assessment) {
                    return '<a href="' . route('quizResults.show', $assessment->id) . '" class="btn btn-primary btn-sm">Review</a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $courses = Auth::user()->hasRole(['Admin', 'Super Admin'])
            ? \App\Models\Course::all()
            : Auth::user()->teacher->courses;

        return view('dashboard.quiz.quizzes_attempts', compact('courses'));
    }

    public function show($id)
    {
        $assessment = Assessment::with(['quiz.questions.choices', 'userResponses', 'quiz.unit.course'])->findOrFail($id);
        return view('dashboard.quiz.quiz_result', compact('assessment'));
    }

    public function update(Request $request, $id)
    {

        try {
            $request->validate([
                'teacher_mark' => 'nullable|numeric|min:0|max:100',
                'teacher_notes' => 'nullable|string',
                'responses' => 'nullable|array',
                'responses.*.correct' => 'nullable|boolean',
                'responses.*.teacher_review' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        }

        if (Auth::user()->hasRole('Teacher')) {
            $quiz = Assessment::findOrFail($id)->quiz;
            if (!in_array(Auth::user()->teacher->id, $quiz->unit->course->teacher->pluck('id')->toArray())) {
                abort(403, 'you are not allowed to review this assessment');
            }
        }

        $assessment = Assessment::findOrFail($id);
        $assessment->teacher_mark = $request->input('teacher_mark');
        $assessment->teacher_notes = $request->input('teacher_notes');
        $assessment->teacher_review = true;

        if (($request->input('teacher_mark') == null) && ($request->input('teacher_notes') == null || $request->input('teacher_notes') == '')) {
            $assessment->teacher_review = false;
        }
        $assessment->save();

        foreach ($request->input('responses') as $responseId => $response) {
            $userResponse = UserResponse::findOrFail($responseId);
            $userResponse->correct = $response['correct'];
            $userResponse->teacher_review = $response['teacher_review'];
            $userResponse->save();
        }

        return response()->json(['success' => true, 'message' => 'Assessment updated successfully']);
    }

    public function byCourse(Request $request)
    {
        $units = Unit::where('course_id', $request->course_id)->get();
        return response()->json(['units' => $units]);
    }
}