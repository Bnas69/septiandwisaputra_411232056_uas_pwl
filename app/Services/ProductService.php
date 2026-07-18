<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(protected ProductRepository $productRepo) {}

    public function list(?string $search = ''): LengthAwarePaginator
    {
        if ($search) {
            return $this->productRepo->search($search);
        }

        return $this->productRepo->paginate();
    }

    public function all(): Collection
    {
        return $this->productRepo->all();
    }

    public function find(int $id): ?Product
    {
        return $this->productRepo->find($id);
    }

    public function create(array $data): Product
    {
        return $this->productRepo->create($data);
    }

    public function update(Product $product, array $data): Product
    {
        return $this->productRepo->update($product, $data);
    }

    public function delete(Product $product): bool
    {
        return $this->productRepo->delete($product);
    }

    public function categories(): Collection
    {
        return $this->productRepo->categories();
    }
}
