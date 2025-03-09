<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Unit;
use App\Models\StudentUnit;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\MoodleService; 
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * دالة عرض الدورات/البلوكات/الوحدات/الدروس للطالب.
     */
    public function myCourses(Request $request)
    {
        $blockId  = $request->input('block_id');
        $unitId   = $request->input('unit_id');
        $lessonId = $request->input('lesson_id');

        $stage   = 'blocks';
        $blocks  = [];
        $courses = [];
        $lessons = [];
        $lesson  = null;

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
        // المرحلة 3: عند إرسال unit_id فقط، نعرض الدروس (units)
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
                if (Str::contains($lesson->content, 'watch?v=')) {
                    $lesson->content = Str::after($lesson->content, 'watch?v=');
                } elseif (Str::contains($lesson->content, 'youtu.be/')) {
                    $lesson->content = Str::after($lesson->content, 'youtu.be/');
                }
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
     * إذا أردت عكس ذلك في Moodle أيضاً، سنضيف استدعاء لـ markSectionCompletion أو نحوها.
     */
    public function updateUnitCompletion(Request $request)
    {
        if (!Auth::check() || !Auth::user()->student) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $studentId = Auth::user()->student->id;

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

        //  تحدّث الإكمال في Moodle 
        if ($completed && Auth::user()->moodle_id && $lesson->moodle_section_id && $lesson->course->moodle_course_id) {
            $result = $this->moodleService->markSectionCompletion(
                Auth::user()->moodle_id,
                $lesson->course->moodle_course_id,
                $lesson->moodle_section_id
            );
            Log::info("تم تسجيل إكمال الدرس في Moodle", ['result' => $result]);
        }

        return redirect()->back()->with('success', 'Lesson status updated!');
    }

    public function teacherCourses()
    {
        $blocks = [
            (object)['id' => 1, 'name' => 'Block A'],
            (object)['id' => 2, 'name' => 'Block B'],
            (object)['id' => 3, 'name' => 'Block C'],
        ];

        return view('dashboard.teacher.courses.index', [
            'blocks' => $blocks,
        ]);
    }

    public function storeUnitForTeacher(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title'     => 'required|string|max:255',
        ]);

        $unit = Unit::create([
            'course_id'    => $request->course_id,
            'title'        => $request->title,
            'content_type' => 'text', 
        ]);

        // إنشاء قسم في Moodle
        $sectionId = $this->moodleService->createSectionForUnit($unit);
        if ($sectionId) {
            $unit->moodle_section_id = $sectionId;
            $unit->save();
        }

        return redirect()->back()->with('success', 'Unit created and synced with Moodle!');
    }
}
