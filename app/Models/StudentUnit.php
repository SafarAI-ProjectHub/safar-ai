<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUnit extends Model
{
    use HasFactory;

    protected $table = 'student_units';
    protected $guarded = [];

    // at each insert hould insert the timestamp as now and on each update should update the updated_at timestamp
    public $timestamps = true;

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}