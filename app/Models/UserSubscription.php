<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $table = 'user_subscriptions'; // تأكد أن هذا هو اسم الجدول الحقيقي

    protected $fillable = [
        'user_id',
        'subscription_id',
        'status',
        'start_date',
        'next_billing_time',
        'subscriptionId',
        'payment_status', // لو أردت حفظ حالة الدفع في هذا الجدول
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'next_billing_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_subscription_id', 'id');
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class, 'user_subscription_id')->latest();
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'subscriptionId', 'id');
    }
}
