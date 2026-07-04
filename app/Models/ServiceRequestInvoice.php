<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequestInvoice extends Model
{
    protected $fillable = [
        'invoice_number', 'service_request_id', 'provider_id', 'customer_id', 'currency_id',
        'subtotal', 'discount_type', 'discount_value', 'discount_amount',
        'tax_label', 'tax_rate', 'tax_amount',
        'adjustment_amount', 'adjustment_note',
        'total', 'payment_status', 'payment_method', 'payment_reference',
        'notes', 'due_date', 'paid_at', 'issued_at',
    ];

    protected $casts = [
        'subtotal'          => 'decimal:2',
        'discount_value'    => 'decimal:2',
        'discount_amount'   => 'decimal:2',
        'tax_rate'          => 'decimal:2',
        'tax_amount'        => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'total'             => 'decimal:2',
        'due_date'          => 'date',
        'paid_at'           => 'datetime',
        'issued_at'         => 'datetime',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function isEditable(): bool
    {
        return in_array($this->payment_status, ['draft', 'pending', 'due']);
    }

    public static function generateNumber(): string
    {
        $count = static::count() + 1;
        return 'INV-' . now()->format('Y') . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
    }

    public static function computeTotals(
        float $subtotal,
        string $discountType,
        ?float $discountValue,
        ?float $taxRate,
        ?float $adjustmentAmount
    ): array {
        $discountAmount = match ($discountType) {
            'fixed'   => min((float) $discountValue, $subtotal),
            'percent' => round($subtotal * ((float) $discountValue / 100), 2),
            default   => 0.0,
        };

        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount     = $taxRate ? round($afterDiscount * ($taxRate / 100), 2) : 0.0;
        $adjustment    = (float) ($adjustmentAmount ?? 0);
        $total         = max(0, $afterDiscount + $taxAmount + $adjustment);

        return [
            'discount_amount' => $discountAmount,
            'tax_amount'      => $taxAmount,
            'total'           => round($total, 2),
        ];
    }
}
