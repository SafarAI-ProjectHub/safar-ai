<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoomMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'meeting_id',
        'topic',
        'agenda',
        'start_time',
        'duration',
        'password',
        'join_url'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}