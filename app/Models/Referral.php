<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    protected $fillable = [
        'affiliate_id', 'referred_user_id', 'subscription_id', 
        'commission_amount', 'commission_status', 'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->belongsTo(AffiliateSystem::class, 'affiliate_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
