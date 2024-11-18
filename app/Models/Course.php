<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_student', 'course_id', 'student_id')
            ->withPivot('enrollment_date', 'progress')
            ->withTimestamps();
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'course_id');
    }

    public function assignToCategory($categoryId)
    {
        $this->category_id = $categoryId;
        $this->save();
    }

    public function zoomMeetings()
    {
        return $this->hasMany(ZoomMeeting::class);
    }

    public function rates()
    {
        return $this->hasMany(Rate::class);
    }

    public function RateAvg()
    {
        return $this->rates()->avg('rate');
    }

    public function courseStudents()
    {
        return $this->hasMany(CourseStudent::class, 'course_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }
}