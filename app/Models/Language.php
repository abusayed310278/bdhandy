<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['name', 'code', 'image', 'is_default', 'status'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function getActiveLanguages()
    {
        return self::where('status', 'active')->orderBy('is_default', 'desc')->get();
    }
}
