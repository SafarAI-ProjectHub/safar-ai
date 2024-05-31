<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelTestAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_test_question_id',
        'user_id',
        'response',
        'correct',
        'ai_review',
        'teacher_review',
    ];

    public function question()
    {
        return $this->belongsTo(LevelTestQuestion::class, 'level_test_question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}