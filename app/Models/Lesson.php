<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'unit_id',
        'title',
        'content',
        'script',
        'content_type',
        'position',
        'moodle_lesson_id',
        'visibility'
    ];

    /**
     * علاقة الدرس بالوحدة التابع لها
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * علاقة Moodle: ربط الدرس بدروس Moodle
     */
    public function moodleLesson()
    {
        return $this->hasOne(MoodleLesson::class, 'id', 'moodle_lesson_id');
    }
}
