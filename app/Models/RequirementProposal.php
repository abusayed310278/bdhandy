<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementProposal extends Model
{
    protected $fillable = [
        'requirement_id', 'provider_id', 'message', 'proposed_price', 
        'currency_id', 'estimated_arrival_time', 'status'
    ];

    public function requirement()
    {
        return $this->belongsTo(CustomerRequirement::class, 'requirement_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
