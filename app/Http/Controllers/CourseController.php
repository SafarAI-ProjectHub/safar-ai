<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Unit;        // موديل الدرس
use App\Models\StudentUnit; // الجدول الوسيط للطالب والدروس
use Carbon\Carbon;
use Illuminate\Support\Str; // للتعامل مع النصوص بشكل أسهل

class CourseController extends Controller
{
    /**
     * دالة عرض الدورات/البلوكات/الوحدات/الدروس للطالب.
     */
    public function myCourses(Request $request)
    {
        $blockId  = $request->input('block_id');
        $unitId   = $request->input('unit_id');
        $lessonId = $request->input('lesson_id');

        // متغيّرات ستحدد أي مرحلة سنعرض
        $stage   = 'blocks';
        $blocks  = [];
        $courses = [];
        $lessons = [];
        $lesson  = null;

        // جلب معرّف الطالب إذا كان المستخدم طالباً
        $studentId = Auth::check() && Auth::user()->student
            ? Auth::user()->student->id
            : null;

        // المرحلة 1: إن لم يصلنا block_id أو unit_id أو lesson_id، نعرض الكتل (blocks)
        if (!$blockId && !$unitId && !$lessonId) {
            $stage  = 'blocks';
            $blocks = [
                (object)['id' => 1, 'name' => 'Block A'],
                (object)['id' => 2, 'name' => 'Block B'],
                (object)['id' => 3, 'name' => 'Block C'],
            ];
        }
        // المرحلة 2: عند إرسال block_id فقط، ننتقل لعرض الوحدات (courses/units)
        elseif ($blockId && !$unitId && !$lessonId) {
            $stage   = 'units';
            $courses = Course::where('block_id', $blockId)->get();
        }
        // المرحلة 3: عند إرسال unit_id فقط، نعرض الدروس (lessons)
        elseif ($unitId && !$lessonId) {
            $stage = 'lessons';
            $unit  = Course::with('units')->find($unitId);
            if ($unit) {
                $lessons = $unit->units;
                // لو الطالب مسجّل دخوله، نحدّد أي الدروس أتمّها
                if ($studentId) {
                    foreach ($lessons as $lsn) {
                        $completedVal = DB::table('student_units')
                            ->where('student_id', $studentId)
                            ->where('unit_id', $lsn->id)
                            ->value('completed');
                        $lsn->is_completed = ($completedVal == 1) ? 1 : 0;
                    }
                }
            }
        }
        // المرحلة 4: عند إرسال lesson_id، نعرض تفاصيل درس واحد
        elseif ($lessonId) {
            $stage  = 'lesson_details';
            $lesson = Unit::find($lessonId);

            // لو الطالب مسجّل دخوله، نحدد حالة إكمال الدرس
            if ($lesson && $studentId) {
                $completedVal = DB::table('student_units')
                    ->where('student_id', $studentId)
                    ->where('unit_id', $lesson->id)
                    ->value('completed');
                $lesson->is_completed = ($completedVal == 1) ? 1 : 0;
            }

            // إذا كان الدرس من نوع "youtube" نتحقق هل هو رابط كامل أم مجرد ID
            if ($lesson && $lesson->content_type === 'youtube') {
                // مثال: https://www.youtube.com/watch?v=xxxx
                if (Str::contains($lesson->content, 'watch?v=')) {
                    $lesson->content = Str::after($lesson->content, 'watch?v=');
                }
                // مثال: https://youtu.be/xxxx
                elseif (Str::contains($lesson->content, 'youtu.be/')) {
                    $lesson->content = Str::after($lesson->content, 'youtu.be/');
                }
                // لو لم يحتوِ على watch?v= أو youtu.be/ نفترض أنه ID جاهز
            }
        }

        // نمرر المتغيرات للعرض
        return view('dashboard.student.myCourses', [
            'stage'   => $stage,
            'blocks'  => $blocks,
            'courses' => $courses,
            'lessons' => $lessons,
            'lesson'  => $lesson,
        ]);
    }

    /**
     * دالة تغيير حالة الدرس (إكمال/إلغاء إكمال).
     */
    public function updateUnitCompletion(Request $request)
    {
        $studentId = Auth::user()->student->id;

        // الحصول على قيم الحقول من النموذج
        $lessonId  = $request->input('lesson_id');
        $completed = $request->input('completed'); // 0 أو 1

        // التحقق من وجود الدرس
        $lesson = Unit::find($lessonId);
        if (!$lesson) {
            return redirect()->back()->with('error', 'Lesson not found');
        }

        // تحديث أو إدراج السجل في جدول student_units
        StudentUnit::updateOrInsert(
            [
                'student_id' => $studentId,
                'unit_id'    => $lessonId,
            ],
            [
                'completed'  => $completed,
                'updated_at' => now()
            ]
        );

        return redirect()->back()->with('success', 'Lesson status updated!');
    }


    /**
     * مثال على دالة للمعلم (في حال احتجتَها):
     * سنمرر أيضاً blocks فارغة أو أي بيانات لازمة حتى لا تظهر رسالة الخطأ.
     */
    public function teacherCourses()
    {
        // هنا مثلاً نعرّف blocks حتى لا يكون المتغيّر غير معرّف
        $blocks = [
            (object)['id' => 1, 'name' => 'Block A'],
            (object)['id' => 2, 'name' => 'Block B'],
            (object)['id' => 3, 'name' => 'Block C'],
        ];

        // ... أي منطق آخر للمعلم ...
        return view('dashboard.teacher.courses.index', [
            'blocks' => $blocks,
            // يمكن تمرير أي بيانات أخرى يحتاجها المعلم
        ]);
    }
}
