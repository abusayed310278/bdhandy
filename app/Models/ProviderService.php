<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderService extends Model
{
    protected $fillable = [
        'provider_profile_id', 'service_id', 'title', 'description', 
        'pricing_type', 'price_fixed', 'price_min', 'price_max', 
        'currency_id', 'duration_minutes', 'is_emergency', 'status'
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
