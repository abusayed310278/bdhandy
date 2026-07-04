<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamMemberService extends Model
{
    protected $fillable = ['team_member_id', 'service_id', 'business_profile_id', 'skill_level', 'is_primary'];
    protected $casts = ['is_primary' => 'boolean'];

    public function member()   { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function service()  { return $this->belongsTo(Service::class); }
    public function business() { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
}
