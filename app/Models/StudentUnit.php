<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUnit extends Model
{
    use HasFactory;

    protected $table = 'student_units';
    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}