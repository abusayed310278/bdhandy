<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamJobAssignment extends Model
{
    protected $fillable = [
        'team_member_id', 'service_request_id', 'business_profile_id',
        'assignment_type', 'assigned_by', 'assigned_at',
        'scheduled_start_time', 'scheduled_end_time', 'status',
        'travel_time_minutes', 'actual_travel_time_minutes', 'work_duration_minutes',
        'distance_traveled_km', 'arrived_at_location', 'started_at', 'completed_at',
        'customer_rating', 'customer_feedback', 'commission_earned', 'commission_currency_id',
    ];
    protected $casts = [
        'assigned_at'           => 'datetime',
        'scheduled_start_time'  => 'datetime',
        'scheduled_end_time'    => 'datetime',
        'arrived_at_location'   => 'datetime',
        'started_at'            => 'datetime',
        'completed_at'          => 'datetime',
    ];

    public function member()          { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function request()         { return $this->belongsTo(ServiceRequest::class, 'service_request_id'); }
    public function business()        { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function assignedBy()      { return $this->belongsTo(User::class, 'assigned_by'); }
    public function commissionCurrency() { return $this->belongsTo(Currency::class, 'commission_currency_id'); }
    public function waypoint()        { return $this->hasOne(TeamScheduleWaypoint::class, 'job_assignment_id'); }
    public function materialUsage()   { return $this->hasMany(JobMaterialUsage::class, 'job_assignment_id'); }
    public function equipmentAssignments() { return $this->hasMany(EquipmentAssignment::class, 'job_assignment_id'); }
}
