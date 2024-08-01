<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Course;
use App\Models\Unit;
use PDF;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\Certificate;


class CertificateController extends Controller
{
    public function check(Request $request)
    {
        $user = User::find($request->user_id);
        $isStudent = $user->hasRole('Student');
        $course = Course::find($request->course_id);
        $completed = false;
        if ($course) {
            $completed = $user->courses()->where('course_id', $request->course_id)->where('completed', 1)->count() == 1;
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
                $lastCompletedUnit = DB::table('student_units')
                    ->where('student_id', $user->student->id)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                $completedAt = $lastCompletedUnit->updated_at;

                Certificate::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'course_id' => $course->id
                    ],
                    [
                        'completed_at' => $completedAt
                    ]
                );

                return view('dashboard.student.certificate', compact('course', 'completedAt'));
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
        $certificate = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first();
        $data = [
            'user' => $user,
            'course' => $course,
            'date' => $certificate->completed_at->format('F j, Y')
        ];
        $pdf = PDF::loadView('pdf.certificate', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('page-width', '224mm');
        $pdf->setOption('page-height', '300mm');
        $pdf->setOption('margin-top', '0');
        $pdf->setOption('margin-right', '0');
        $pdf->setOption('margin-bottom', '0');
        $pdf->setOption('margin-left', '0');

        return $pdf->download('certificate-' . $course->title . '.pdf');
    }

    public function myCertificates(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $courses = $user->courses()->where('completed', 1)->get();
            $completedCourses = [];
            foreach ($courses as $course) {
                $unitnumber = Unit::where('course_id', $course->id)->count();
                $unitsIds = $course->units->pluck('id')->toArray();
                $completedUnitIds = DB::table('student_units')
                    ->where('student_id', $user->student->id)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->pluck('unit_id')
                    ->toArray();

                $completedUnitCount = count($completedUnitIds);

                if ($unitnumber == $completedUnitCount) {
                    $lastCompletedUnit = DB::table('student_units')
                        ->where('student_id', $user->student->id)
                        ->where('completed', 1)
                        ->whereIn('unit_id', $unitsIds)
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    $completedAt = $lastCompletedUnit->updated_at;

                    // Store or update certificate record
                    Certificate::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'course_id' => $course->id
                        ],
                        [
                            'completed_at' => $completedAt
                        ]
                    );

                    $completedCourses[] = $course;
                }
            }

            // Get the certificates data to return to DataTables
            $certificates = Certificate::where('user_id', $user->id)->with('course')->get();

            return DataTables::of($certificates)
                ->addColumn('course', function ($certificate) {
                    return $certificate->course->title;
                })
                ->addColumn('completed_at', function ($certificate) {
                    return $certificate->completed_at;
                })
                ->addColumn('action', function ($certificate) {
                    return '<a href="' . route('certificate.review', $certificate->course->id) . '" class="btn btn-primary">Show</a>';
                })
                ->make(true);
        }

        return view('dashboard.student.my_certificates');
    }
}