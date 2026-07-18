<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function __construct(protected Product $model) {}

    public function all(): Collection
    {
        return $this->model->orderBy('product_name')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('stock')->orderBy('product_name')->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        $escaped = str_replace(['%', '_'], ['\%', '\_'], $query);
        return $this->model
            ->where(function ($q) use ($escaped) {
                $q->where('product_name', 'like', "%{$escaped}%")
                  ->orWhere('product_code', 'like', "%{$escaped}%")
                  ->orWhere('category', 'like', "%{$escaped}%");
            })
            ->orderBy('stock')
            ->orderBy('product_name')
            ->paginate($perPage);
    }

    public function find(int $id): ?Product
    {
        return $this->model->find($id);
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product->fresh();
    }

    public function delete(Product $product): bool
    {
        return $product->delete();
    }

    public function lowStock(): Collection
    {
        return $this->model->whereColumn('stock', '<=', 'minimum_stock')
            ->where('minimum_stock', '>', 0)
            ->orderBy('stock')
            ->get();
    }

    public function count(): int
    {
        return $this->model->count();
    }

    public function totalStock(): int
    {
        return (int) $this->model->sum('stock');
    }

    public function categories(): \Illuminate\Support\Collection
    {
        return $this->model->pluck('category')->unique()->sort()->values();
    }
}
