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
     * هذه الدالة تقرأ الطلب (user_id, course_id) وتتحقق هل الطالب
     * (1) لديه دور Student
     * (2) أنهى الكورس في Pivot
     * (3) أكمل كل وحدات الكورس
     */
    public function check(Request $request)
    {
        // اجلب المستخدم
        $user      = User::find($request->user_id);
        // هل له دور Student؟
        $isStudent = $user->hasRole('Student');
        // اجلب الكورس
        $course    = Course::find($request->course_id);

        // افتراضيًا لم نتحقق بعد من الإكمال
        $completed = false;
        if ($course) {
            // هل يوجد سطر في pivot (مثلاً user_courses) يشير إلى أنه أكمل الكورس؟
            $completed = $user->courses()
                ->where('course_id', $request->course_id)
                ->where('completed', 1)
                ->count() == 1;
        }

        // نجلب سجل الطالب من علاقة الـUser
        $student = $user->student; 
        // إن لم يكن لديه سجل في جدول students، فلا يمكن إكمال الشروط
        if (!$student) {
            return response()->json([
                'isStudent' => $isStudent,
                'completed' => $completed
            ]);
        }

        // رقم الطالب في جدول students
        $studentId = $student->id;

        // إن كانت كل الشروط OK
        if ($isStudent && $completed && $studentId) {
            // عدد الوحدات في هذا الكورس
            $unitnumber = Unit::where('course_id', $request->course_id)->count();
            // كل وحدات هذا الكورس
            $unitsIds   = $course->units->pluck('id')->toArray();

            // جلب الوحدات المكتملة عند الطالب
            $completedUnitIds = DB::table('student_units')
                ->where('student_id', $studentId)
                ->where('completed', 1)
                ->pluck('unit_id')
                ->toArray();

            // حصر الوحدات المكتملة ضمن وحدات هذا الكورس
            $completedUnitIds   = array_intersect($completedUnitIds, $unitsIds);
            $completedUnitCount = count($completedUnitIds);

            if ($completedUnitCount == $unitnumber) {
                return response()->json([
                    'isStudent'        => $isStudent,
                    'completed'        => $completed,
                    'allow_Certificate'=> true
                ]);
            }

            return response()->json([
                'isStudent'        => $isStudent,
                'completed'        => $completed,
                'allow_Certificate'=> false
            ]);
        }

        return response()->json([
            'isStudent' => $isStudent,
            'completed' => $completed
        ]);
    }

    public function download()
    {
        // لو عندك منطق سابق للتحميل المباشر، ضعيه هنا
    }

    /**
     * عرض صفحة الشهادة
     */
    public function certificatePage($course_id)
    {
        // يشترط أن يكون المستخدم الحالي طالبًا
        if (!Auth::user()->hasRole('Student')) {
            abort(403, 'Unauthorized action.');
        }

        // جلب الكورس
        $course = Course::find($course_id);
        // المستخدم الحالي
        $user   = Auth::user();
        // جلب سجل الطالب
        $student = $user->student;

        // إن لم يكن لديه سجل طالب
        if (!$student) {
            abort(403, 'Student record not found for this user.');
        }

        // هل المستخدم أكمل الكورس في pivot (مثلاً user_courses.completed=1)؟
        $completed = $user->courses()
            ->where('course_id', $course_id)
            ->exists(); 

        if ($completed) {
            $studentId  = $student->id;
            $unitnumber = Unit::where('course_id', $course_id)->count();
            $unitsIds   = $course->units->pluck('id')->toArray();

            // جلب معرفات الوحدات المكتملة
            $completedUnitIds = DB::table('student_units')
                ->where('student_id', $studentId)
                ->where('completed', 1)
                ->pluck('unit_id')
                ->toArray();

            // نفلترها لتخص هذا الكورس
            $completedUnitIds   = array_intersect($completedUnitIds, $unitsIds);
            $completedUnitCount = count($completedUnitIds);

            if ($completedUnitCount == $unitnumber) {
                // آخر وحدة مكتملة من حيث التاريخ
                $lastCompletedUnit = DB::table('student_units')
                    ->where('student_id', $studentId)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->orderBy('updated_at', 'desc')
                    ->first();

                // تاريخ الإكمال
                $completedAt = $lastCompletedUnit ? $lastCompletedUnit->updated_at : now();

                // أنشئ / احصل على الشهادة
                Certificate::firstOrCreate(
                    [
                        'user_id'   => $user->id,
                        'course_id' => $course->id
                    ],
                    [
                        'completed_at' => $completedAt
                    ]
                );

                // اعرض الـView
                return view('dashboard.student.certificate', compact('course', 'completedAt'));
            } else {
                abort(403, 'You have not completed the course yet.');
            }
        }

        abort(403, 'the course is not completed yet.');
    }

    /**
     * توليد الـPDF وتنزيل الشهادة
     */
    public function generatePDF(Request $request)
    {
        $user   = Auth::user();
        $course = Course::find($request->course_id);

        if (!$course) {
            return response()->json([
                'error' => 'Course not found.'
            ], 404);
        }

        $certificate = Certificate::where('user_id', $user->id)
                                  ->where('course_id', $course->id)
                                  ->first();

        if (!$certificate) {
            return response()->json([
                'error' => 'No certificate found for this user and course.'
            ], 404);
        }

        if (!$certificate->completed_at) {
            return response()->json([
                'error' => 'Certificate does not have a completion date.'
            ], 422);
        }

        // تحضير بيانات الـView
        $data = [
            'user'   => $user,
            'course' => $course,
            'date'   => $certificate->completed_at->format('F j, Y'),
        ];

        // استخدمي أي مكتبة PDF (مثل Dompdf أو Snappy) حسب مشروعك
        $pdf = PDF::loadView('pdf.certificate', $data)
                  ->setOption('page-size', 'A4')
                  ->setOption('orientation', 'landscape')
                  ->setOption('margin-top', '0')
                  ->setOption('margin-right', '0')
                  ->setOption('margin-bottom', '0')
                  ->setOption('margin-left', '0');

        return $pdf->download('certificate-' . $course->title . '.pdf');
    }

    /**
     * جلب قائمة الشهادات في DataTable
     */
    public function myCertificates(Request $request)
    {
        if ($request->ajax()) {
            $user    = Auth::user();
            $student = $user->student;   // سجل الطالب
            $studentId = $student ? $student->id : null;

            // جلب جميع الكورسات المكتملة من pivot
            // (نفترض عندك عمود completed=1 في user_courses)
            $courses = $user->courses()->where('completed', 1)->get();
            $completedCourses = [];

            foreach ($courses as $course) {
                $unitnumber = Unit::where('course_id', $course->id)->count();
                $unitsIds   = $course->units->pluck('id')->toArray();

                // جلب الوحدات المكتملة
                $completedUnitIds = DB::table('student_units')
                    ->where('student_id', $studentId)
                    ->where('completed', 1)
                    ->whereIn('unit_id', $unitsIds)
                    ->pluck('unit_id')
                    ->toArray();

                // إن كان عدد الوحدات في الكورس يساوي عدد الوحدات المكتملة
                if ($unitnumber > 0 && count($completedUnitIds) == $unitnumber) {
                    // آخر وحدة مكتملة
                    $lastCompletedUnit = DB::table('student_units')
                        ->where('student_id', $studentId)
                        ->where('completed', 1)
                        ->whereIn('unit_id', $unitsIds)
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    if ($lastCompletedUnit) {
                        $completedAt = $lastCompletedUnit->updated_at;

                        // ننشئ / نجلب الشهادة
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

            // جلب الشهادات النهائية للمستخدم
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
