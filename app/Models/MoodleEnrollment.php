<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleEnrollment extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * كل حقل في جدول moodle_enrollments قابل للإسناد.
     * الحقول: user_id, moodle_course_id, enrolled_at, ...
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * نربط هنا مع Course بالاعتماد على العمود moodle_course_id
     * الذي يقابل moodle_course_id في جدول courses
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'moodle_course_id', 'moodle_course_id');
    }
}
