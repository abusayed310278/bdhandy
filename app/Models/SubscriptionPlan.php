<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SubscriptionPlan extends Model
{
    use HasSlug;

    protected $fillable = [
        'name', 'slug', 'duration_months', 'price', 'currency_id', 
        'discount_percent', 'lead_limit', 'service_area_limit', 
        'gallery_limit', 'search_rank_weight', 'is_featured', 
        'is_verified_badge_included', 'status', 'target', 'team_member_limit', 'team_features'
    ];

    protected $casts = [
        'is_featured'               => 'boolean',
        'is_verified_badge_included' => 'boolean',
        'team_features'             => 'array',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
