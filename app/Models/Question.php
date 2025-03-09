<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function setMark($mark)
    {
        $this->mark = $mark;
        $this->save();
    }

    public function assignMedia($mediaId)
    {
        $this->media_id = $mediaId;
        $this->save();
    }

    public function choices()
    {
        return $this->hasMany(Choice::class);
    }

    public function userResponses()
    {
        return $this->hasMany(UserResponse::class, 'question_id');
    }
}
