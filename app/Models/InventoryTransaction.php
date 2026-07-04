<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = [
        'inventory_id', 'business_profile_id', 'transaction_type',
        'quantity', 'quantity_before', 'quantity_after',
        'reference_type', 'reference_id', 'performed_by', 'notes',
    ];

    public function inventory() { return $this->belongsTo(Inventory::class); }
    public function business()  { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function performer() { return $this->belongsTo(User::class, 'performed_by'); }
}
