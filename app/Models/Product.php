<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_code', 'product_name', 'category', 'price', 'stock', 'minimum_stock',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function getStockPercentage(): float
    {
        if ($this->minimum_stock <= 0) {
            return $this->stock > 0 ? 100.0 : 0.0;
        }
        return min(100.0, max(0.0, ($this->stock / $this->minimum_stock) * 100));
    }

    public function getEstimatedDaysToEmpty(): int
    {
        $cacheKey = "product_{$this->id}_estimated_days";

        return Cache::remember($cacheKey, now()->addHours(6), function () {
            $avgDailySales = $this->sales()
                ->where('transaction_date', '>=', now()->subDays(30))
                ->sum('qty') / 30;

            if ($avgDailySales <= 0) {
                return 999;
            }

            return (int) ceil($this->stock / $avgDailySales);
        });
    }

    public static function getEstimatedDaysToEmptyBatch(\Illuminate\Database\Eloquent\Collection $products): array
    {
        $productIds = $products->pluck('id')->toArray();
        if (empty($productIds)) {
            return [];
        }

        $salesData = DB::table('sales')
            ->whereIn('product_id', $productIds)
            ->where('transaction_date', '>=', now()->subDays(30))
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $result = [];
        foreach ($products as $product) {
            $totalQty = $salesData->has($product->id) ? $salesData[$product->id]->total_qty : 0;
            $avgDailySales = $totalQty / 30;
            $result[$product->id] = $avgDailySales <= 0 ? 999 : (int) ceil($product->stock / $avgDailySales);
        }

        return $result;
    }


}
