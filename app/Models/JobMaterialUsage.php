<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobMaterialUsage extends Model
{
    protected $table = 'job_material_usage';

    protected $fillable = [
        'job_assignment_id', 'team_member_id', 'business_profile_id',
        'inventory_id', 'quantity_used', 'unit_cost_at_time', 'cost_currency_id', 'notes', 'logged_at',
    ];
    protected $casts = ['logged_at' => 'datetime'];

    public function jobAssignment() { return $this->belongsTo(TeamJobAssignment::class, 'job_assignment_id'); }
    public function member()        { return $this->belongsTo(TeamMember::class, 'team_member_id'); }
    public function business()      { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function inventory()     { return $this->belongsTo(Inventory::class); }
    public function currency()      { return $this->belongsTo(Currency::class, 'cost_currency_id'); }
}
