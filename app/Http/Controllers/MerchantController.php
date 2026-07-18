<?php

namespace App\Http\Controllers;

use App\Repositories\MerchantRepository;

class MerchantController extends Controller
{
    public function __construct(protected MerchantRepository $merchantRepo) {}

    public function index()
    {
        try {
            $merchants = $this->merchantRepo->all();
            $performance = $this->merchantRepo->performance();

            $merchantsWithStats = $merchants->map(function ($merchant) use ($performance) {
                $perf = $performance->firstWhere('merchant_code', $merchant->code);
                $revenueThisMonth = $this->merchantRepo->revenueThisMonth($merchant->code);
                $revenueLastMonth = $this->merchantRepo->revenueLastMonth($merchant->code);
                $growth = $revenueLastMonth > 0
                    ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                    : ($revenueThisMonth > 0 ? 100.0 : 0.0);

                return [
                    'merchant' => $merchant,
                    'transaction_count' => $perf?->transaction_count ?? 0,
                    'total_revenue' => $perf?->total_revenue ?? 0,
                    'avg_transaction' => $perf?->avg_transaction ?? 0,
                    'revenue_this_month' => $revenueThisMonth,
                    'growth' => $growth,
                    'total_products' => $this->merchantRepo->totalProducts($merchant->code),
                ];
            });

            return view('merchants.index', compact('merchantsWithStats'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal memuat data merchant.');
        }
    }

    public function show(string $code)
    {
        try {
            $merchant = $this->merchantRepo->findOrFail($code);
            $performance = $this->merchantRepo->performance()->firstWhere('merchant_code', $code);
            $monthlyRevenue = $this->merchantRepo->monthlyRevenue($code, 6);
            $topProducts = $this->merchantRepo->topProducts($code, 5);
            $recentTransactions = $this->merchantRepo->recentTransactions($code, 5);
            $revenueThisMonth = $this->merchantRepo->revenueThisMonth($code);
            $revenueLastMonth = $this->merchantRepo->revenueLastMonth($code);
            $totalTransactions = $this->merchantRepo->totalTransactions($code);
            $totalProducts = $this->merchantRepo->totalProducts($code);
            $growth = $revenueLastMonth > 0
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                : ($revenueThisMonth > 0 ? 100.0 : 0.0);

            return view('merchants.show', compact(
                'merchant', 'performance', 'monthlyRevenue', 'topProducts',
                'recentTransactions', 'revenueThisMonth', 'totalTransactions',
                'totalProducts', 'growth'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('merchants.index')->with('error', 'Merchant tidak ditemukan.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal memuat detail merchant.');
        }
    }
}
