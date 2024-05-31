<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function setType($type)
    {
        $this->subscription_type = $type;
        $this->save();
    }

    public function cancel()
    {
        $this->is_cancelled = true;
        $this->save();
    }
}
