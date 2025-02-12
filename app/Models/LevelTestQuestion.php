<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelTestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_test_id',
        'question_text',
        'sub_text',
        'question_type',
        'mark',
        'media_url',
        'media_type',
        'script',
    ];

    public function levelTest()
    {
        return $this->belongsTo(LevelTest::class);
    }

    public function choices()
    {
        return $this->hasMany(LevelTestChoice::class);
    }

    public function assessments()
    {
        return $this->hasMany(LevelTestAssessment::class);
    }
}