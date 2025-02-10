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
    // add function to libk quiz to course by unit
    public function course()
    {
        return $this->unit->course();
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

}