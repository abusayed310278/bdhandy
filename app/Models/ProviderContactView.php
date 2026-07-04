<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderContactView extends Model
{
    protected $fillable = [
        'provider_profile_id', 'user_id', 'ip_address', 'user_agent',
        'device_type', 'browser', 'platform', 'country', 'city',
        'region', 'latitude', 'longitude'
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
