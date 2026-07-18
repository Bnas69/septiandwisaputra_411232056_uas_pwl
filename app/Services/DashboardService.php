<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockTransaction;
use App\Repositories\ProductRepository;
use App\Repositories\SalesRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    protected const RECOMMENDATION_MULTIPLIER = 2;
    protected const CACHE_TTL = 300;

    private const CACHE_KEYS = [
        'dashboard.low_stock',
        'dashboard.recommendations',
        'dashboard.charts',
        'dashboard.revenue_analytics',
    ];

    public static function clearCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }

    public function __construct(
        protected SalesRepository $salesRepo,
        protected ProductRepository $productRepo,
    ) {}

    public function getKpiData(): array
    {
        $currentMonthRevenue = $this->salesRepo->revenueThisMonth();
        $lastMonthRevenue = $this->salesRepo->revenueLastMonth();
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $totalSales = $this->salesRepo->totalTransactions();
        $lastMonthTransactions = Sale::whereMonth('transaction_date', now()->subMonth()->month)
            ->whereYear('transaction_date', now()->subMonth()->year)
            ->count();
        $salesGrowth = $lastMonthTransactions > 0
            ? round((($totalSales - $lastMonthTransactions) / $lastMonthTransactions) * 100, 1)
            : ($totalSales > 0 ? 100.0 : 0.0);

        $totalProducts = $this->productRepo->count();
        $previousMonthProducts = Product::where('created_at', '<', now()->startOfMonth())->count();
        $productGrowth = $previousMonthProducts > 0
            ? round((($totalProducts - $previousMonthProducts) / $previousMonthProducts) * 100, 1)
            : 0;

        $totalStock = $this->productRepo->totalStock();
        $stockMovementThisMonth = StockTransaction::where('stock_date', '>=', now()->startOfMonth())
            ->where('stock_date', '<=', now()->endOfMonth())
            ->sum(DB::raw("CASE WHEN type = 'in' THEN qty ELSE -qty END"));

        $salesQtyThisMonth = Sale::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('qty');
        $netStockChangeThisMonth = $stockMovementThisMonth - $salesQtyThisMonth;

        $stockAtStartOfMonth = $totalStock - $netStockChangeThisMonth;
        $stockGrowth = $stockAtStartOfMonth > 0
            ? round(($netStockChangeThisMonth / $stockAtStartOfMonth) * 100, 1)
            : 0;

        $totalMerchants = $this->salesRepo->totalMerchants();
        $previousMerchants = Sale::where('created_at', '<', now()->startOfMonth())
            ->distinct('merchant_code')
            ->count('merchant_code');
        $merchantGrowth = $previousMerchants > 0
            ? round((($totalMerchants - $previousMerchants) / $previousMerchants) * 100, 1)
            : 0;

        $totalTransactions = $totalSales;
        $transactionGrowth = $salesGrowth;

        return [
            'revenue' => [
                'value' => $currentMonthRevenue,
                'growth' => $revenueGrowth,
                'label' => 'Total Revenue',
                'icon' => 'dollar-sign',
                'color' => 'primary',
            ],
            'sales' => [
                'value' => $totalSales,
                'growth' => $salesGrowth,
                'label' => 'Total Sales',
                'icon' => 'shopping-cart',
                'color' => 'success',
            ],
            'products' => [
                'value' => $totalProducts,
                'growth' => $productGrowth,
                'label' => 'Total Products',
                'icon' => 'package',
                'color' => 'info',
            ],
            'stock' => [
                'value' => $totalStock,
                'growth' => $stockGrowth,
                'label' => 'Total Stock',
                'icon' => 'archive',
                'color' => 'warning',
            ],
            'merchants' => [
                'value' => $totalMerchants,
                'growth' => $merchantGrowth,
                'label' => 'Total Merchants',
                'icon' => 'store',
                'color' => 'secondary',
            ],
            'transactions' => [
                'value' => $totalTransactions,
                'growth' => $transactionGrowth,
                'label' => 'Total Transactions',
                'icon' => 'receipt',
                'color' => 'danger',
            ],
        ];
    }

    public function getTopProducts(int $limit = 5): \Illuminate\Database\Eloquent\Collection
    {
        return $this->salesRepo->topProducts($limit);
    }

    public function getLowStockProducts(): array
    {
        return Cache::remember('dashboard.low_stock', self::CACHE_TTL, function () {
            $lowStock = $this->productRepo->lowStock();
            $daysMap = Product::getEstimatedDaysToEmptyBatch($lowStock);

            return $lowStock->map(function ($product) use ($daysMap) {
                $days = $daysMap[$product->id] ?? 999;

                return [
                    'id' => $product->id,
                    'product_code' => $product->product_code,
                    'name' => $product->product_name,
                    'category' => $product->category,
                    'stock' => $product->stock,
                    'minimum_stock' => $product->minimum_stock,
                    'estimated_days' => $days,
                    'priority' => $product->stock === 0
                        ? 'CRITICAL'
                        : ($product->stock <= ($product->minimum_stock / 2) ? 'HIGH' : 'MEDIUM'),
                    'percentage' => $product->getStockPercentage(),
                ];
            })->toArray();
        });
    }

    public function getRecommendations(): array
    {
        return Cache::remember('dashboard.recommendations', self::CACHE_TTL, function () {
            $recommendations = [];
            $lowStock = $this->productRepo->lowStock();
            $daysMap = Product::getEstimatedDaysToEmptyBatch($lowStock);

            foreach ($lowStock as $product) {
                $days = $daysMap[$product->id] ?? 999;
                $restockAmount = $product->minimum_stock * self::RECOMMENDATION_MULTIPLIER;

                $recommendations[] = [
                    'product' => $product->product_name,
                    'type' => 'restock',
                    'message' => "Segera lakukan restock produk {$product->product_name}",
                    'current_stock' => $product->stock,
                    'minimum_stock' => $product->minimum_stock,
                    'restock_amount' => $restockAmount,
                    'estimated_days' => $days,
                    'priority' => $product->stock === 0
                        ? 'CRITICAL'
                        : ($days <= 3 ? 'HIGH' : 'MEDIUM'),
                    'suggestions' => [
                        "Tambah stok minimal {$restockAmount} unit",
                        'Periksa ketersediaan supplier',
                        'Pertimbangkan promo untuk percepatan penjualan',
                    ],
                ];
            }

            $topProducts = $this->salesRepo->topProducts(3);
            foreach ($topProducts as $top) {
                if ($top->product) {
                    $recommendations[] = [
                        'product' => $top->product->product_name,
                        'type' => 'promotion',
                        'message' => "Produk {$top->product->product_name} memiliki penjualan tinggi",
                        'total_sold' => $top->total_qty,
                        'total_revenue' => $top->total_revenue,
                        'priority' => 'OPPORTUNITY',
                        'suggestions' => [
                            'Tambah stok untuk mengantisipasi lonjakan demand',
                            'Jadikan produk unggulan di semua merchant',
                            'Buat paket bundling dengan produk lain',
                        ],
                    ];
                }
            }

            return $recommendations;
        });
    }

    public function getChartData(): array
    {
        return Cache::remember('dashboard.charts', self::CACHE_TTL, function () {
            return [
                'monthly_trend' => $this->salesRepo->revenueByPeriod('month')->toArray(),
                'top_products' => $this->salesRepo->topProducts(10)->toArray(),
                'merchant_contribution' => $this->salesRepo->merchantPerformance()->toArray(),
                'daily_revenue' => $this->salesRepo->dailyRevenue(30)->toArray(),
            ];
        });
    }

    public function getRecentTransactions(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->salesRepo->recentTransactions(5);
    }

    public function getMerchantPerformance(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->salesRepo->merchantPerformance();
    }

    public function getRevenueAnalytics(): array
    {
        return Cache::remember('dashboard.revenue_analytics', 300, function () {
            return [
                'today' => $this->salesRepo->revenueToday(),
                'week' => $this->salesRepo->revenueThisWeek(),
                'month' => $this->salesRepo->revenueThisMonth(),
                'year' => $this->salesRepo->revenueThisYear(),
                'growth' => $this->salesRepo->salesGrowth(),
            ];
        });
    }

    public function getDeveloperDashboard(): array
    {
        return [
            'kpi' => $this->getKpiData(),
            'topProducts' => $this->getTopProducts(5),
            'lowStock' => $this->getLowStockProducts(),
            'recommendations' => $this->getRecommendations(),
            'chartData' => $this->getChartData(),
            'recentTransactions' => $this->getRecentTransactions(),
        ];
    }

    public function getOwnerDashboard(): array
    {
        $merchantPerformance = $this->getMerchantPerformance();
        $merchantDetails = Merchant::all()->keyBy('code');

        $merchantsWithStats = $merchantPerformance->map(function ($perf) use ($merchantDetails) {
            $m = $merchantDetails->get($perf->merchant_code);
            $revenueThisMonth = (float) Sale::where('merchant_code', $perf->merchant_code)
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('grand_total');
            $revenueLastMonth = (float) Sale::where('merchant_code', $perf->merchant_code)
                ->whereMonth('transaction_date', now()->subMonth()->month)
                ->whereYear('transaction_date', now()->subMonth()->year)
                ->sum('grand_total');
            $growth = $revenueLastMonth > 0
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                : ($revenueThisMonth > 0 ? 100.0 : 0.0);

            return [
                'code' => $perf->merchant_code,
                'name' => $m?->name ?? $perf->merchant_code,
                'icon' => $m?->icon ?? 'store',
                'transaction_count' => $perf->transaction_count,
                'total_revenue' => $perf->total_revenue,
                'avg_transaction' => $perf->avg_transaction,
                'revenue_this_month' => $revenueThisMonth,
                'growth' => $growth,
            ];
        });

        return [
            'kpi' => $this->getKpiData(),
            'topProducts' => $this->getTopProducts(5),
            'lowStock' => $this->getLowStockProducts(),
            'recommendations' => $this->getRecommendations(),
            'chartData' => $this->getChartData(),
            'recentTransactions' => $this->getRecentTransactions(),
            'merchantPerformance' => $merchantPerformance,
            'merchantsWithStats' => $merchantsWithStats,
            'revenueAnalytics' => $this->getRevenueAnalytics(),
        ];
    }

    public function getPegawaiDashboard(): array
    {
        $kpi = collect($this->getKpiData())->only(['products', 'stock', 'transactions'])->toArray();

        return [
            'kpi' => $kpi,
            'topProducts' => $this->getTopProducts(5),
            'lowStock' => $this->getLowStockProducts(),
            'recentTransactions' => $this->getRecentTransactions(),
        ];
    }

    public function getUserDashboard(): array
    {
        $user = Auth::user();

        $userTransactions = Sale::where('user_id', $user->id);
        $totalTransactions = $userTransactions->count();
        $totalSpending = (float) $userTransactions->sum('grand_total');
        $lastTransaction = Sale::where('user_id', $user->id)->latest()->first();
        $avgTransaction = $totalTransactions > 0 ? $totalSpending / $totalTransactions : 0;

        return [
            'user' => $user,
            'recentTransactions' => $this->salesRepo->recentTransactions(5),
            'userStats' => [
                'total_transactions' => $totalTransactions,
                'total_spending' => $totalSpending,
                'avg_transaction' => $avgTransaction,
                'last_transaction_date' => $lastTransaction?->transaction_date,
            ],
        ];
    }
}
