<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', 'iso_code', 'phone_code', 'currency_code', 'currency_symbol', 
        'locale', 'direction', 'status'
    ];

    public function divisions()
    {
        return $this->hasMany(Division::class);
    }
}
