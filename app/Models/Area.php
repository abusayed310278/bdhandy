<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Area extends Model
{
    use HasSlug;

    protected $fillable = ['district_id', 'name', 'slug', 'latitude', 'longitude'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
