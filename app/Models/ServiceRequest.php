<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id', 'provider_id', 'service_id', 'provider_service_id', 'request_number',
        'title', 'description', 'preferred_date', 'preferred_time',
        'address', 'latitude', 'longitude', 'urgency', 'estimated_price',
        'final_price', 'currency_id', 'payment_status', 'request_status',
        'cancellation_reason', 'completed_at'
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_time' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function providerService()
    {
        return $this->belongsTo(ProviderService::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequestAttachment::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(RequestStatusLog::class);
    }


    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function invoice()
    {
        return $this->hasOne(ServiceRequestInvoice::class);
    }

    public function teamAssignments()
    {
        return $this->hasMany(TeamJobAssignment::class);
    }
}
