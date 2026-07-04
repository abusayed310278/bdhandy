<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'business_profile_id', 'name', 'sku', 'category', 'unit',
        'quantity_in_stock', 'low_stock_threshold', 'unit_cost', 'cost_currency_id',
        'supplier_name', 'supplier_contact', 'notes', 'photo',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            if ($item->photo) {
                (new \App\Http\Controllers\ImageController())->deletePhoto($item->photo);
            }
        });
    }

    public function business()     { return $this->belongsTo(ProviderProfile::class, 'business_profile_id'); }
    public function currency()     { return $this->belongsTo(Currency::class, 'cost_currency_id'); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class); }
    public function usages()       { return $this->hasMany(JobMaterialUsage::class); }

    public function isLowStock(): bool
    {
        return $this->quantity_in_stock <= $this->low_stock_threshold;
    }
}
