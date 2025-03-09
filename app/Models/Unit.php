<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'block_id',
        'title',
        'summary',
        'position',
        'moodle_unit_id',
        'visibility'
    ];

    /**
     * علاقة الوحدة بالبلوك التابع لها
     */
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    /**
     * علاقة الوحدة بالكويزات
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'unit_id');
    }

    /**
     * علاقة الوحدة بالدروس
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'unit_id');
    }

    /**
     * علاقة الوحدة بالأنشطة
     */
    public function activities()
    {
        return $this->hasMany(Activity::class, 'unit_id');
    }

    /**
     * علاقة Moodle: ربط الوحدة بوحدات Moodle
     */
    public function moodleUnit()
    {
        return $this->hasOne(MoodleUnit::class, 'id', 'moodle_unit_id');
    }
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_units')
                    ->withPivot('completed')
                    ->withTimestamps();
    }
}
