<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;
use PDF;
use Illuminate\Support\Facades\DB;


class CertificateController extends Controller
{
    public function check(Request $request)
    {
        $user = User::find($request->user_id);
        $isStudent = $user->hasRole('Student');
        $course = Course::find($request->course_id);
        $completed = false;
        if ($course) {
            $completed = $user->courses()->where('course_id', $request->course_id)->exists();
        }

        if ($isStudent && $completed) {
            $unitnumber = Unit::where('course_id', $request->course_id)->count();
            $unitsIds = $course->units->pluck('id')->toArray();
            $completedUnitIds = DB::table('student_units')
                ->where('student_id', $user->student->id)
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

            if ($completedUnitCount == $unitnumber) {
                return response()->json([
                    'isStudent' => $isStudent,
                    'completed' => $completed,
                    'allow_Certificate' => true
                ]);
            }

            return response()->json([
                'isStudent' => $isStudent,
                'completed' => $completed,
                'allow_Certificate' => false
            ]);

        }

        return response()->json([
            'isStudent' => $isStudent,
            'completed' => $completed
        ]);
    }

    public function download()
    {
        // $user = Auth::user();
        // $pdf = PDF::loadView('certificate.pdf', compact('user'));
        // return $pdf->download('certificate.pdf');
    }

    public function certificatePage($course_id)
    {
        if (!Auth::user()->hasRole('Student')) {
            abort(403, 'Unauthorized action.');
        }
        $course = Course::find($course_id);
        $user = Auth::user();
        $completed = $user->courses()->where('course_id', $course_id)->exists();

        if ($completed) {
            $unitnumber = Unit::where('course_id', $course_id)->count();
            $unitsIds = $course->units->pluck('id')->toArray();
            $completedUnitIds = DB::table('student_units')
                ->where('student_id', $user->student->id)
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

            if ($completedUnitCount == $unitnumber) {

                return view('dashboard.student.certificate', compact('course'));
            } else {
                abort(403, 'You have not completed the course yet.');
            }
        }

        abort(403, 'the course is not completed yet.');
    }

    public function generatePDF(Request $request)
    {
        $user = Auth::user();
        $course = Course::find($request->course_id);
        $data = [
            'user' => $user,
            'course' => $course,
            'date' => date('F j, Y')
        ];
        $pdf = PDF::loadView('pdf.certificate', $data);


        return $pdf->download('certificate.pdf');
    }
}