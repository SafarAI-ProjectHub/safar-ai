<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleGrade extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'user_id',
        'quiz_id',
        'grade'
    ];

    /**
     * علاقة الدرجة بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة الدرجة بالكويز التابع له
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * تعيين درجة الطالب
     */
    public function setGrade($newGrade)
    {
        $this->grade = $newGrade;
        $this->saveQuietly();
    }

    /**
     * استعلام سريع للبحث عن درجات مستخدم معين
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * استعلام سريع للبحث عن درجات كويز معين
     */
    public function scopeByQuiz($query, $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }
}
