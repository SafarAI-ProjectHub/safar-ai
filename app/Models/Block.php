<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $table = 'blocks';

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'course_id',
        'name',
        'description',
        'position',
        'moodle_section_id',
        'visibility'
    ];

    /**
     * علاقة البلوك بالكورس التابع له
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * علاقة البلوك بوحداته (Units)
     */
    public function units()
    {
        return $this->hasMany(Unit::class, 'block_id');
    }

    /**
     * تكامل Moodle: ربط البلوك بالقسم في Moodle
     */
    public function moodleSection()
    {
        return $this->hasOne(MoodleSection::class, 'id', 'moodle_section_id');
    }
}
