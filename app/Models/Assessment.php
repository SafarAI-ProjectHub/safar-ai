<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'response',
        'total_score',
        'ai_mark',
        'teacher_mark',
        'teacher_notes',
        'ai_notes',
        'ai_assessment',
        'teacher_review',
        'assessment_date'
    ];

    /**
     * علاقة التقييم بالكويز التابع له
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * علاقة التقييم بالمستخدم الذي تم تقييمه
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * ضبط الدرجة النهائية للتقييم
     */
    public function setTotalScore($score)
    {
        $this->total_score = $score;
        $this->saveQuietly();
    }

    /**
     * ضبط درجة الذكاء الاصطناعي
     */
    public function setAiMark($mark)
    {
        $this->ai_mark = $mark;
        $this->saveQuietly();
    }

    /**
     * ضبط درجة المعلم
     */
    public function setTeacherMark($mark)
    {
        $this->teacher_mark = $mark;
        $this->saveQuietly();
    }

    /**
     * إضافة ملاحظات المعلم
     */
    public function addTeacherNotes($notes)
    {
        $this->teacher_notes = $notes;
        $this->saveQuietly();
    }

    /**
     * إضافة ملاحظات الذكاء الاصطناعي
     */
    public function addAiNotes($notes)
    {
        $this->ai_notes = $notes;
        $this->saveQuietly();
    }
}
