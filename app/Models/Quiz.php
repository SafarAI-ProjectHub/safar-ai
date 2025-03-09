<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * علاقة مع الدورة عبر الوحدة
     */
    public function course()
    {
        // إما أن تستخدم belongsToThrough (من حزم خارجية)
        // أو ببساطة تصل للدورة عبر:
        return $this->unit ? $this->unit->course() : null;
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'quiz_id');
    }

    public function setPassMark($mark)
    {
        $this->pass_mark = $mark;
        $this->save();
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * ارتباط درجات Moodle بهذا الكويز
     */
    public function moodleGrades()
    {
        // نفترض أن جدول moodle_grades يحوي quiz_id يشير لنفس quiz_id هنا
        return $this->hasMany(MoodleGrade::class, 'quiz_id');
    }
}
