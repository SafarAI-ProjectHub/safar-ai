<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'unit_id',
        'title',
        'type',
        'start_date',
        'end_date',
        'pass_mark',
        'moodle_quiz_id'
    ];

    /**
     * علاقة الكويز بالوحدة التابع لها
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * علاقة الكويز بالكورس عبر الوحدة
     */
    public function course()
    {
        return $this->unit ? $this->unit->block->course : null;
    }

    /**
     * علاقة الكويز بالأسئلة
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }

    /**
     * علاقة الكويز بالتقييمات
     */
    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'quiz_id');
    }

    /**
     * علاقة Moodle: ربط الكويز بكويزات Moodle
     */
    public function moodleQuiz()
    {
        return $this->hasOne(MoodleQuiz::class, 'id', 'moodle_quiz_id');
    }

    /**
     * ارتباط درجات Moodle بهذا الكويز
     */
    public function moodleGrades()
    {
        return $this->hasMany(MoodleGrade::class, 'quiz_id');
    }

    /**
     * تعيين درجة النجاح للكويز
     */
    public function setPassMark($mark)
    {
        if ($mark >= 0) {
            $this->pass_mark = $mark;
            $this->save();
        }
    }
}
