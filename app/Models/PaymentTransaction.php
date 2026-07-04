<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id', 'invoice_id', 'gateway', 'transaction_id', 
        'amount', 'currency_id', 'status', 'gateway_response', 'paid_at'
    ];

    protected $casts = [
        'gateway_response' => 'json',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
