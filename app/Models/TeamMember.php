<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'business_profile_id', 'team_role_id', 'full_name', 'email', 'phone',
        'profile_photo', 'nid_number', 'nid_photo', 'passport_number', 'passport_photo',
        'employee_code', 'designation', 'joining_date', 'renewal_date', 'status', 'compensation_type',
    ];
    protected $casts = [
        'joining_date' => 'date',
        'renewal_date' => 'date',
    ];

    public function user()          { return $this->belongsTo(User::class); }
    public function business()      { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function role()          { return $this->belongsTo(TeamRole::class, 'team_role_id'); }
    public function services()      { return $this->hasMany(TeamMemberService::class); }
    public function compensation()  { return $this->hasMany(TeamCompensation::class); }
    public function attendance()    { return $this->hasMany(TeamAttendance::class); }
    public function locations()     { return $this->hasMany(TeamLocationTracking::class); }
    public function assignments()   { return $this->hasMany(TeamJobAssignment::class); }
    public function schedules()     { return $this->hasMany(TeamDailySchedule::class); }
    public function equipmentAssignments() { return $this->hasMany(EquipmentAssignment::class); }
    public function vehicleAssignments()   { return $this->hasMany(VehicleAssignment::class); }

    public function currentCompensation()
    {
        return $this->hasOne(TeamCompensation::class)->whereNull('effective_to');
    }

    public function activeVehicle()
    {
        return $this->hasOne(VehicleAssignment::class)->where('status', 'active');
    }

    public function hasTeamPermission(string $group, string $permission): bool
    {
        $this->loadMissing('role');
        $perms = $this->role?->permissions ?? [];
        return (bool) ($perms[$group][$permission] ?? false);
    }
}
