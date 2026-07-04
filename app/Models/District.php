<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class District extends Model
{
    use HasSlug;

    protected $fillable = ['division_id', 'name', 'slug'];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('name')->saveSlugsTo('slug');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
