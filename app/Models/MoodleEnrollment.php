<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleEnrollment extends Model
{
    use HasFactory;

    // الحقول القابلة للتعبئة
    protected $fillable = [
        'user_id',
        'moodle_course_id',
        'enrolled_at'
    ];

    /**
     * علاقة تسجيل Moodle بالمستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * علاقة تسجيل Moodle بالدورة التعليمية
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'moodle_course_id', 'moodle_course_id');
    }

    /**
     * تنسيق تاريخ التسجيل
     */
    public function getEnrolledAtAttribute($value)
    {
        return $value ? date('Y-m-d H:i', strtotime($value)) : null;
    }

    /**
     * نطاق استعلام للحصول على تسجيلات مستخدم معين
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
