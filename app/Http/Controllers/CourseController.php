<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Unit;
use App\Models\Quiz;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\CourseStudent;


class CourseController extends Controller
{
    public function showcourse($courseId)
    {
        $unitnumber = Unit::where('course_id', $courseId)->count();
        $numberstd = Course::find($courseId)->students->count();
        $course = Course::with('units')->findOrFail($courseId);
        return view('dashboard.admin.show_course', compact('course', 'unitnumber', 'numberstd'));
    }

}