@extends('layouts.app')
@section('title', 'Dashboard Pegawai')
@section('content')
@php $lowStockCount = count($lowStock); @endphp

<div class="page-header">
    <div>
        <h4><i data-lucide="gauge"></i> Dashboard Pegawai</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Beranda</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.create') }}" class="btn btn-success"><i data-lucide="shopping-cart"></i> Penjualan Baru</a>
        <a href="{{ route('stock.create') }}" class="btn btn-outline-secondary"><i data-lucide="package"></i> Barang Masuk</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('sales.create') }}" class="link-card">
            <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success-subtle"><i data-lucide="shopping-cart"></i></div>
                <div><div class="stat-label">Aksi Cepat</div><div class="stat-value text-line-height">Penjualan Baru</div><div class="stat-label">Klik untuk transaksi</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('stock.create') }}" class="link-card">
            <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle"><i data-lucide="package"></i></div>
                <div><div class="stat-label">Aksi Cepat</div><div class="stat-value text-line-height">Barang Masuk</div><div class="stat-label">Klik untuk catat stok</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-lg-2 col-md-6 col-6">
        <a href="{{ route('products.index') }}" class="link-card">
            <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary-subtle"><i data-lucide="package"></i></div>
                <div><div class="stat-value">{{ number_format($kpi['products']['value']) }}</div><div class="stat-label">Produk</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-lg-2 col-md-6 col-6">
        <a href="{{ route('stock.index') }}" class="link-card">
            <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning-subtle"><i data-lucide="archive"></i></div>
                <div><div class="stat-value">{{ number_format($kpi['stock']['value']) }}</div><div class="stat-label">Total Stok</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-lg-2 col-md-6 col-6">
        <a href="{{ route('products.index') }}" class="link-card">
            <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
                <div class="stat-icon {{ $lowStockCount > 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }}"><i data-lucide="triangle-alert"></i></div>
                <div><div class="stat-value {{ $lowStockCount > 0 ? 'text-danger' : '' }}">{{ $lowStockCount }}</div><div class="stat-label">Stok Menipis</div></div>
            </div></div>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="table-card h-100">
            <div class="table-card-header"><span class="fw-semibold text-danger"><i data-lucide="triangle-alert" class="me-1"></i> Stok Menipis</span></div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Produk</th><th class="text-center">Stok</th><th class="text-center">Min</th><th class="text-center">Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($lowStock as $ls)
                        <tr class="{{ ($ls['stock'] ?? 0) == 0 ? 'row-stock-critical' : (($ls['stock'] ?? 0) <= ($ls['minimum_stock'] ?? 1) / 2 ? 'row-stock-warning' : '') }}">
                            <td class="fw-semibold">{{ $ls['name'] }}</td>
                            <td class="text-center"><span class="fw-bold {{ ($ls['stock'] ?? 0) == 0 ? 'text-danger' : 'text-warning' }}">{{ $ls['stock'] ?? 0 }}</span></td>
                            <td class="text-center text-secondary">{{ $ls['minimum_stock'] ?? 0 }}</td>
                            <td class="text-center">
                                @if(($ls['stock'] ?? 0) == 0)<span class="badge-status priority-critical">HABIS</span>
                                @elseif(($ls['stock'] ?? 0) <= ($ls['minimum_stock'] ?? 1) / 2)<span class="badge-status priority-high">KRITIS</span>
                                @else<span class="badge-status priority-medium">MENIPIS</span>@endif
                            </td>
                            <td class="text-end"><a href="{{ route('stock.create') }}" class="btn btn-sm btn-outline-success"><i data-lucide="plus"></i> Masuk</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="5"><div class="empty-state py-3"><i data-lucide="circle-check" class="text-success"></i><p>Semua stok aman</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="calendar-check" class="me-1"></i> Ringkasan</div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom"><span class="text-secondary">Total Transaksi</span><strong>{{ number_format($kpi['transactions']['value'] ?? 0) }}</strong></div>
                <div class="d-flex justify-content-between align-items-center pb-2 mb-2 border-bottom"><span class="text-secondary">Total Stok</span><strong>{{ number_format($kpi['stock']['value']) }}</strong></div>
                <div class="d-flex justify-content-between align-items-center"><span class="text-secondary">Stok Menipis</span>
                    @if($lowStockCount > 0)<span class="badge-status priority-critical">{{ $lowStockCount }}</span>
                    @else<span class="badge-status badge-active">0</span>@endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="table-card h-100">
            <div class="table-card-header"><span class="fw-semibold"><i data-lucide="trophy" class="me-1"></i> Top 5 Produk</span></div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead><tr><th class="col-no">Rank</th><th>Produk</th><th class="text-center">Qty</th></tr></thead>
                    <tbody>
                        @forelse($topProducts as $tp)
                        <tr>
                            <td><span class="badge-status bg-primary-subtle text-primary">{{ $loop->iteration }}</span></td>
                            <td class="fw-semibold">{{ $tp->product?->product_name ?? '-' }}</td>
                            <td class="text-center fw-semibold">{{ number_format($tp->total_qty) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3"><div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada data</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="clock" class="me-1"></i> Transaksi Terbaru</div>
            <div class="card-body">
                @forelse($recentTransactions as $tx)
                <div class="d-flex align-items-center justify-content-between {{ !$loop->last ? 'pb-2 mb-2 border-bottom' : '' }}">
                    <div><div class="fw-semibold text-line-height">{{ $tx->product?->product_name ?? '-' }}</div><div class="text-xs text-secondary">{{ $tx->transaction_number }}</div></div>
                    <div class="text-end fw-semibold text-line-height">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</div>
                </div>
                @empty
                <div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada transaksi</p></div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
