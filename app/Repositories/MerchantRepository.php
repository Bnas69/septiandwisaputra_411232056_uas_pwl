<?php

namespace App\Repositories;

use App\Models\Merchant;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MerchantRepository
{
    public function __construct(protected Merchant $model) {}

    public function all(): Collection
    {
        return $this->model->orderBy('code')->get();
    }

    public function find(string $code): ?Merchant
    {
        return $this->model->where('code', $code)->first();
    }

    public function findOrFail(string $code): Merchant
    {
        return $this->model->where('code', $code)->firstOrFail();
    }

    public function performance(): Collection
    {
        return Sale::select(
            'merchant_code',
            DB::raw('COUNT(id) as transaction_count'),
            DB::raw('SUM(grand_total) as total_revenue'),
            DB::raw('AVG(grand_total) as avg_transaction')
        )
        ->groupBy('merchant_code')
        ->orderByDesc('total_revenue')
        ->get();
    }

    public function monthlyRevenue(string $merchantCode, int $months = 6): Collection
    {
        $startDate = now()->subMonths($months)->startOfMonth();

        return Sale::select(
            DB::raw("DATE_FORMAT(transaction_date, '%Y-%m') as period"),
            DB::raw('SUM(grand_total) as revenue'),
            DB::raw('COUNT(id) as transaction_count'),
            DB::raw('SUM(qty) as total_qty')
        )
        ->where('merchant_code', $merchantCode)
        ->where('transaction_date', '>=', $startDate)
        ->groupBy('period')
        ->orderBy('period')
        ->get();
    }

    public function topProducts(string $merchantCode, int $limit = 5): Collection
    {
        return Sale::select(
            'product_id',
            DB::raw('SUM(qty) as total_qty'),
            DB::raw('SUM(grand_total) as total_revenue')
        )
        ->with('product')
        ->where('merchant_code', $merchantCode)
        ->groupBy('product_id')
        ->orderByDesc('total_qty')
        ->limit($limit)
        ->get();
    }

    public function recentTransactions(string $merchantCode, int $limit = 5): Collection
    {
        return Sale::with('product')
            ->where('merchant_code', $merchantCode)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function revenueThisMonth(string $merchantCode): float
    {
        return (float) Sale::where('merchant_code', $merchantCode)
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('grand_total');
    }

    public function revenueLastMonth(string $merchantCode): float
    {
        $lastMonth = now()->subMonth();
        return (float) Sale::where('merchant_code', $merchantCode)
            ->whereMonth('transaction_date', $lastMonth->month)
            ->whereYear('transaction_date', $lastMonth->year)
            ->sum('grand_total');
    }

    public function totalTransactions(string $merchantCode): int
    {
        return (int) Sale::where('merchant_code', $merchantCode)->count();
    }

    public function totalProducts(string $merchantCode): int
    {
        return (int) Sale::where('merchant_code', $merchantCode)
            ->distinct('product_id')
            ->count('product_id');
    }
}
