<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Quiz;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Rate;
use App\Models\CourseStudent;
use App\Models\StudentUnit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class CourseController extends Controller
{
    public function showcourse($courseId)
    {
        $unitnumber = Unit::where('course_id', $courseId)->count();
        $course = Course::find($courseId);
        $numberstd = CourseStudent::where('course_id', $courseId)->count();

        $course = Course::with('units')->findOrFail($courseId);
        $unitsIds = $course->units->pluck('id')->toArray();
        if (Auth::user()->hasRole('Student')) {
            if (!Auth::user()->courses->contains($courseId)) {
                abort(403, 'You are not enrolled in this course');
            }

            $completedUnitIds = DB::table('student_units')
                ->where('student_id', Auth::user()->student->id)
                ->where('completed', 1)
                ->get('unit_id')
                ->toArray();

            $completedUnitIds = array_filter($completedUnitIds, function ($unit) use ($unitsIds) {
                return in_array($unit->unit_id, $unitsIds);
            });

            $completedUnitIds = array_map(function ($unit) {
                return $unit->unit_id;
            }, $completedUnitIds);

            $completedUnitIds = array_values($completedUnitIds);

            $completedUnitCount = count($completedUnitIds);

        } else {
            $completedUnitIds = [];
            $completedUnitCount = 0;
        }
        $reviewsCount = Rate::where('course_id', $courseId)->count();
        $reviews = Rate::where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        $reviewsRate = Rate::where('course_id', $courseId)->avg('rate');
        if ($reviewsRate == null) {
            $reviewsRate = 0;
        }


        return view('dashboard.admin.show_course', compact('course', 'unitnumber', 'numberstd', 'completedUnitIds', 'completedUnitCount', 'reviews', 'reviewsCount', 'reviewsRate'));
    }

    public function updateUnitCompletion(Request $request)
    {
        $studentId = Auth::user()->student->id;
        $unitId = $request->input('unit_id');
        $completed = $request->input('completed');


        StudentUnit::updateOrInsert(
            ['student_id' => $studentId, 'unit_id' => $unitId],
            [
                'completed' => $completed,
                'updated_at' => Carbon::now(), // Set the current timestamp for updated_at
                'created_at' => DB::raw('IFNULL(created_at, "' . Carbon::now() . '")') // Only set created_at if it's a new record
            ]
        );

        return response()->json(['success' => true]);
    }

    public function rateCourse(Request $request)
    {

        $courseId = $request->input('course_id');
        $rating = $request->input('rating');
        $comment = $request->input('comment');
        $userId = Auth::user()->id;

        Rate::updateOrInsert(
            ['user_id' => $userId, 'course_id' => $courseId],
            ['rate' => $rating, 'comment' => $comment, 'updated_at' => Carbon::now(), 'created_at' => DB::raw('IFNULL(created_at, "' . Carbon::now() . '")')]
        );

        return response()->json(['success' => true]);
    }

}