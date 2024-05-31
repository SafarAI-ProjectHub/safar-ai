<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Relationship to manage the courses a teacher is assigned to.
     */

    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    /**
     * Function to assign a course to a teacher.
     */
    public function assignCourse($courseId)
    {
        $this->courses()->syncWithoutDetaching([$courseId]);
    }

    /**
     * Function to remove a course from a teacher.
     */
    public function removeCourse($courseId)
    {
        $this->courses()->detach($courseId);
    }

    /**
     * Function to set a quiz mark by a teacher.
     */

    public function updateExamScore($score)
    {
        $this->exam_score = $score;
        $this->save();
    }
}