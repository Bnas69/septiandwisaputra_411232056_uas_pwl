<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockTransaction;
use App\Repositories\StockRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(protected StockRepository $stockRepo) {}

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->stockRepo->search($filters, $perPage);
    }

    public function createStockIn(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::where('id', $data['product_id'])->lockForUpdate()->firstOrFail();

            $data['stock_code'] = $data['stock_code'] ?? StockTransaction::generateStockCode();
            $data['stock_date'] = $data['stock_date'] ?? today();
            $data['type'] = 'in';

            $stockTx = $this->stockRepo->create($data);

            $product->increment('stock', $data['qty']);

            DashboardService::clearCache();

            return $stockTx;
        });
    }

    public function createStockOut(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $product = Product::where('id', $data['product_id'])->lockForUpdate()->firstOrFail();

            if ($product->stock < $data['qty']) {
                throw new \Exception(
                    "Stok {$product->product_name} tidak mencukupi untuk pengeluaran. Stok tersedia: {$product->stock}"
                );
            }

            $data['stock_code'] = $data['stock_code'] ?? StockTransaction::generateStockCode();
            $data['stock_date'] = $data['stock_date'] ?? today();
            $data['type'] = 'out';

            $stockTx = $this->stockRepo->create($data);

            $product->decrement('stock', $data['qty']);

            DashboardService::clearCache();

            return $stockTx;
        });
    }
}
