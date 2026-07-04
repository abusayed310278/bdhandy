<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderBusinessHour extends Model
{
    protected $fillable = ['provider_profile_id', 'day_of_week_id', 'start_time', 'end_time', 'is_closed'];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function dayOfWeek()
    {
        return $this->belongsTo(DayOfWeek::class);
    }
}
