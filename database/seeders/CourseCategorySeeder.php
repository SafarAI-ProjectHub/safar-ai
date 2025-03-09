<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CourseCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('course_categories')->insert([
            [
                'name' => 'Mathematics',
                'general_category' => 'Mathematics',
                'age_group' => '6-10',
                'moodle_category_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Science',
                'general_category' => 'Science',
                'age_group' => '10-14',
                'moodle_category_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Programming',
                'general_category' => 'Programming',
                'age_group' => '14-18',
                'moodle_category_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Business & Finance',
                'general_category' => 'Business',
                'age_group' => '18+',
                'moodle_category_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
