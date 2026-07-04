<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateSystem extends Model
{
    protected $fillable = [
        'user_id', 'referral_code', 'commission_type', 'commission_value', 
        'minimum_payout', 'total_earnings', 'total_paid', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referrals()
    {
        return $this->hasMany(Referral::class, 'affiliate_id');
    }
}
