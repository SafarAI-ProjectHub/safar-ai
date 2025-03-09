<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'unit_id',
        'name',
        'type',
        'description',
        'position',
        'moodle_activity_id',
        'visibility'
    ];

    /**
     * علاقة النشاط بالوحدة التابع لها
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * علاقة Moodle: ربط النشاط بأنشطة Moodle
     */
    public function moodleActivity()
    {
        return $this->hasOne(MoodleActivity::class, 'id', 'moodle_activity_id');
    }
}
