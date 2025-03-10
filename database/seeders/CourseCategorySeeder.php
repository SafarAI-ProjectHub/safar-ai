<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseCategory;
use App\Services\MoodleService;
use Carbon\Carbon;

class CourseCategorySeeder extends Seeder
{
    protected $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    public function run()
    {
        $categories = [
            [
                'name' => 'Mathematics',
                'general_category' => 'Mathematics',
                'age_group' => '6-10',
            ],
            [
                'name' => 'Science',
                'general_category' => 'Science',
                'age_group' => '10-14',
            ],
            [
                'name' => 'Programming',
                'general_category' => 'Programming',
                'age_group' => '14-18',
            ],
            [
                'name' => 'Business & Finance',
                'general_category' => 'Business',
                'age_group' => '18+',
            ],
        ];

        foreach ($categories as $data) {
            // إنشاء التصنيف في Laravel
            $category = CourseCategory::create(array_merge($data, [
                'moodle_category_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]));

            // إرساله إلى Moodle وتحديث `moodle_category_id`
            // $moodleCategoryId = $this->moodleService->createCategory($category);

            // تم تعديله لتمرير مصفوفة بدلاً من كائن:
            // $moodleCategoryId = $this->moodleService->createCategory($category->toArray());

            // if ($moodleCategoryId) {
            //     $category->update(['moodle_category_id' => $moodleCategoryId]);
            // }
        }
    }
}
