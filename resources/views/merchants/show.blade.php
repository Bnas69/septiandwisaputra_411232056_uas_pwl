@extends('layouts.app')
@section('title', $merchant->name)
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="{{ $merchant->icon }}"></i> {{ $merchant->name }}</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item"><a href="{{ route('merchants.index') }}">Merchant</a></li><li class="breadcrumb-item active">{{ $merchant->code }}</li></ol></nav>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            @php
                $iconColors = [
                    'store' => 'bg-primary-subtle',
                    'monitor' => 'bg-info-subtle',
                    'apple' => 'bg-success-subtle',
                    'shirt' => 'bg-warning-subtle',
                    'home' => 'bg-danger-subtle',
                ];
                $iconBg = $iconColors[$merchant->icon] ?? 'bg-secondary-subtle';
            @endphp
            <div class="stat-icon {{ $iconBg }}" style="width:56px;height:56px;border-radius:14px;"><i data-lucide="{{ $merchant->icon }}" style="width:24px;height:24px;"></i></div>
            <div class="flex-grow-1 min-width-0">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <h5 class="fw-bold mb-0">{{ $merchant->name }}</h5>
                    <span class="badge-status {{ $merchant->isActive() ? 'badge-active' : 'badge-inactive' }}">{{ $merchant->isActive() ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                <div class="text-xs text-secondary mb-1">{{ $merchant->code }} &middot; {{ $merchant->description ?? '-' }}</div>
                <div class="d-flex align-items-center gap-1 text-xs text-secondary">
                    <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                    <span>{{ $merchant->location ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle"><i data-lucide="dollar-sign"></i></div>
            <div><div class="stat-value">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</div><div class="stat-label">Revenue Bulan Ini</div>
                @if($growth != 0)<div class="stat-growth {{ $growth > 0 ? 'positive' : 'negative' }}">{{ $growth > 0 ? '+' : '' }}{{ $growth }}%</div>@endif
            </div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle"><i data-lucide="receipt"></i></div>
            <div><div class="stat-value">{{ number_format($totalTransactions) }}</div><div class="stat-label">Total Transaksi</div></div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-info-subtle"><i data-lucide="package"></i></div>
            <div><div class="stat-value">{{ number_format($totalProducts) }}</div><div class="stat-label">Produk Dijual</div></div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning-subtle"><i data-lucide="trending-up"></i></div>
            <div><div class="stat-value">Rp {{ number_format($performance->avg_transaction ?? 0, 0, ',', '.') }}</div><div class="stat-label">Rata-rata/Transaksi</div></div>
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
        <div class="card h-100">
            <div class="card-header"><i data-lucide="bar-chart-3" class="me-1"></i> Revenue Trend (6 Bulan)</div>
            <div class="card-body" style="position:relative;height:260px;"><canvas id="merchantRevenueChart" data-monthly='@json($monthlyRevenue)'></canvas></div>
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
                <div class="text-xs text-secondary">{{ $tx->transaction_date?->format('d M Y') ?? '-' }} &middot; {{ $tx->transaction_number }}</div>
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
    var ctx = document.getElementById('merchantRevenueChart');
    if (!ctx) return;
    var data = JSON.parse(ctx.getAttribute('data-monthly') || '[]');
    new Chart(ctx, {
        type: 'line',
        data: { labels: data.map(function(d) { return d.period; }), datasets: [{ label: 'Revenue', data: data.map(function(d) { return parseFloat(d.revenue); }), borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.06)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#4f46e5', borderWidth: 2 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function(c) { return 'Rp ' + c.raw.toLocaleString('id-ID'); } } } },
            scales: { x: { grid: { display: false }, ticks: { font: { size: 11 } } }, y: { beginAtZero: true, ticks: { font: { size: 11 }, callback: function(v) { return v >= 1000000 ? (v/1000000).toFixed(0)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v; } }, grid: { color: 'rgba(0,0,0,0.04)' } } }
        }
    });
})();
</script>
@endpush
