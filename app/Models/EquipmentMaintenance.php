<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EquipmentMaintenance extends Model
{
    protected $table = 'equipment_maintenance';

    protected $fillable = [
        'equipment_id', 'business_profile_id', 'maintenance_type', 'description',
        'performed_by', 'cost', 'cost_currency_id', 'maintenance_date',
        'next_maintenance_date', 'status', 'notes',
    ];
    protected $casts = ['maintenance_date' => 'date', 'next_maintenance_date' => 'date'];

    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function business()  { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function currency()  { return $this->belongsTo(Currency::class, 'cost_currency_id'); }
}
