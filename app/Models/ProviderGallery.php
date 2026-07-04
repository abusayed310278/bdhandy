<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderGallery extends Model
{
    protected $fillable = ['provider_profile_id', 'url', 'caption', 'sort_order', 'is_video'];

    protected $casts = [
        'is_video' => 'boolean',
    ];

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }
}
