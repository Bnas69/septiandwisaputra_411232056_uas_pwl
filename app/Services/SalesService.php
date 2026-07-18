<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Repositories\SalesRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function __construct(protected SalesRepository $salesRepo) {}

    public function search(array $filters): LengthAwarePaginator
    {
        if (!empty(array_filter($filters))) {
            return $this->salesRepo->search($filters);
        }

        return $this->salesRepo->paginate();
    }

    public function createTransaction(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $product = Product::where('id', $data['product_id'])->lockForUpdate()->firstOrFail();

            if ($product->stock < $data['qty']) {
                throw new \Exception(
                    "Stok {$product->product_name} tidak mencukupi. Stok tersedia: {$product->stock}"
                );
            }

            $data['transaction_number'] = $data['transaction_number'] ?? Sale::generateTransactionNumber();
            $data['price'] = $product->price;
            $data['subtotal'] = $product->price * $data['qty'];
            $data['grand_total'] = $data['subtotal'];
            $data['transaction_date'] = $data['transaction_date'] ?? today();
            $data['payment_method'] = $data['payment_method'] ?? 'cash';
            $data['payment_status'] = 'paid';

            $sale = $this->salesRepo->create($data);

            $product->decrement('stock', $data['qty']);

            DashboardService::clearCache();

            return $sale;
        });
    }
}
