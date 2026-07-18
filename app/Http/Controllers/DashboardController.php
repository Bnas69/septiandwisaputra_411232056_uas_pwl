<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index()
    {
        return match (Auth::user()?->role) {
            'developer' => $this->developer(),
            'owner' => $this->owner(),
            'pegawai' => $this->pegawai(),
            default => $this->user(),
        };
    }

    public function developer()
    {
        try {
            $data = $this->dashboardService->getDeveloperDashboard();

            return view('dashboard.developer', $data);
        } catch (\Throwable $e) {
            report($e);

            return view('dashboard.developer', [
                'kpi' => [],
                'topProducts' => collect(),
                'lowStock' => [],
                'recommendations' => [],
                'chartData' => ['monthly_trend' => []],
                'recentTransactions' => collect(),
            ]);
        }
    }

    public function owner()
    {
        try {
            $data = $this->dashboardService->getOwnerDashboard();

            return view('dashboard.owner', $data);
        } catch (\Throwable $e) {
            report($e);

            return view('dashboard.owner', [
                'kpi' => [],
                'topProducts' => collect(),
                'lowStock' => [],
                'recommendations' => [],
                'chartData' => ['monthly_trend' => []],
                'recentTransactions' => collect(),
                'merchantPerformance' => collect(),
                'revenueAnalytics' => [],
            ]);
        }
    }

    public function pegawai()
    {
        try {
            $data = $this->dashboardService->getPegawaiDashboard();

            return view('dashboard.pegawai', $data);
        } catch (\Throwable $e) {
            report($e);

            return view('dashboard.pegawai', [
                'kpi' => [],
                'topProducts' => collect(),
                'lowStock' => [],
                'recentTransactions' => collect(),
            ]);
        }
    }

    public function user()
    {
        try {
            $data = $this->dashboardService->getUserDashboard();

            return view('dashboard.user', $data);
        } catch (\Throwable $e) {
            report($e);

            return view('dashboard.user', [
                'user' => Auth::user(),
                'recentTransactions' => collect(),
                'userStats' => [
                    'total_transactions' => 0,
                    'total_spending' => 0,
                    'avg_transaction' => 0,
                    'last_transaction_date' => null,
                ],
            ]);
        }
    }
}
