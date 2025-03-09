<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Block;
use App\Models\Course;

class BlocksTableSeeder extends Seeder
{
    public function run()
    {
        $courses = Course::pluck('id')->toArray();

        for ($i = 1; $i <= 20; $i++) {
            Block::create([
                'course_id' => $courses[array_rand($courses)], // تعيين الكورس بشكل عشوائي
                'name' => 'Block ' . $i,
                'description' => 'Description for Block ' . $i,
                'position' => $i,
                'moodle_section_id' => null, // افتراضيًا، لم يتم ربطه بـ Moodle
                'visibility' => true,
            ]);
        }
    }
}
