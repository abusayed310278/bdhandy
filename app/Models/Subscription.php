<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'provider_id', 'plan_id', 'stripe_checkout_session_id',
        'start_date', 'end_date',
        'next_billing_at', 'notified_3_day_at', 'notified_6_hour_at',
        'auto_renew', 'payment_status', 'subscription_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_billing_at' => 'datetime',
        'notified_3_day_at' => 'datetime',
        'notified_6_hour_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function invoices()
    {
        return $this->hasMany(SubscriptionInvoice::class);
    }
}
