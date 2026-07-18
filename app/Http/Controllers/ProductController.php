<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\AuditLog;
use App\Services\DashboardService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function index(Request $request)
    {
        try {
            $products = $this->productService->list($request->search);
            return view('products.index', compact('products'));
        } catch (\Throwable $e) {
            report($e);
            return redirect()->route('dashboard')->with('error', 'Gagal memuat data produk.');
        }
    }

    public function create()
    {
        try {
            $categories = $this->productService->categories();
            return view('products.create', compact('categories'));
        } catch (\Throwable $e) {
            return redirect()->route('products.index')->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->create($request->validated());
            AuditLog::log('product_created', 'Produk baru: ' . $product->product_name, $product);
            DashboardService::clearCache();
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }
            $categories = $this->productService->categories();
            return view('products.edit', compact('product', 'categories'));
        } catch (\Throwable $e) {
            return redirect()->route('products.index')->with('error', 'Gagal memuat form: ' . $e->getMessage());
        }
    }

    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }
            $oldData = $product->only('product_name', 'price', 'stock', 'minimum_stock');
            $this->productService->update($product, $request->validated());
            $product->refresh();
            AuditLog::log('product_updated', 'Produk diperbarui: ' . $product->product_name, $product, [
                'old_values' => $oldData,
                'new_values' => $request->validated(),
            ]);
            DashboardService::clearCache();
            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $product = $this->productService->find($id);
            if (!$product) {
                return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
            }
            if ($product->sales()->count() > 0) {
                return back()->with('error', 'Produk tidak dapat dihapus karena memiliki riwayat penjualan.');
            }
            AuditLog::log('product_deleted', 'Produk dihapus: ' . $product->product_name, $product);
            $this->productService->delete($product);
            DashboardService::clearCache();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Tidak dapat menghapus produk yang masih memiliki data transaksi.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
}
