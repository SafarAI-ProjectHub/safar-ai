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
                ->where('student_id', $studentId)
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
                    'isStudent'         => $isStudent,
                    'completed'         => $completed,
                    'allow_Certificate' => true
                ]);
            }

            return response()->json([
                'isStudent'         => $isStudent,
                'completed'         => $completed,
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
            \Log::info('certificatePage => User is not Student => 403', [
                'user_id' => Auth::id()
            ]);
            abort(403, 'Unauthorized action.');
        }
        $course = Course::find($course_id);
        $user = Auth::user();
        $completed = $user->courses()->where('course_id', $course_id)->exists();

        if ($completed) {
            // عدد الوحدات الخاصة بالكورس
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
            \Log::info('certificatePage => completedUnitCount vs totalUnits', [
                'completedUnitCount' => $completedUnitCount,
                'unitnumber'         => $unitnumber
            ]);

            if ($completedUnitCount == $unitnumber) {
                $lastCompletedUnit = DB::table('student_units')
                    ->where('student_id', $studentId)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                $completedAt = $lastCompletedUnit->updated_at;

                Certificate::firstOrCreate(
                    [
                        'user_id'   => $user->id,
                        'course_id' => $course->id
                    ],
                    [
                        'completed_at' => $completedAt
                    ]
                );

                // عرض صفحة الشهادة
                return view('dashboard.student.certificate', compact('course', 'completedAt'));
            } else {
                \Log::info('certificatePage => Not all course units are completed => 403');
                abort(403, 'You have not completed the course yet.');
            }
        }

        abort(403, 'the course is not completed yet.');
    }

    /**
     * توليد ملف PDF للشهادة
     */
    public function generatePDF(Request $request)
    {
        $user   = Auth::user();
        $course = Course::find($request->course_id);
    
        // تحقّق من وجود الكورس
        if (!$course) {
            return response()->json([
                'error' => 'Course not found.'
            ], 404);
        }
    
        // تحقّق من وجود شهادة للمستخدم في هذا الكورس
        $certificate = Certificate::where('user_id', $user->id)
                                  ->where('course_id', $course->id)
                                  ->first();
    
        if (!$certificate) {
            return response()->json([
                'error' => 'No certificate found for this user and course.'
            ], 404);
        }
    
        // تأكد أن حقل completed_at متوفر
        if (!$certificate->completed_at) {
            \Log::info('generatePDF => No completion date => 422');
            return response()->json([
                'error' => 'Certificate does not have a completion date.'
            ], 422);
        }
    
        // البيانات التي ستُعرض في الـPDF
        $data = [
            'user'   => $user,
            'course' => $course,
            'date'   => $certificate->completed_at->format('F j, Y'),
        ];
    
        // توليد الـPDF باستخدام المكتبة
        $pdf = PDF::loadView('pdf.certificate', $data);
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('page-width', '224mm');
        $pdf->setOption('page-height', '300mm');
        $pdf->setOption('margin-top', '0');
        $pdf->setOption('margin-right', '0');
        $pdf->setOption('margin-bottom', '0');
        $pdf->setOption('margin-left', '0');
    
        // نرجع الملف كـ"Blob" حتى يتعامل معه الـAjax في المتصفح
        return $pdf->download('certificate-' . $course->title . '.pdf');
    }
    
    public function myCertificates(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
    
            // جلب جميع الكورسات التي ظهرت في جدول user_courses بعلامة completed = 1
            $courses = $user->courses()->where('completed', 1)->get();
            $completedCourses = [];

            foreach ($courses as $course) {
                // عدد وحدات الكورس
                $unitnumber = Unit::where('course_id', $course->id)->count();
                $unitsIds = $course->units->pluck('id')->toArray();
    
                // جلب معرفات الوحدات التي أنهاها الطالب
                $completedUnitIds = DB::table('student_units')
                    ->where('student_id', $user->student->id)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->pluck('unit_id')
                    ->toArray();
    
                $completedUnitCount = count($completedUnitIds);
    
                // إذا كان عدد الوحدات في الكورس يساوي عدد الوحدات المكتملة فعلاً
                if ($unitnumber == $completedUnitCount && $unitnumber > 0) {
                    // آخر وحدة مكتملة من حيث التاريخ
                    $lastCompletedUnit = DB::table('student_units')
                        ->where('student_id', $studentId)
                        ->where('completed', 1)
                        ->whereIn('unit_id', $unitsIds)
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    if ($lastCompletedUnit) {
                        $completedAt = $lastCompletedUnit->updated_at;
    
                        // إنشاء شهادة أو تجاهل إذا كانت موجودة مسبقاً
                        Certificate::firstOrCreate(
                            [
                                'user_id'   => $user->id,
                                'course_id' => $course->id
                            ],
                            [
                                'completed_at' => $completedAt
                            ]
                        );

                        $completedCourses[] = $course;
                    }
                }
            }
    
            // جلب الشهادات لإرسالها إلى DataTables
            $certificates = Certificate::where('user_id', $user->id)
                ->with('course')
                ->get();
    
            return DataTables::of($certificates)
                ->addColumn('course', function ($certificate) {
                    return $certificate->course
                        ? $certificate->course->title
                        : 'Course Not Found';
                })
                ->addColumn('completed_at', function ($certificate) {
                    return $certificate->completed_at
                        ? $certificate->completed_at->format('Y-m-d')
                        : '-';
                })
                ->addColumn('action', function ($certificate) {
                    if ($certificate->course) {
                        return '<a href="' . route('certificate.review', $certificate->course->id) . '"
                                    class="btn btn-primary">Show</a>';
                    }
                    return '';
                })
                ->make(true);
        }
    
        return view('dashboard.student.my_certificates');
    }
}
