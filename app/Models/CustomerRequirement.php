<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerRequirement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'category_id', 'service_id', 'title', 'description', 
        'budget_type', 'budget_fixed', 'budget_min', 'budget_max', 
        'currency_id', 'urgency', 'preferred_date', 'address', 
        'latitude', 'longitude', 'expiry_at', 'visibility_radius_km', 'status'
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'expiry_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequirementAttachment::class, 'requirement_id');
    }

    public function proposals()
    {
        return $this->hasMany(RequirementProposal::class, 'requirement_id');
    }
}
