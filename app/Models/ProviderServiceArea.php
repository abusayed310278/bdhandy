<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderServiceArea extends Model
{
    protected $fillable = [
        'provider_profile_id', 'country_id', 'division_id', 
        'district_id', 'area_id', 'address', 'latitude', 
        'longitude', 'radius_km'
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
