@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="layout-dashboard"></i> Dashboard</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item active">Beranda</li></ol></nav>
    </div>
</div>

<div class="card hero-card mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-4">
            @php
                $avatarId = $user->avatar ?? 1;
                $heroIcons = [1 => 'store', 2 => 'leaf', 3 => 'star', 4 => 'heart'];
                $heroIcon = $heroIcons[$avatarId] ?? 'user';
            @endphp
            <div class="avatar-xl avatar-color-{{ $avatarId }}"><i data-lucide="{{ $heroIcon }}" style="width:28px;height:28px;"></i></div>
            <div>
                <h4 class="fw-bold mb-1">Selamat datang, {{ $user->name ?? 'User' }}!</h4>
                <p class="mb-0 text-xs text-white-50">{{ $user->email ?? '' }}</p>
                <span class="badge-status mt-2">{{ ucfirst($user->role ?? 'user') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-primary-subtle"><i data-lucide="receipt"></i></div>
            <div><div class="stat-value">{{ number_format($userStats['total_transactions'] ?? 0) }}</div><div class="stat-label">Total Transaksi</div></div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-success-subtle"><i data-lucide="dollar-sign"></i></div>
            <div><div class="stat-value">Rp {{ number_format($userStats['total_spending'] ?? 0, 0, ',', '.') }}</div><div class="stat-label">Total Pengeluaran</div></div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-info-subtle"><i data-lucide="calculator"></i></div>
            <div><div class="stat-value">Rp {{ number_format($userStats['avg_transaction'] ?? 0, 0, ',', '.') }}</div><div class="stat-label">Rata-rata/Transaksi</div></div>
        </div></div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card h-100"><div class="d-flex align-items-center gap-3">
            <div class="stat-icon bg-warning-subtle"><i data-lucide="calendar"></i></div>
            <div><div class="stat-value text-line-height">{{ $userStats['last_transaction_date'] ? \Carbon\Carbon::parse($userStats['last_transaction_date'])->format('d M Y') : '-' }}</div><div class="stat-label">Transaksi Terakhir</div></div>
        </div></div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i data-lucide="clock" class="me-1"></i> Transaksi Terbaru</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead><tr><th>No. Transaksi</th><th>Tanggal</th><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                    @forelse($recentTransactions as $tx)
                    <tr>
                        <td><span class="text-xs text-secondary">{{ $tx->transaction_number }}</span></td>
                        <td class="text-line-height">{{ $tx->transaction_date ? $tx->transaction_date->format('d M Y') : '-' }}</td>
                        <td class="fw-semibold text-line-height">{{ $tx->product?->product_name ?? '-' }}</td>
                        <td class="text-center">{{ $tx->qty }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5"><div class="empty-state py-3"><i data-lucide="inbox"></i><p>Belum ada transaksi</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="user" class="me-1"></i> Informasi Akun</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6"><div class="text-xs text-secondary mb-1">Nama</div><div class="fw-semibold">{{ $user->name ?? '-' }}</div></div>
                    <div class="col-sm-6"><div class="text-xs text-secondary mb-1">Email</div><div class="fw-semibold">{{ $user->email ?? '-' }}</div></div>
                    <div class="col-sm-6"><div class="text-xs text-secondary mb-1">Role</div><span class="badge-status bg-primary-subtle text-primary">{{ ucfirst($user->role ?? 'user') }}</span></div>
                    <div class="col-sm-6"><div class="text-xs text-secondary mb-1">Member Sejak</div><div class="fw-semibold">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header"><i data-lucide="settings" class="me-1"></i> Pengaturan</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('profile') }}" class="btn btn-outline-primary text-start"><i data-lucide="user" class="me-1"></i> Edit Profil</a>
                    <a href="{{ route('change.password.form') }}" class="btn btn-outline-secondary text-start"><i data-lucide="key-round" class="me-1"></i> Ubah Password</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
