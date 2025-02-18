<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'paypal_product_id',
        'payment_method',
        'subscription_type',
        'paypal_plan_id',
        'description',
        'is_cancelled',
        'price',
        'is_active',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'subscriptionId', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getSubscriptionTypeAttribute($value)
    {
        return ucfirst($value);
    }
}
