<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\CourseCategory;
use App\Models\Course;
use App\Models\User;
use Spatie\Permission\Models\Role;


class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = CourseCategory::pluck('id')->toArray();
        $teachers = Role::where('name', 'teacher')->first()?->users()->pluck('id')->toArray();

        $courses = [
            [
                'title' => 'Introduction to Mathematics',
                'description' => 'A fundamental course on basic mathematical concepts.',
                'level' => 1,
                'type' => 'weekly',
            ],
            [
                'title' => 'Fundamentals of Science',
                'description' => 'Basic scientific principles and experiments.',
                'level' => 2,
                'type' => 'intensive',
            ],
            [
                'title' => 'Introduction to Programming',
                'description' => 'Learn the basics of programming with hands-on exercises.',
                'level' => 1,
                'type' => 'weekly',
            ],
            [
                'title' => 'Business & Financial Planning',
                'description' => 'An essential course for understanding financial management and business strategies.',
                'level' => 3,
                'type' => 'intensive',
            ],
        ];

        foreach ($courses as $course) {
            Course::create([
                'category_id' => $categories[array_rand($categories)], // تعيين تصنيف عشوائي
                'title' => $course['title'],
                'description' => $course['description'],
                'level' => $course['level'],
                'type' => $course['type'],
                'completed' => false,
                'visibility' => true,
                'startdate' => Carbon::now()->addDays(rand(1, 30)),
                'enddate' => Carbon::now()->addDays(rand(60, 120)),
                'moodle_course_id' => null,
                'moodle_category_id' => null,
                'moodle_enrollment_method' => 'manual',
                'image' => 'default-course.jpg',
                'teacher_id' => $teachers ? $teachers[array_rand($teachers)] : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
