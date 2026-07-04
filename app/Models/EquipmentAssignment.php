<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EquipmentAssignment extends Model
{
    protected $fillable = [
        'equipment_id', 'team_member_id', 'business_profile_id', 'job_assignment_id',
        'assigned_by', 'assigned_at', 'returned_at', 'returned_condition', 'return_notes', 'status',
    ];
    protected $casts = ['assigned_at' => 'datetime', 'returned_at' => 'datetime'];

    public function equipment()     { return $this->belongsTo(Equipment::class); }
    public function member()        { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business()      { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function jobAssignment() { return $this->belongsTo(TeamJobAssignment::class, 'job_assignment_id'); }
    public function assignedBy()    { return $this->belongsTo(User::class, 'assigned_by'); }
}
