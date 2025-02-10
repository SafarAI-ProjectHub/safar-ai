<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'icon',
        'type',
        'is_seen',
        'reminder',
        'reminder_time',
        'model_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userMeeting()
    {
        return $this->belongsTo(UserMeeting::class, 'model_id');
    }
}