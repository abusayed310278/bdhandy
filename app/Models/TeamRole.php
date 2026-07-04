<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeamRole extends Model
{
    protected $fillable = ['business_profile_id', 'role_name', 'permissions', 'is_default'];
    protected $casts = ['permissions' => 'array', 'is_default' => 'boolean'];

    public function business() { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function members()  { return $this->hasMany(TeamMember::class); }
}
