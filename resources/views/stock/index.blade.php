@extends('layouts.app')
@section('title', 'Stok')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="archive"></i> Stok</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Stok</li></ol></nav>
    </div>
    <a href="{{ route('stock.create') }}" class="btn btn-primary"><i data-lucide="plus"></i> Catat Stok</a>
</div>

<div class="table-card">
    <div class="table-card-header">
        <form method="GET" action="{{ route('stock.index') }}" class="d-flex align-items-center gap-3">
            <div class="input-group search-box">
                <span class="input-group-text bg-light border-end-0"><i data-lucide="search"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari kode atau nama produk..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i data-lucide="search"></i></button>
            </div>
            <select name="type" class="form-select filter-select">
                <option value="">Semua Tipe</option>
                <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Barang Masuk</option>
                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Barang Keluar</option>
            </select>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Produk</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-end">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td>{{ method_exists($stocks, 'firstItem') ? $stocks->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td><span class="font-monospace fw-semibold">{{ $stock->stock_code }}</span></td>
                        <td>{{ $stock->stock_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="fw-medium">{{ $stock->product?->product_name ?? '-' }}</td>
                        <td class="text-center">
                            @if($stock->type === 'in')
                                <span class="badge-status badge-stock-in">Masuk</span>
                            @else
                                <span class="badge-status badge-stock-out">Keluar</span>
                            @endif
                        </td>
                        <td class="text-end fw-semibold">
                            @if($stock->type === 'in')
                                <span class="text-success">+{{ number_format($stock->qty) }}</span>
                            @else
                                <span class="text-danger">-{{ number_format($stock->qty) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i data-lucide="inbox"></i>
                                <p>Belum ada data stok.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stocks->total() > 0)
    <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
        <span class="text-xs text-secondary">Menampilkan {{ $stocks->firstItem() }}-{{ $stocks->lastItem() }} dari {{ $stocks->total() }} data</span>
        @if($stocks->hasPages())
            {{ $stocks->links() }}
        @endif
    </div>
    @endif
</div>
@endsection
