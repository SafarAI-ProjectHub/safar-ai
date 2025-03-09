<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'moodle_category_id',
        'age_group',
        'general_category'
    ];

    /**
     * علاقة التصنيفات بالدورات
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * علاقة التصنيفات الفرعية مع التصنيفات الرئيسية (Parent-Child)
     */
    public function parent()
    {
        return $this->belongsTo(CourseCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CourseCategory::class, 'parent_id');
    }

    /**
     * علاقة Moodle Category
     */
    public function moodleCategory()
    {
        return $this->hasOne(MoodleCategory::class, 'moodle_category_id', 'id');
    }

   
    public function levelTests()
    {
        return $this->hasMany(LevelTest::class, 'age_group_id');
    }
}
