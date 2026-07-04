<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['date_of_holiday', 'reason', 'provider_profile_id'];

    protected $casts = [
        'date_of_holiday' => 'date',
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}
