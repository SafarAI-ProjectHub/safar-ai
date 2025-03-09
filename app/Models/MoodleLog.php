<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodleLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * هنا يمكنك إضافة أي علاقة أو لا.
     * إن كان الجدول لا يحوي user_id أو course_id، لا حاجة لعلاقات.
     * لكن إذا أضفت عمود user_id في moodle_logs، يمكنك إضافة علاقة:
     * public function user()
     * {
     *     return $this->belongsTo(User::class);
     * }
     */

}
