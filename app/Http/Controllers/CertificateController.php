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
    /**
     * التحقق إن كان المستخدم طالب وأنهى الكورس والوحدات.
     */
    public function check(Request $request)
    {
        $user      = User::find($request->user_id);
        $isStudent = $user && $user->hasRole('Student');
        $course    = Course::find($request->course_id);
        $completed = false;

        if ($user && $course) {
            // نفترض أنك تستخدم حقل completed=1 في pivot لتدل أن الطالب أنهى الكورس
            $completed = $user->courses()
                              ->where('course_id', $course->id)
                              ->where('completed', 1)
                              ->exists();
        }
        \Log::info('CertificateController@check => Course completed ?', [
            'completed' => $completed
        ]);

        // إذا هو طالب وأتمّ إنهاء الكورس
        if ($isStudent && $completed) {
            $unitnumber = Unit::where('course_id', $course->id)->count();
            $unitsIds   = $course->units->pluck('id')->toArray();

            // معرف الطالب في جدول students
            $studentId  = $user->student->id;

            // سحب الوحدات المكتملة الخاصة بهذا الطالب في هذا الكورس
            $completedUnitIds = DB::table('student_units')
                ->where('student_id', $studentId)
                ->where('completed', 1)
                ->whereIn('unit_id', $unitsIds)
                ->distinct()
                ->pluck('unit_id')
                ->toArray();

            $completedUnitCount = count($completedUnitIds);
            \Log::info('CertificateController@check => completedUnitCount', [
                'count' => $completedUnitCount
            ]);

            // إذا أكمل كل الوحدات
            if ($completedUnitCount == $unitnumber && $unitnumber > 0) {
                \Log::info('CertificateController@check => All units completed => allow certificate');
                return response()->json([
                    'isStudent'         => $isStudent,
                    'completed'         => $completed,
                    'allow_Certificate' => true
                ]);
            }

            \Log::info('CertificateController@check => Not all units completed => no certificate yet');
            return response()->json([
                'isStudent'         => $isStudent,
                'completed'         => $completed,
                'allow_Certificate' => false
            ]);
        }

        \Log::info('CertificateController@check => Either not a student or not completed');
        return response()->json([
            'isStudent' => $isStudent,
            'completed' => $completed
        ]);
    }

    /**
     * عرض صفحة الشهادة (إنهاء الكورس + إنهاء جميع الوحدات)
     */
    public function certificatePage($course_id)
    {
        \Log::info('CertificateController@certificatePage => Start', [
            'course_id'    => $course_id,
            'auth_user_id' => Auth::id()
        ]);

        // السماح فقط للطالب
        if (!Auth::user()->hasRole('Student')) {
            abort(403, 'Unauthorized action.');
        }

        $course = Course::find($course_id);
        $user   = Auth::user();

        // تحقق من إتمام الكورس في الجدول الوسيط
        $completed = $user->courses()
                          ->where('course_id', $course_id)
                          ->where('completed', 1)
                          ->exists();

        \Log::info('certificatePage => Checking course completion', [
            'user_id'   => $user->id,
            'course_id' => $course_id,
            'completed' => $completed
        ]);

        if ($completed) {
            $unitnumber = Unit::where('course_id', $course_id)->count();
            $unitsIds   = $course->units->pluck('id')->toArray();
            $studentId  = $user->student->id;

            // جلب الوحدات المكتملة
            $completedUnitIds = DB::table('student_units')
                ->where('student_id', $studentId)
                ->where('completed', 1)
                ->whereIn('unit_id', $unitsIds)
                ->distinct()
                ->pluck('unit_id')
                ->toArray();

            $completedUnitCount = count($completedUnitIds);

            \Log::info('certificatePage => completedUnitCount vs totalUnits', [
                'completedUnitCount' => $completedUnitCount,
                'unitnumber'         => $unitnumber
            ]);

            // إذا أنهى جميع الوحدات
            if ($completedUnitCount == $unitnumber && $unitnumber > 0) {
                // أحضر أحدث وحدة مكتملة من حيث التاريخ
                $lastCompletedUnit = DB::table('student_units')
                    ->where('student_id', $studentId)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                // تاريخ الإكمال
                $completedAt = $lastCompletedUnit->updated_at ?? now();

                // أنشئ أو أحضر الشهادة
                Certificate::firstOrCreate(
                    [
                        'user_id'   => $user->id,
                        'course_id' => $course->id
                    ],
                    [
                        'completed_at' => $completedAt
                    ]
                );

                return view('dashboard.student.certificate', compact('course', 'completedAt'));
            } else {
                \Log::info('certificatePage => Not all course units are completed => 403');
                abort(403, 'You have not completed the course yet.');
            }
        }

        \Log::info('certificatePage => course not completed => 403');
        abort(403, 'the course is not completed yet.');
    }

    /**
     * توليد ملف PDF للشهادة
     */
    public function generatePDF(Request $request)
    {
        \Log::info('CertificateController@generatePDF => Start', [
            'auth_user_id' => Auth::id(),
            'course_id'    => $request->course_id
        ]);

        $user    = Auth::user();
        $course  = Course::find($request->course_id);

        if (!$course) {
            \Log::info('generatePDF => Course not found => 404');
            return response()->json([
                'error' => 'Course not found.'
            ], 404);
        }

        // جلب الشهادة إن وجدت
        $certificate = Certificate::where('user_id', $user->id)
                                  ->where('course_id', $course->id)
                                  ->first();

        if (!$certificate) {
            \Log::info('generatePDF => No certificate found => 404');
            return response()->json([
                'error' => 'No certificate found for this user and course.'
            ], 404);
        }

        if (!$certificate->completed_at) {
            \Log::info('generatePDF => No completion date => 422');
            return response()->json([
                'error' => 'Certificate does not have a completion date.'
            ], 422);
        }

        $data = [
            'user'   => $user,
            'course' => $course,
            'date'   => $certificate->completed_at->format('F j, Y'),
        ];

        // توليد الملف PDF
        $pdf = PDF::loadView('pdf.certificate', $data);

        // إذا تريد العمل محلّيًا على ويندوز:
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // ضَع المسار الفعلي wkhtmltopdf.exe
            $pdf->setBinary('C:\Progra~1\wkhtmltopdf\bin\wkhtmltopdf.exe');
            // السماح بالوصول للملفات المحلية
            $pdf->setOption('enable-local-file-access', true);
        } else {
            // على السيرفر (لينكس)، عدل المسار حسب ما هو متوفر
            $pdf->setBinary('/usr/local/bin/wkhtmltopdf');
        }

        // بقية الإعدادات
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('page-width', '224mm');
        $pdf->setOption('page-height', '300mm');
        $pdf->setOption('margin-top', 0);
        $pdf->setOption('margin-right', 0);
        $pdf->setOption('margin-bottom', 0);
        $pdf->setOption('margin-left', 0);

        // ننفذ التنزيل
        return $pdf->download('certificate-' . $course->title . '.pdf');
    }

    /**
     * إظهار جميع شهادات الطالب في DataTables
     */
    public function myCertificates(Request $request)
    {
        \Log::info('CertificateController@myCertificates => Start (Ajax check)', [
            'isAjax'       => $request->ajax(),
            'auth_user_id' => Auth::id()
        ]);

        if ($request->ajax()) {
            $user      = Auth::user();
            $studentId = $user->student->id;

            // جلب جميع الكورسات التي أنهى الطالب فيها الكورس (completed=1)
            $courses = $user->courses()
                            ->where('completed', 1)
                            ->get();

            \Log::info('myCertificates => courses found with completed=1', [
                'course_ids' => $courses->pluck('id')->toArray()
            ]);

            $completedCourses = [];

            foreach ($courses as $course) {
                $unitnumber = Unit::where('course_id', $course->id)->count();
                $unitsIds   = $course->units->pluck('id')->toArray();

                $completedUnitIds = DB::table('student_units')
                    ->where('student_id', $studentId)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->pluck('unit_id')
                    ->toArray();

                $completedUnitCount = count($completedUnitIds);

                if ($unitnumber == $completedUnitCount && $unitnumber > 0) {
                    $lastCompletedUnit = DB::table('student_units')
                        ->where('student_id', $studentId)
                        ->where('completed', 1)
                        ->whereIn('unit_id', $unitsIds)
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    if ($lastCompletedUnit) {
                        $completedAt = $lastCompletedUnit->updated_at;

                        // أنشئ أو أحضر الشهادة
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

            // جلب الشهادات لهذا الطالب
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
