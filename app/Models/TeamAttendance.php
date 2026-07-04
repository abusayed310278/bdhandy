<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamAttendance extends Model
{
    protected $table = 'team_attendance';

    protected $fillable = [
        'team_member_id', 'business_profile_id',
        'clock_in_time', 'clock_in_latitude', 'clock_in_longitude', 'clock_in_address', 'clock_in_photo',
        'clock_out_time', 'clock_out_latitude', 'clock_out_longitude', 'clock_out_photo',
        'total_hours', 'status', 'is_verified', 'notes',
    ];
    protected $casts = [
        'clock_in_time'  => 'datetime',
        'clock_out_time' => 'datetime',
        'is_verified'    => 'boolean',
    ];

    public function member()   { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business() { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }

    public function computeTotalHours(): void
    {
        if ($this->clock_in_time && $this->clock_out_time) {
            $this->total_hours = round($this->clock_in_time->diffInMinutes($this->clock_out_time) / 60, 2);
        }
    }
}
