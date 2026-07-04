<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class VehicleAssignment extends Model
{
    protected $fillable = [
        'vehicle_id', 'team_member_id', 'business_profile_id',
        'assigned_by', 'assigned_at', 'returned_at',
        'odometer_at_assignment', 'odometer_at_return', 'status', 'notes',
    ];
    protected $casts = ['assigned_at' => 'datetime', 'returned_at' => 'datetime'];

    public function vehicle()     { return $this->belongsTo(Vehicle::class); }
    public function member()      { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business()    { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function assignedBy()  { return $this->belongsTo(User::class, 'assigned_by'); }
}
