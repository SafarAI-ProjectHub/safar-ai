<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'question_id',
        'user_id',
        'attempt_id',
        'response',
        'is_correct',
        'ai_review',
        'teacher_review'
    ];

    /**
     * علاقة الإجابة بالسؤال التابع له
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * علاقة الإجابة بالمستخدم الذي أجاب عليها
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ضبط صحة الإجابة
     */
    public function markCorrect($isCorrect)
    {
        $this->is_correct = $isCorrect;
        $this->save();
    }

    /**
     * إضافة مراجعة الذكاء الاصطناعي
     */
    public function addAiReview($review)
    {
        $this->ai_review = $review;
        $this->save();
    }

    /**
     * إضافة مراجعة المدرس
     */
    public function addTeacherReview($review)
    {
        $this->teacher_review = $review;
        $this->save();
    }
}
