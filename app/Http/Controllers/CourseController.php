<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Quiz;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\CourseStudent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CourseController extends Controller
{
    public function showcourse($courseId)
    {
        $unitnumber = Unit::where('course_id', $courseId)->count();
        $numberstd = Course::find($courseId)->students->count();
        $course = Course::with('units')->findOrFail($courseId);

        if (Auth::user()->hasRole('Student')) {
            if (!Auth::user()->courses->contains($courseId)) {
                abort(403, 'You are not enrolled in this course');
            }

            $completedUnitIds = DB::table('student_units')
                ->where('student_id', Auth::user()->student->id)
                ->where('completed', true)
                ->pluck('unit_id')
                ->toArray();

        } else {
            $completedUnitIds = [];
        }

        return view('dashboard.admin.show_course', compact('course', 'unitnumber', 'numberstd', 'completedUnitIds'));
    }

    public function updateUnitCompletion(Request $request)
    {
        $studentId = Auth::user()->student->id;
        $unitId = $request->input('unit_id');
        $completed = $request->input('completed');


        DB::table('student_units')->updateOrInsert(
            ['student_id' => $studentId, 'unit_id' => $unitId],
            ['completed' => $completed]
        );

        return response()->json(['success' => true]);
    }

}