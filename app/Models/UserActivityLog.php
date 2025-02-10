<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $table = 'user_activity_logs';

    protected $fillable = [
        'user_id',
        'login_time',
        'logout_time',
        'last_activity_time',
        'session_status',
        'total_active_time',
        'current_activity_start',
        'previous_activity_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}