<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'meeting_id',
        'meeting_title',
        'meeting_description',
        'meeting_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function meeting()
    {
        return $this->belongsTo(ZoomMeeting::class);
    }
}