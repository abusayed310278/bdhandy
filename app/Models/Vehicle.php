<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'business_profile_id', 'vehicle_type', 'make', 'model', 'year', 'color',
        'plate_number', 'vin', 'registration_expiry', 'insurance_expiry', 'fitness_expiry',
        'fuel_type', 'fuel_tank_capacity_liters', 'current_odometer_km', 'status', 'photo', 'notes',
    ];
    protected $casts = [
        'registration_expiry' => 'date',
        'insurance_expiry'    => 'date',
        'fitness_expiry'      => 'date',
    ];

    public function business()     { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function assignments()  { return $this->hasMany(VehicleAssignment::class); }
    public function fuelRecords()  { return $this->hasMany(VehicleFuelRecord::class); }
    public function maintenance()  { return $this->hasMany(VehicleMaintenance::class); }

    public function currentAssignment()
    {
        return $this->hasOne(VehicleAssignment::class)->where('status', 'active');
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        return collect([$this->registration_expiry, $this->insurance_expiry, $this->fitness_expiry])
            ->filter()
            ->some(fn($date) => $date->lte(now()->addDays($days)));
    }
}
