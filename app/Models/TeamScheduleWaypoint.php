<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamScheduleWaypoint extends Model
{
    protected $fillable = [
        'daily_schedule_id', 'job_assignment_id', 'sequence_order',
        'estimated_travel_time_from_previous_minutes', 'estimated_distance_from_previous_km',
    ];

    public function schedule()       { return $this->belongsTo(TeamDailySchedule::class, 'daily_schedule_id'); }
    public function jobAssignment()  { return $this->belongsTo(TeamJobAssignment::class, 'job_assignment_id'); }
}
