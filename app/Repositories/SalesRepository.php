<?php

namespace App\Repositories;

use App\Models\Sale;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SalesRepository
{
    public function __construct(protected Sale $model) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('product')->latest('transaction_date')->paginate($perPage);
    }

    public function search(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->applyFilters($this->model->with('product'), $filters)
            ->latest('transaction_date')
            ->paginate($perPage);
    }

    public function create(array $data): Sale
    {
        return $this->model->create($data);
    }

    public function totalTransactions(): int
    {
        return $this->model->count('id');
    }

    public function totalQuantitySold(): int
    {
        return (int) $this->model->sum('qty');
    }

    public function revenueToday(): float
    {
        return (float) $this->model->whereDate('transaction_date', today())->sum('grand_total');
    }

    public function revenueThisWeek(): float
    {
        return (float) $this->model
            ->whereBetween('transaction_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('grand_total');
    }

    public function revenueThisMonth(): float
    {
        return (float) $this->model
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('grand_total');
    }

    public function revenueThisYear(): float
    {
        return (float) $this->model
            ->whereYear('transaction_date', now()->year)
            ->sum('grand_total');
    }

    public function revenueLastMonth(): float
    {
        $lastMonth = now()->subMonth();
        return (float) $this->model
            ->whereMonth('transaction_date', $lastMonth->month)
            ->whereYear('transaction_date', $lastMonth->year)
            ->sum('grand_total');
    }

    public function revenueByPeriod(string $period = 'month'): Collection
    {
        $format = match ($period) {
            'day' => '%Y-%m-%d',
            'week' => '%x-W%v',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        return $this->model
            ->select(
                DB::raw("DATE_FORMAT(transaction_date, '{$format}') as period"),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(id) as transaction_count'),
                DB::raw('SUM(qty) as total_qty')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    public function topProducts(int $limit = 10): Collection
    {
        return $this->model
            ->select(
                'product_id',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(grand_total) as total_revenue')
            )
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit($limit)
            ->get();
    }

    public function merchantPerformance(): Collection
    {
        return $this->model
            ->select(
                'merchant_code',
                DB::raw('COUNT(id) as transaction_count'),
                DB::raw('SUM(grand_total) as total_revenue'),
                DB::raw('AVG(grand_total) as avg_transaction')
            )
            ->groupBy('merchant_code')
            ->orderByDesc('total_revenue')
            ->get();
    }

    public function dailyRevenue(int $days = 30): Collection
    {
        return $this->model
            ->select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('SUM(grand_total) as revenue')
            )
            ->where('transaction_date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function recentTransactions(int $limit = 5): Collection
    {
        return $this->model->with('product')->latest()->limit($limit)->get();
    }

    public function salesGrowth(?float $thisMonth = null, ?float $lastMonth = null): float
    {
        $thisMonth ??= $this->revenueThisMonth();
        $lastMonth ??= $this->revenueLastMonth();
        if ($lastMonth === 0.0) {
            return $thisMonth > 0 ? 100.0 : 0.0;
        }
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    public function totalMerchants(): int
    {
        return (int) $this->model->distinct('merchant_code')->count('merchant_code');
    }

    public function getFilteredExport(array $filters, int $limit = 5000): Collection
    {
        return $this->applyFilters($this->model->with('product'), $filters)
            ->orderBy('transaction_date', 'desc')
            ->limit($limit)
            ->get();
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['date_from'])) {
            $query->where('transaction_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('transaction_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }
        if (!empty($filters['merchant_code'])) {
            $escaped = str_replace(['%', '_'], ['\%', '\_'], $filters['merchant_code']);
            $query->where('merchant_code', 'like', "%{$escaped}%");
        }

        return $query;
    }
}
