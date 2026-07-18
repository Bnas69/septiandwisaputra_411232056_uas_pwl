<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class StockTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'stock_code', 'stock_date', 'type', 'product_id', 'qty',
    ];

    protected $casts = [
        'stock_date' => 'date',
        'qty' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function generateStockCode(): string
    {
        return DB::transaction(function () {
            $prefix = 'STK-' . now()->format('Ymd') . '-';

            $last = self::where('stock_code', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('stock_code')
                ->value('stock_code');

            if ($last) {
                $sequence = (int) substr($last, -4) + 1;
            } else {
                $sequence = 1;
            }

            return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }


}
