<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionInvoice extends Model
{
    protected $fillable = [
        'subscription_id', 'invoice_number', 'subtotal', 'discount', 
        'total', 'currency_id', 'payment_method', 'payment_status', 'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
