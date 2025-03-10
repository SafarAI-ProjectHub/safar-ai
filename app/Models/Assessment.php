<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'ai_mark',
        'response',
        'teacher_mark',
        'ai_notes',
        'ai_assessment',
        'teacher_review',
        'score',
        'teacher_notes',
        'assessment_date'
    ];
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userResponses()
    {
        return $this->hasMany(UserResponse::class, 'assessment_id');
    }

    public function setAiMark($mark)
    {
        $this->ai_mark = $mark;
        $this->save();
    }

    public function setTeacherMark($mark)
    {
        $this->teacher_mark = $mark;
        $this->save();
    }

    public function addTeacherNotes($notes)
    {
        $this->teacher_notes = $notes;
        $this->save();
    }

    public function addAiNotes($notes)
    {
        $this->ai_notes = $notes;
        $this->save();
    }
}