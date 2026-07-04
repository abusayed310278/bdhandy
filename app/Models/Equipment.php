<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'business_profile_id', 'name', 'code', 'category', 'brand', 'model',
        'serial_number', 'purchase_date', 'purchase_price', 'purchase_currency_id',
        'condition', 'status', 'notes', 'photo',
    ];
    protected $casts = ['purchase_date' => 'date'];

    public function business()     { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function currency()     { return $this->belongsTo(Currency::class, 'purchase_currency_id'); }
    public function assignments()  { return $this->hasMany(EquipmentAssignment::class); }
    public function maintenance()  { return $this->hasMany(EquipmentMaintenance::class); }

    public function currentAssignment()
    {
        return $this->hasOne(EquipmentAssignment::class)->where('status', 'assigned');
    }
}
