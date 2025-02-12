<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }
    public function levelTests()
    {
        return $this->hasMany(LevelTest::class, 'age_group_id');
    }
}