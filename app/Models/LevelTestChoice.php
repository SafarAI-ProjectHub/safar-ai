<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelTestChoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_test_question_id',
        'choice_text',
        'is_correct',
    ];

    public function question()
    {
        return $this->belongsTo(LevelTestQuestion::class, 'level_test_question_id');
    }
}