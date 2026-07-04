<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamDailySchedule extends Model
{
    protected $table = 'team_daily_schedule';

    protected $fillable = [
        'team_member_id', 'business_profile_id', 'schedule_date',
        'optimized_route', 'total_distance_km', 'estimated_total_duration_minutes',
        'total_jobs_assigned', 'total_jobs_completed', 'total_earnings_day',
        'is_published', 'is_accepted',
    ];
    protected $casts = [
        'schedule_date'    => 'date',
        'optimized_route'  => 'array',
        'is_published'     => 'boolean',
        'is_accepted'      => 'boolean',
    ];

    public function member()    { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business()  { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function waypoints() { return $this->hasMany(TeamScheduleWaypoint::class, 'daily_schedule_id')->orderBy('sequence_order'); }
}
