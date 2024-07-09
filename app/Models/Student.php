<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relationship to manage the courses a student is enrolled in.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
            ->withPivot(['enrollment_date', 'progress']);
    }

    /**
     * Function to enroll a student in a course.
     */
    public function enrollInCourse($courseId, $enrollmentDate = null)
    {
        $this->courses()->attach($courseId, ['enrollment_date' => $enrollmentDate ?? now()]);
    }

    /**
     * Function to update the English proficiency level of a student.
     */
    public function updateProficiencyLevel($level)
    {
        $this->english_proficiency_level = $level;
        $this->save();
    }

    /**
     * Function to record the score of the initial test.
     */
    public function recordInitialTestScore($score)
    {
        $this->initial_assessment_score = $score;
        $this->save();
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'student_units')->withPivot('completed')->withTimestamps();
    }
}