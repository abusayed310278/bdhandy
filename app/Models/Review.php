<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['service_request_id', 'customer_id', 'provider_id', 'rating', 'review', 'is_approved'];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function reply()
    {
        return $this->hasOne(ReviewReply::class);
    }
}
