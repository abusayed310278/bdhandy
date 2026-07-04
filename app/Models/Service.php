<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Service extends Model
{
    use HasSlug;

    protected $fillable = ['category_id', 'slug', 'image', 'status', 'translations'];

    protected $casts = [
        'translations' => 'array',
    ];

    /**
     * Get a translation for a specific locale.
     */
    public function getTranslation($field, $locale)
    {
        if ($field === 'translations' && is_array($this->translations)) {
            return $this->translations[$locale] ?? [];
        }
        return [];
    }

    /**
     * Set a translation for a specific locale.
     */
    public function setTranslation($field, $locale, $data)
    {
        if ($field === 'translations') {
            $translations = $this->translations ?? [];
            $translations[$locale] = $data;
            $this->translations = $translations;
        }
        return $this;
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                $trans = $model->getTranslation('translations', 'en');
                return $trans['name'] ?? 'service';
            })
            ->saveSlugsTo('slug');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }
}
