<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayOfWeek extends Model
{
    protected $fillable = ['translations'];

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
}
