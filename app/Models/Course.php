<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'level',
        'type',
        'completed',
        'visibility',
        'startdate',
        'enddate',
        'moodle_course_id',
        'moodle_category_id',
        'moodle_enrollment_method',
        'image',
        'teacher_id'
    ];

    /**
     * علاقة الكورس بالمدرس
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * علاقة الكورس بالطلاب (Many-to-Many)
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id')
                    ->withPivot('enrollment_date', 'progress')
                    ->withTimestamps();
    }

    /**
     * علاقة الكورس بالتصنيف
     */
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * علاقة الكورس بالبلوكات (Blocks)
     */
    public function blocks()
    {
        return $this->hasMany(Block::class, 'course_id');
    }

    /**
     * علاقة الكورس بوحداته (من خلال البلوكات)
     */
    public function units()
    {
        return $this->hasManyThrough(Unit::class, Block::class, 'course_id', 'block_id');
    }

    /**
     * علاقة Moodle Category
     */
    public function moodleCategory()
    {
        return $this->hasOne(MoodleCategory::class, 'id', 'moodle_category_id');
    }

    /**
     * تكامل Moodle: ربط الكورس ببيانات التسجيل في Moodle
     */
    public function moodleEnrollments()
    {
        return $this->hasMany(MoodleEnrollment::class, 'moodle_course_id', 'moodle_course_id');
    }

    /**
     * علاقة الكورس بالاجتماعات (مثل Zoom)
     */
    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class, 'course_id');
    }

    /**
     * التقييمات الخاصة بالكورس
     */
    public function rates()
    {
        return $this->hasMany(Rate::class, 'course_id');
    }

    public function rateAvg()
    {
        return $this->rates()->avg('rate');
    }

    /**
     * علاقة الكورس بشهاداته
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }

    /**
     * دالة لتحديث حالة الكورس
     */
    public function markAsCompleted()
    {
        $this->completed = true;
        $this->save();
    }
}
