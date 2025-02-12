<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'exam_type',
        'active',
        'age_group_id'
    ];

    public function questions()
    {
        return $this->hasMany(LevelTestQuestion::class);
    }
    //use App\Models\CourseCategory;

    public function ageGroup()
    {
        return $this->belongsTo(CourseCategory::class, 'age_group_id');
    }
}