<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\AuditLog;
use App\Models\Sale;
use App\Services\ProductService;
use App\Services\SalesService;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function __construct(
        protected SalesService $salesService,
        protected ProductService $productService,
    ) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'product_id', 'merchant_code']);
            $sales = $this->salesService->search($filters);
            $products = $this->productService->all()->sortBy('product_name');
            return view('sales.index', compact('sales', 'products', 'filters'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat data penjualan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $products = $this->productService->all()->filter(fn ($p) => !$p->isOutOfStock())->sortBy('product_name');
            $transactionNumber = Sale::generateTransactionNumber();
            return view('sales.create', compact('products', 'transactionNumber'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat form penjualan: ' . $e->getMessage());
        }
    }

    public function store(StoreSaleRequest $request)
    {
        try {
            $sale = $this->salesService->createTransaction($request->validated());
            AuditLog::log('sale_created', 'Penjualan baru: ' . $sale->transaction_number, $sale);
            return redirect()->route('sales.index')->with('success', 'Transaksi penjualan berhasil dibuat.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $sale = Sale::with('product')->findOrFail($id);
            return view('sales.show', compact('sale'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('sales.index')->with('error', 'Data penjualan tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat detail penjualan: ' . $e->getMessage());
        }
    }

    public function receipt(int $id)
    {
        try {
            $sale = Sale::with('product')->findOrFail($id);
            return view('sales.receipt', compact('sale'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('sales.index')->with('error', 'Data penjualan tidak ditemukan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat resi: ' . $e->getMessage());
        }
    }
}
