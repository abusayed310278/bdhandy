<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamLocationTracking extends Model
{
    protected $table = 'team_location_tracking';

    public $timestamps = false;

    protected $fillable = [
        'team_member_id', 'business_profile_id',
        'latitude', 'longitude', 'accuracy_meters', 'heading', 'speed_kmh',
        'battery_level', 'is_moving', 'location_time', 'created_at',
    ];
    protected $casts = ['location_time' => 'datetime', 'is_moving' => 'boolean', 'created_at' => 'datetime'];

    public function member()   { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business() { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
}
