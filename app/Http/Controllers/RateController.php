<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rate;
use App\Models\User;
use App\Models\Course;
use Yajra\DataTables\Facades\DataTables;

class RateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Rate::with(['user', 'course']);

            if ($request->course_id) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->student_id) {
                $query->where('user_id', $request->student_id);
            }

            return DataTables::of($query)
                ->addColumn('username', function ($review) {
                    return $review->user->full_name;
                })
                ->addColumn('course_title', function ($review) {
                    return $review->course->title;
                })
                ->addColumn('rate', function ($review) {
                    return $review->rate;
                })
                ->addColumn('comment', function ($review) {
                    return $review->comment;
                })
                ->addColumn('created_at', function ($review) {
                    return $review->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('actions', function ($review) {
                    return '<a href="#" class="btn btn-sm btn-danger delete" data-id="' . $review->id . '"><i class="bx bxs-trash"></i> Delete</a>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        $students = User::whereHas('roles', function ($q) {
            $q->where('name', 'student');
        })->get();

        $courses = Course::all();

        return view('dashboard.admin.reviews', compact('students', 'courses'));
    }

    public function destroy($id)
    {
        $review = Rate::findOrFail($id);
        $review->delete();

        return response()->json(['success' => 'Review deleted successfully']);
    }
}