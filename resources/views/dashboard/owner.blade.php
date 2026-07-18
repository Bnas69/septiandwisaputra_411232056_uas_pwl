@extends('layouts.app')
@section('title', 'Dashboard Owner')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="trending-up"></i> Dashboard Owner</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Beranda</li></ol></nav>
    </div>
    <a href="{{ route('report.index') }}" class="btn btn-primary"><i data-lucide="bar-chart-3"></i> Laporan</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('report.index') }}" class="link-card"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle"><i data-lucide="calendar"></i></div>
            <div><div class="stat-label">Hari Ini</div><div class="stat-value">Rp {{ number_format($revenueAnalytics['today'] ?? 0, 0, ',', '.') }}</div></div>
        </div></div></a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('report.index') }}" class="link-card"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle"><i data-lucide="calendar"></i></div>
            <div><div class="stat-label">Minggu Ini</div><div class="stat-value">Rp {{ number_format($revenueAnalytics['week'] ?? 0, 0, ',', '.') }}</div></div>
        </div></div></a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('report.index') }}" class="link-card"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-info-subtle"><i data-lucide="calendar"></i></div>
            <div><div class="stat-label">Bulan Ini</div><div class="stat-value">Rp {{ number_format($revenueAnalytics['month'] ?? 0, 0, ',', '.') }}</div></div>
        </div></div></a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('report.index') }}" class="link-card"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning-subtle"><i data-lucide="calendar"></i></div>
            <div><div class="stat-label">Tahun Ini</div><div class="stat-value">Rp {{ number_format($revenueAnalytics['year'] ?? 0, 0, ',', '.') }}</div></div>
        </div></div></a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle"><i data-lucide="dollar-sign"></i></div>
            <div><div class="stat-value">Rp {{ number_format($kpi['revenue']['value'] ?? 0, 0, ',', '.') }}</div><div class="stat-label">Total Revenue</div>
                @if(($kpi['revenue']['growth'] ?? 0) != 0)<div class="stat-growth {{ ($kpi['revenue']['growth'] ?? 0) > 0 ? 'positive' : 'negative' }}">{{ ($kpi['revenue']['growth'] ?? 0) > 0 ? '+' : '' }}{{ $kpi['revenue']['growth'] ?? 0 }}%</div>@endif
            </div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('sales.index') }}" class="link-card"><div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle"><i data-lucide="receipt"></i></div>
            <div><div class="stat-value">{{ number_format($kpi['transactions']['value'] ?? 0) }}</div><div class="stat-label">Total Transaksi</div>
                @if(($kpi['transactions']['growth'] ?? 0) != 0)<div class="stat-growth {{ ($kpi['transactions']['growth'] ?? 0) > 0 ? 'positive' : 'negative' }}">{{ ($kpi['transactions']['growth'] ?? 0) > 0 ? '+' : '' }}{{ $kpi['transactions']['growth'] ?? 0 }}%</div>@endif
            </div>
        </div></div></a>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-secondary-subtle"><i data-lucide="store"></i></div>
            <div><div class="stat-value">{{ number_format($kpi['merchants']['value'] ?? 0) }}</div><div class="stat-label">Merchants</div>
                @if(($kpi['merchants']['growth'] ?? 0) != 0)<div class="stat-growth {{ ($kpi['merchants']['growth'] ?? 0) > 0 ? 'positive' : 'negative' }}">{{ ($kpi['merchants']['growth'] ?? 0) > 0 ? '+' : '' }}{{ $kpi['merchants']['growth'] ?? 0 }}%</div>@endif
            </div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon {{ ($revenueAnalytics['growth'] ?? 0) >= 0 ? 'bg-success-subtle' : 'bg-danger-subtle' }}"><i data-lucide="trending-up"></i></div>
            <div><div class="stat-value {{ ($revenueAnalytics['growth'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">{{ ($revenueAnalytics['growth'] ?? 0) >= 0 ? '+' : '' }}{{ $revenueAnalytics['growth'] ?? 0 }}%</div><div class="stat-label">Growth</div></div>
        </div></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="table-card h-100">
            <div class="table-card-header"><span class="fw-semibold"><i data-lucide="trophy" class="me-1"></i> Top 5 Produk</span></div>
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
                        <tr><td colspan="4"><div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada data</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="table-card h-100">
            <div class="table-card-header">
                <span class="fw-semibold"><i data-lucide="store" class="me-1"></i> Merchant Performance</span>
                <a href="{{ route('merchants.index') }}" class="text-xs text-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Merchant</th><th class="text-center">Transaksi</th><th class="text-end">Revenue</th><th class="text-end">Growth</th></tr></thead>
                    <tbody>
                        @forelse($merchantsWithStats as $ms)
                        <tr>
                            <td>
                                <a href="{{ route('merchants.show', $ms['code']) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                    <div class="avatar-sm avatar-color-2"><i data-lucide="{{ $ms['icon'] }}" style="width:14px;height:14px;"></i></div>
                                    <span class="fw-semibold text-line-height">{{ $ms['name'] }}</span>
                                </a>
                            </td>
                            <td class="text-center">{{ number_format($ms['transaction_count']) }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($ms['total_revenue'], 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if($ms['growth'] != 0)
                                <span class="stat-growth {{ $ms['growth'] > 0 ? 'positive' : 'negative' }}">{{ $ms['growth'] > 0 ? '+' : '' }}{{ $ms['growth'] }}%</span>
                                @else
                                <span class="text-xs text-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4"><div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada data</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="triangle-alert" class="text-warning me-1"></i> Low Stock</div>
            <div class="card-body">
                @forelse($lowStock as $ls)
                @php
                    $stockVal = $ls['stock'] ?? 0;
                    $minVal = $ls['minimum_stock'] ?? 1;
                    $rowBg = $stockVal == 0 ? 'row-stock-critical' : ($stockVal <= $minVal / 2 ? 'row-stock-warning' : '');
                @endphp
                <div class="d-flex align-items-center justify-content-between {{ $rowBg }} rounded px-2 py-2 {{ !$loop->last ? 'mb-2' : '' }}">
                    <div><div class="fw-semibold text-line-height">{{ $ls['name'] ?? '-' }}</div><div class="text-xs text-secondary">{{ $stockVal }} / {{ $minVal }}</div></div>
                    <div class="text-end"><span class="badge-status priority-{{ strtolower($ls['priority'] ?? 'medium') }}">{{ $ls['priority'] ?? 'Normal' }}</span><div class="text-xs text-secondary mt-1">{{ $ls['estimated_days'] ?? 0 }} hari</div></div>
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
                @forelse($recommendations as $rec)
                <div class="{{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge-status {{ $rec['type'] === 'restock' ? 'priority-high' : 'priority-opportunity' }}">{{ $rec['type'] === 'restock' ? 'Restock' : 'Promosi' }}</span>
                    </div>
                    <p class="mb-1 fw-semibold text-xs">{{ $rec['message'] }}</p>
                    <ul class="mb-0 text-xs text-secondary ps-4">@foreach($rec['suggestions'] as $s)<li>{{ $s }}</li>@endforeach</ul>
                </div>
                @empty
                <div class="empty-state py-3"><i data-lucide="circle-check"></i><p>Tidak ada rekomendasi</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i data-lucide="bar-chart-3" class="me-1"></i> Revenue Trend</div>
    <div class="card-body" style="position:relative;height:240px;"><canvas id="revenueTrendChart" data-monthly='@json($chartData["monthly_trend"] ?? [])'></canvas></div>
</div>

<div class="card mb-4">
    <div class="card-header"><i data-lucide="clock" class="me-1"></i> Transaksi Terbaru</div>
    <div class="card-body">
        @forelse($recentTransactions as $tx)
        <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
            <div class="icon-sm-32 bg-info-subtle"><i data-lucide="receipt"></i></div>
            <div class="flex-grow-1 min-width-0"><div class="fw-semibold text-line-height">{{ $tx->product?->product_name ?? '-' }}</div><div class="text-xs text-secondary">{{ $tx->transaction_date?->format('d M Y') ?? '-' }}</div></div>
            <div class="text-end fw-semibold text-line-height">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</div>
        </div>
        @empty
        <div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada aktivitas</p></div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function() {
    var ctx = document.getElementById('revenueTrendChart');
    if (!ctx) return;
    var data = JSON.parse(ctx.getAttribute('data-monthly') || '[]');
    new Chart(ctx, {
        type: 'line',
        data: { labels: data.map(function(d) { return d.period; }), datasets: [{ label: 'Revenue', data: data.map(function(d) { return d.revenue; }), borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.06)', fill: true, tension: 0.4, pointRadius: 3, pointBackgroundColor: '#4f46e5', borderWidth: 2 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) { return formatRp(c.raw); } } } },
            scales: { x: { grid: { display: false }, ticks: { font: { size: 11 } } }, y: { beginAtZero: true, ticks: { font: { size: 11 }, callback: function(v) { return v >= 1000000 ? (v/1000000).toFixed(0)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v; } }, grid: { color: 'rgba(0,0,0,0.04)' } } }
        }
    });
})();
</script>
@endpush
