<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_date',
        'expiry_date',
        'payment_method',
        'subscription_type',
        'paypal_plan_id',
        'is_cancelled',
        'is_active',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subscriptions')
            ->withPivot('start_date', 'end_date')
            ->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Accessor for subscription type
    public function getSubscriptionTypeAttribute($value)
    {
        return ucfirst($value);
    }

    //$this -> paypal_plan_id
    public function paypal_plan_id()
    {
        return $this->paypal_plan_id ? $this->paypal_plan_id : 'N/A';
    }

}