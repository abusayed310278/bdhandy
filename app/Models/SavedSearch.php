<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $fillable = ['customer_id', 'keyword', 'filters'];

    protected $casts = [
        'filters' => 'json',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
