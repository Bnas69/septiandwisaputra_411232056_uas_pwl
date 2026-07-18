<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index()
    {
        try {
            $filters = request()->only(['date_from', 'date_to', 'product_id', 'merchant_code']);
            $reportData = $this->reportService->getSalesReport($filters);
            $products = $reportData['products'];
            return view('report.index', compact('products', 'filters'));
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memuat laporan: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'product_id', 'merchant_code']);
            $reportData = $this->reportService->getSalesReport($filters);

            $callback = function () use ($reportData) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['No', 'No. Transaksi', 'Tanggal', 'Merchant', 'Produk', 'Qty', 'Harga', 'Subtotal']);
                $no = 1;
                foreach ($reportData['sales'] as $sale) {
                    fputcsv($file, [
                        $no++,
                        $sale->transaction_number ?? '-',
                        $sale->transaction_date?->format('d/m/Y') ?? '-',
                        $sale->merchant_code ?? '-',
                        $sale->product?->product_name ?? '-',
                        $sale->qty ?? 0,
                        $sale->price ?? 0,
                        $sale->subtotal ?? ($sale->qty ?? 0) * ($sale->price ?? 0),
                    ]);
                }
                fclose($file);
            };

            return Response::stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="laporan-penjualan-' . now()->format('Y-m-d') . '.csv"',
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'product_id', 'merchant_code']);
            $reportData = $this->reportService->getSalesReport($filters);

            return view('report.pdf', [
                'sales' => $reportData['sales'],
                'summary' => $reportData['summary'],
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
