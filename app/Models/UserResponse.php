<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model
{
    use HasFactory;

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markCorrect($isCorrect)
    {
        $this->correct = $isCorrect;
        $this->save();
    }

    public function addAiReview($review)
    {
        $this->ai_review = $review;
        $this->save();
    }

    public function addTeacherReview($review)
    {
        $this->teacher_review = $review;
        $this->save();
    }
}
