<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRequest;
use App\Models\AuditLog;
use App\Models\StockTransaction;
use App\Services\ProductService;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService,
        protected ProductService $productService,
    ) {}

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'type', 'product_id']);
            $stocks = $this->stockService->search($filters);

            return view('stock.index', compact('stocks'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat data stok: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $products = $this->productService->all()->sortBy('product_name');
            $stockCode = StockTransaction::generateStockCode();
            return view('stock.create', compact('products', 'stockCode'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat form stok: ' . $e->getMessage());
        }
    }

    public function store(StoreStockRequest $request)
    {
        try {
            $data = $request->validated();

            if (($data['type'] ?? null) === 'out') {
                $stockTx = $this->stockService->createStockOut($data);
                AuditLog::log('stock_out', 'Barang keluar: ' . $stockTx->stock_code, $stockTx);
                return redirect()->route('stock.index')->with('success', 'Barang keluar berhasil dicatat.');
            }

            $stockTx = $this->stockService->createStockIn($data);
            AuditLog::log('stock_in', 'Barang masuk: ' . $stockTx->stock_code, $stockTx);
            return redirect()->route('stock.index')->with('success', 'Barang masuk berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
