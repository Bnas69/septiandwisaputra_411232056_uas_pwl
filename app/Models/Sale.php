<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'transaction_number', 'transaction_date', 'merchant_code',
        'product_id', 'qty', 'price', 'subtotal', 'grand_total',
        'payment_method', 'payment_status', 'payment_ref',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'qty' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'qris' => 'QRIS',
            'transfer' => 'Transfer Bank',
            default => 'Tunai',
        };
    }

    public function paymentStatusBadge(): string
    {
        return match ($this->payment_status) {
            'paid' => 'badge-active',
            'pending' => 'badge-warn-stock',
            default => 'badge-low-stock',
        };
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function generateTransactionNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'TRX-' . now()->format('Ymd') . '-';

            $last = self::where('transaction_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('transaction_number')
                ->value('transaction_number');

            if ($last) {
                $sequence = (int) substr($last, -4) + 1;
            } else {
                $sequence = 1;
            }

            return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }
}
