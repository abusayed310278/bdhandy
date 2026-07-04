<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VehicleFuelRecord extends Model
{
    protected $fillable = [
        'vehicle_id', 'team_member_id', 'business_profile_id',
        'fuel_date', 'liters_filled', 'cost_per_liter', 'total_cost', 'cost_currency_id',
        'odometer_reading', 'station_name', 'receipt_photo',
    ];
    protected $casts = ['fuel_date' => 'date'];

    public function vehicle()  { return $this->belongsTo(Vehicle::class); }
    public function member()   { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business() { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function currency() { return $this->belongsTo(Currency::class, 'cost_currency_id'); }
}
