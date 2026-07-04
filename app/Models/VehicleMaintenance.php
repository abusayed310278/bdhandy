<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VehicleMaintenance extends Model
{
    protected $table = 'vehicle_maintenance';

    protected $fillable = [
        'vehicle_id', 'business_profile_id', 'maintenance_type', 'description',
        'workshop_name', 'maintenance_date', 'odometer_at_service',
        'next_service_date', 'next_service_odometer_km',
        'cost', 'cost_currency_id', 'receipt_photo', 'status', 'notes',
    ];
    protected $casts = ['maintenance_date' => 'date', 'next_service_date' => 'date'];

    public function vehicle()  { return $this->belongsTo(Vehicle::class); }
    public function business() { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function currency() { return $this->belongsTo(Currency::class, 'cost_currency_id'); }
}
