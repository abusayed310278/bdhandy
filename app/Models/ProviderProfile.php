<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Currency;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProviderProfile extends Model
{
    use HasSlug, SoftDeletes;

    protected $fillable = [
        'user_id', 'provider_type', 'business_name', 'tagline', 'slug', 'logo', 'cover_photo',
        'description', 'years_of_experience', 'experience_level', 'languages',
        'emergency_available', 'is_verified', 'verification_status', 'is_featured',
        'primary_phone', 'whatsapp_number', 'website', 'facebook_url',
        'instagram_url', 'youtube_url', 'status', 'currency_id'
    ];

    protected $casts = [
        'languages' => 'json',
        'emergency_available' => 'boolean',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('business_name')
            ->saveSlugsTo('slug');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function documents()
    {
        return $this->hasMany(ProviderDocument::class);
    }

    public function teamRoles()      { return $this->hasMany(TeamRole::class, 'business_profile_id'); }
    public function teamMembers()    { return $this->hasMany(TeamMember::class, 'business_profile_id'); }
    public function equipment()      { return $this->hasMany(Equipment::class, 'business_profile_id'); }
    public function inventory()      { return $this->hasMany(Inventory::class, 'business_profile_id'); }
    public function vehicles()       { return $this->hasMany(Vehicle::class, 'business_profile_id'); }

    public function serviceAreas()
    {
        return $this->hasMany(ProviderServiceArea::class);
    }

    public function services()
    {
        return $this->hasMany(ProviderService::class);
    }

    public function gallery()
    {
        return $this->hasMany(ProviderGallery::class);
    }

    public function businessHours()
    {
        return $this->hasMany(ProviderBusinessHour::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'provider_id', 'user_id')
            ->where('is_approved', true);
    }

    public function getAvgRatingAttribute(): ?float
    {
        // Prefer pre-computed value from withAvg('reviews','rating')
        if (array_key_exists('reviews_avg_rating', $this->attributes)) {
            return $this->attributes['reviews_avg_rating'] !== null
                ? round((float) $this->attributes['reviews_avg_rating'], 1)
                : null;
        }
        $avg = $this->reviews()->avg('rating');
        return $avg ? round((float) $avg, 1) : null;
    }

    public function getTotalReviewsAttribute(): int
    {
        if (array_key_exists('reviews_count', $this->attributes)) {
            return (int) $this->attributes['reviews_count'];
        }
        return $this->reviews()->count();
    }

    public function hasVerifiedBadge(): bool
    {
        return $this->user?->hasVerifiedBadge() ?? false;
    }

    public function getPriceRange(): ?string
    {
        $services = $this->services;
        if ($services->isEmpty()) {
            return null;
        }

        $min = null;
        $max = null;
        $currencySymbol = '৳';

        foreach ($services as $service) {
            $sym = $service->currency?->symbol ?? '৳';
            if ($sym) {
                $currencySymbol = $sym;
            }

            if ($service->price_fixed !== null && $service->price_fixed > 0) {
                $val = (float) $service->price_fixed;
                if ($min === null || $val < $min) {
                    $min = $val;
                }
                if ($max === null || $val > $max) {
                    $max = $val;
                }
            }

            if ($service->price_min !== null && $service->price_min > 0) {
                $val = (float) $service->price_min;
                if ($min === null || $val < $min) {
                    $min = $val;
                }
            }

            if ($service->price_max !== null && $service->price_max > 0) {
                $val = (float) $service->price_max;
                if ($max === null || $val > $max) {
                    $max = $val;
                }
            }
        }

        if ($min === null && $max === null) {
            return null;
        }

        if ($min === null) {
            $min = $max;
        }
        if ($max === null) {
            $max = $min;
        }

        if ($min === $max) {
            return $currencySymbol . ' ' . number_format($min);
        }

        return $currencySymbol . ' ' . number_format($min) . ' - ' . number_format($max);
    }
}

