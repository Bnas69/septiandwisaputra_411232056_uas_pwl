@extends('layouts.app')
@section('title', 'Merchant')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="store"></i> Merchant</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Merchant</li></ol></nav>
    </div>
</div>

<div class="row g-4">
    @forelse($merchantsWithStats as $ms)
    @php
        $m = $ms['merchant'];
        $iconColors = [
            'store' => 'bg-primary-subtle',
            'monitor' => 'bg-info-subtle',
            'apple' => 'bg-success-subtle',
            'shirt' => 'bg-warning-subtle',
            'home' => 'bg-danger-subtle',
        ];
        $iconBg = $iconColors[$m->icon] ?? 'bg-secondary-subtle';
    @endphp
    <div class="col-lg-4 col-md-6">
        <a href="{{ route('merchants.show', $m->code) }}" class="link-card">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="stat-icon {{ $iconBg }}"><i data-lucide="{{ $m->icon }}"></i></div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-bold">{{ $m->name }}</div>
                            <div class="text-xs text-secondary">{{ $m->code }}</div>
                        </div>
                    </div>
                    <p class="text-xs text-secondary mb-3">{{ $m->description ?? '-' }}</p>
                    <div class="d-flex align-items-center gap-1 text-xs text-secondary mb-3">
                        <i data-lucide="map-pin" style="width:12px;height:12px;"></i>
                        <span>{{ $m->location ?? '-' }}</span>
                    </div>
                    <hr class="my-2">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="text-xs text-secondary mb-1">Revenue</div>
                            <div class="fw-bold text-xs">Rp {{ number_format($ms['revenue_this_month'], 0, ',', '.') }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-xs text-secondary mb-1">Transaksi</div>
                            <div class="fw-bold text-xs">{{ number_format($ms['transaction_count']) }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-xs text-secondary mb-1">Growth</div>
                            <div class="fw-bold text-xs {{ $ms['growth'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $ms['growth'] >= 0 ? '+' : '' }}{{ $ms['growth'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="col-12">
        <div class="empty-state py-5"><i data-lucide="store"></i><p>Belum ada data merchant</p></div>
    </div>
    @endforelse
</div>
@endsection
