<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meeting_id',
        'course_id',
        'topic',
        'agenda',
        'start_time',
        'duration',
        'password',
        'join_url',
    ];
    protected $casts = [
        'start_time' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function userMeetings()
    {
        return $this->hasMany(UserMeeting::class);
    }
}