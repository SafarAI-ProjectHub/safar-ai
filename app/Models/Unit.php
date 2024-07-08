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
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_units')->withPivot('completed')->withTimestamps();
    }
}