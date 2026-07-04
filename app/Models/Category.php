<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;


class Category extends Model
{
    use HasSlug;

    protected $fillable = ['slug', 'icon', 'image', 'sort_order', 'status', 'translations'];

    protected $casts = [
        'translations' => 'array',
    ];

    /**
     * Get a translation for a specific locale.
     */
    public function getTranslation($field, $locale)
    {
        if ($field === 'translations' && is_array($this->translations)) {
            return $this->translations[$locale] ?? '';
        }
        return '';
    }

    /**
     * Set a translation for a specific locale.
     */
    public function setTranslation($field, $locale, $value)
    {
        if ($field === 'translations') {
            $translations = $this->translations ?? [];
            $translations[$locale] = $value;
            $this->translations = $translations;
        }
        return $this;
    }


    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(function($model) {
                return $model->getTranslation('translations', 'en');
            })
            ->saveSlugsTo('slug');
    }


    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
