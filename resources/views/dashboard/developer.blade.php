@extends('layouts.app')
@section('title', 'Dashboard Developer')
@section('content')
@php $lowStockCount = count($lowStock); @endphp

<div class="page-header">
    <div>
        <h4><i data-lucide="monitor"></i> Dashboard Developer</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Beranda</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('products.create') }}" class="btn btn-primary"><i data-lucide="plus"></i> Produk</a>
        <a href="{{ route('sales.create') }}" class="btn btn-success"><i data-lucide="shopping-cart"></i> Penjualan</a>
        <a href="{{ route('stock.create') }}" class="btn btn-outline-secondary"><i data-lucide="package"></i> Stok</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle"><i data-lucide="circle-check"></i></div>
                <div><div class="stat-value text-success text-xs">OK</div><div class="stat-label">System Status</div></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <a href="{{ route('report.index') }}" class="link-card">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-success-subtle"><i data-lucide="dollar-sign"></i></div>
                    <div>
                        <div class="stat-value">Rp {{ number_format($kpi['revenue']['value'] ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Revenue Bulan Ini</div>
                        @if(($kpi['revenue']['growth'] ?? 0) != 0)
                        <div class="stat-growth {{ ($kpi['revenue']['growth'] ?? 0) > 0 ? 'positive' : 'negative' }}">{{ ($kpi['revenue']['growth'] ?? 0) > 0 ? '+' : '' }}{{ $kpi['revenue']['growth'] ?? 0 }}%</div>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-md-6 col-6">
        <a href="{{ route('sales.index') }}" class="link-card">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-primary-subtle"><i data-lucide="shopping-cart"></i></div>
                    <div>
                        <div class="stat-value">{{ number_format($kpi['sales']['value'] ?? 0) }}</div>
                        <div class="stat-label">Total Transaksi</div>
                        @if(($kpi['sales']['growth'] ?? 0) != 0)
                        <div class="stat-growth {{ ($kpi['sales']['growth'] ?? 0) > 0 ? 'positive' : 'negative' }}">{{ ($kpi['sales']['growth'] ?? 0) > 0 ? '+' : '' }}{{ $kpi['sales']['growth'] ?? 0 }}%</div>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-2 col-md-6 col-6">
        <a href="{{ route('products.index') }}" class="link-card">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon bg-info-subtle"><i data-lucide="package"></i></div>
                    <div><div class="stat-value">{{ number_format($kpi['products']['value'] ?? 0) }}</div><div class="stat-label">Produk</div></div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-xl-2 col-md-6 col-6">
        <div class="stat-card h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle"><i data-lucide="archive"></i></div>
                <div><div class="stat-value">{{ number_format($kpi['stock']['value'] ?? 0) }}</div><div class="stat-label">Total Stok</div></div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 col-6">
        <a href="{{ route('products.index') }}" class="link-card">
            <div class="stat-card h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon {{ $lowStockCount > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }}"><i data-lucide="triangle-alert"></i></div>
                    <div><div class="stat-value {{ $lowStockCount > 0 ? 'text-danger' : '' }}">{{ $lowStockCount }}</div><div class="stat-label">Stok Menipis</div></div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="table-card h-100">
            <div class="table-card-header"><span class="fw-semibold"><i data-lucide="trophy" class="me-1"></i> Top 5 Produk Terlaris</span></div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th class="col-no">Rank</th><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Revenue</th></tr></thead>
                    <tbody>
                        @forelse($topProducts as $tp)
                        <tr>
                            <td><span class="badge-status bg-primary-subtle text-primary">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $tp->product?->product_name ?? '-' }}</td>
                            <td class="text-center">{{ number_format($tp->total_qty) }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada data penjualan</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="bar-chart-3" class="me-1"></i> Revenue Trend</div>
            <div class="card-body" style="position:relative;height:260px;"><canvas id="revenueChart" data-monthly='@json($chartData["monthly_trend"] ?? [])'></canvas></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="triangle-alert" class="text-warning me-1"></i> Stok Hampir Habis</div>
            <div class="card-body">
                @forelse($lowStock as $ls)
                @php
                    $stockVal = $ls['stock'] ?? 0;
                    $minVal = $ls['minimum_stock'] ?? 1;
                    $rowBg = $stockVal == 0 ? 'row-stock-critical' : ($stockVal <= $minVal / 2 ? 'row-stock-warning' : '');
                @endphp
                <div class="d-flex align-items-center justify-content-between {{ $rowBg }} rounded px-2 py-2 {{ !$loop->last ? 'mb-2' : '' }}">
                    <div><div class="fw-semibold text-line-height">{{ $ls['name'] ?? '-' }}</div><div class="text-xs text-secondary">Stok: {{ $stockVal }} / Min: {{ $minVal }}</div></div>
                    <span class="badge-status priority-{{ strtolower($ls['priority'] ?? 'medium') }}">{{ $ls['priority'] ?? 'Normal' }}</span>
                </div>
                @empty
                <div class="empty-state py-3"><i data-lucide="circle-check"></i><p>Semua stok aman</p></div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="lightbulb" class="text-warning me-1"></i> Rekomendasi</div>
            <div class="card-body">
                @if(!empty($recommendations))
                <div class="row g-3">
                    @foreach($recommendations as $rec)
                    <div class="col-md-6">
                        <div class="border rounded-lg p-3 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge-status {{ $rec['type'] === 'restock' ? 'priority-high' : 'priority-opportunity' }}">{{ $rec['type'] === 'restock' ? 'Restock' : 'Promosi' }}</span>
                                <span class="badge-status priority-{{ strtolower($rec['priority']) }}">{{ $rec['priority'] }}</span>
                            </div>
                            <p class="mb-2 fw-semibold text-xs">{{ $rec['message'] }}</p>
                            <ul class="mb-0 text-xs text-secondary ps-4">
                                @foreach($rec['suggestions'] as $s)<li>{{ $s }}</li>@endforeach
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state py-3"><i data-lucide="circle-check"></i><p>Tidak ada rekomendasi</p></div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i data-lucide="clock" class="me-1"></i> Transaksi Terbaru</div>
    <div class="card-body">
        @forelse($recentTransactions as $tx)
        <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
            <div class="icon-sm-32 bg-info-subtle"><i data-lucide="receipt"></i></div>
            <div class="flex-grow-1 min-width-0">
                <div class="fw-semibold text-line-height">{{ $tx->product?->product_name ?? '-' }}</div>
                <div class="text-xs text-secondary">{{ $tx->transaction_date?->format('d M Y') ?? '-' }}</div>
            </div>
            <div class="text-end fw-semibold text-line-height">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</div>
        </div>
        @empty
        <div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada transaksi</p></div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function() {
    var ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    var data = JSON.parse(ctx.getAttribute('data-monthly') || '[]');
    new Chart(ctx, {
        type: 'bar',
        data: { labels: data.map(function(d) { return d.period; }), datasets: [{ label: 'Revenue', data: data.map(function(d) { return d.revenue; }), backgroundColor: 'rgba(79,70,229,0.75)', borderRadius: 6, borderSkipped: false }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) { return formatRp(c.raw); } } } },
            scales: { x: { grid: { display: false }, ticks: { font: { size: 11 } } }, y: { beginAtZero: true, ticks: { font: { size: 11 }, callback: function(v) { return v >= 1000000 ? (v/1000000).toFixed(0)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v; } }, grid: { color: 'rgba(0,0,0,0.04)' } } }
        }
    });
})();
</script>
@endpush
