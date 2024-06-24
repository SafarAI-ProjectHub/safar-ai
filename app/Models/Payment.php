<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'paypal_subscription_id',
        'subscription_id',
        'user_subscription_id',
        'user_id',
        'amount',
        'payment_status',
        'payment_type',
        'transaction_date',

    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    //user_subscription_id relation 
    public function userSubscription()
    {
        return $this->belongsTo(UserSubscription::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}