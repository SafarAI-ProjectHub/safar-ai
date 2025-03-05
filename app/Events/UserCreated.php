<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use Dispatchable, SerializesModels;

    public $user;

    /**
     * إنشاء حدث عند تسجيل مستخدم جديد
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * القنوات التي سيتم بث الحدث فيها
     */
    public function broadcastOn()
    {
        return new Channel('users');
    }
}
