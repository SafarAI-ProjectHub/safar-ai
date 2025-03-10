<?php

namespace App\Services\MoodleServices;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Block;

class BlocksService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url   = config('app.moodle_url', 'https://moodle.safarai.org/webservice/rest/server.php');
        $this->token = config('app.moodle_wstoken');
    }

    /**
     * إنشاء بلوك في Moodle على هيئة Section
     */
    public function createBlock(Block $block)
    {
        // نحتاج إلى course_id في Moodle:
        $moodleCourseId = optional($block->course)->moodle_course_id;
        if (!$moodleCourseId) {
            Log::warning("لا يمكن إنشاء بلوك لأن الكورس لا يملك moodle_course_id");
            return null;
        }

        // نستدعي core_course_create_sections لإضافة قسم جديد
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_create_sections',
            'moodlewsrestformat' => 'json',
            'sections' => [[
                'courseid'    => $moodleCourseId,
                'section'     => $block->position ?? 0,
                'name'        => $block->name,
                'summary'     => $block->description ?? '',
                'summaryformat' => 1, // 1 يعني نص (HTML)
                'visible'     => (int) $block->visibility,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        if ($response->successful()) {
            $data = $response->json();
            // من المتوقّع أن يعيد مصفوفة بأقسام أنشئت
            // مثل: [{"id":12,"section":1,"name":"BlockName","visible":1,...}]
            if (!empty($data[0]['id'])) {
                return $data[0]['id']; // القسم الذي تم إنشاؤه في Moodle
            }
        }

        Log::error('Failed to create block (section) in Moodle.', ['response' => $response->body()]);
        return null;
    }

    /**
     * تحديث بيانات بلوك (Section) في Moodle
     */
    public function updateBlock(Block $block)
    {
        // نحتاج إلى معرف القسم في Moodle:
        if (!$block->moodle_section_id) {
            Log::warning("لا يمكن تحديث بلوك لأنه لا يملك moodle_section_id");
            return null;
        }

        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_update_sections',
            'moodlewsrestformat' => 'json',
            'sections' => [[
                'id'            => $block->moodle_section_id,
                'name'          => $block->name,
                'summary'       => $block->description ?? '',
                'summaryformat' => 1,
                'visible'       => (int) $block->visibility,
            ]],
        ];

        $response = Http::asForm()->post($this->url, $params);
        if (!$response->successful()) {
            Log::error('Failed to update block (section) in Moodle.', ['response' => $response->body()]);
        }
        return $response->json();
    }

    /**
     * حذف بلوك (Section) من Moodle
     */
    public function deleteBlock($moodleSectionId)
    {
        if (!$moodleSectionId) {
            return null;
        }

        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_delete_course_sections',
            'moodlewsrestformat' => 'json',
            'sectionids'         => [$moodleSectionId],
        ];

        $response = Http::asForm()->post($this->url, $params);
        if (!$response->successful()) {
            Log::error('Failed to delete block (section) in Moodle.', ['response' => $response->body()]);
        }
        return $response->json();
    }

    /**
     * جلب أقسام (Sections) المقرر من Moodle ومزامنتها مع نظام Laravel
     */
    public function syncBlocksFromMoodle($course)
    {
        // يجب أن يملك الكورس معرفًا في Moodle
        if (!$course->moodle_course_id) {
            Log::warning("لا يمكن المزامنة لأن الكورس لا يملك moodle_course_id");
            return;
        }

        // نستدعي core_course_get_contents لجلب أقسام الكورس ومواده
        $params = [
            'wstoken'            => $this->token,
            'wsfunction'         => 'core_course_get_contents',
            'moodlewsrestformat' => 'json',
            'courseid'           => $course->moodle_course_id,
        ];

        $response = Http::get($this->url, $params);
        if (!$response->successful()) {
            Log::error('Failed to fetch sections (blocks) from Moodle.', ['response' => $response->body()]);
            return;
        }

        $sections = $response->json(); // مصفوفة أقسام
        if (!is_array($sections)) {
            Log::warning("syncBlocksFromMoodle: Unexpected response format.", ['response' => $sections]);
            return;
        }

        foreach ($sections as $sec) {
            // بعض الأقسام قد يكون 'section' = 0 هو القسم العام
            // قد نختار تجاهله أو نستخدمه حسب احتياجاتنا
            if (!isset($sec['section']) || $sec['section'] == 0) {
                continue;
            }

            // ابحث عن بلوك مطابق في قاعدة البيانات
            $localBlock = $course->blocks()->where('moodle_section_id', $sec['id'])->first();

            // إذا لم نجده، ننشئه
            if (!$localBlock) {
                $localBlock = $course->blocks()->create([
                    'moodle_section_id' => $sec['id'],
                    'name'              => $sec['name'] ?? 'Unnamed Block',
                    'description'       => $sec['summary'] ?? '',
                    'position'          => $sec['section'] ?? 1,
                    'visibility'        => isset($sec['visible']) ? (int) $sec['visible'] : 1,
                ]);
            } else {
                // حدّث البيانات
                $localBlock->update([
                    'name'        => $sec['name'] ?? $localBlock->name,
                    'description' => $sec['summary'] ?? $localBlock->description,
                    'position'    => $sec['section'] ?? $localBlock->position,
                    'visibility'  => isset($sec['visible']) ? (int) $sec['visible'] : $localBlock->visibility,
                ]);
            }
        }

        Log::info("syncBlocksFromMoodle: synced blocks (sections) for course #{$course->id}");
    }
}
