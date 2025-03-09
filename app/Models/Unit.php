<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'unit_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_units')
                    ->withPivot('completed')
                    ->withTimestamps();
    }

    // لو أردت علاقة Moodle section:
    // public function moodleSectionId() { ... }
    // حيث أنك تملك عمود moodle_section_id في جدول units
}
