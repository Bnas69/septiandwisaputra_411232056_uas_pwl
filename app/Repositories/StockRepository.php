<?php

namespace App\Repositories;

use App\Models\StockTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class StockRepository
{
    public function __construct(protected StockTransaction $model) {}

    public function create(array $data): StockTransaction
    {
        return $this->model->create($data);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyFilters($this->model->with('product'), $filters)
            ->latest('stock_date')
            ->paginate($perPage)
            ->withQueryString();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $escaped = str_replace(['%', '_'], ['\%', '\_'], $filters['search']);
            $query->whereHas('product', function ($q) use ($escaped) {
                $q->where('product_name', 'like', "%{$escaped}%")
                  ->orWhere('product_code', 'like', "%{$escaped}%");
            });
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        return $query;
    }
}
