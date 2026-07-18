@extends('layouts.app')
@section('title', 'Penjualan')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="receipt"></i> Penjualan</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Penjualan</li></ol></nav>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary"><i data-lucide="plus"></i> Transaksi Baru</a>
</div>

<div class="form-card mb-4">
    <form method="GET" action="{{ route('sales.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <div class="auth-field mb-0">
                    <label for="date_from">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="auth-field mb-0">
                    <label for="date_to">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="auth-field mb-0">
                    <label for="product_id">Produk</label>
                    <select name="product_id" id="product_id" class="form-select">
                        <option value="">Semua Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="auth-field mb-0">
                    <label for="merchant_code">Kode Merchant</label>
                    <input type="text" name="merchant_code" id="merchant_code" class="form-control" placeholder="MRC-001" value="{{ request('merchant_code') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i data-lucide="filter"></i> Filter</button>
                <a href="{{ route('sales.index') }}" class="btn btn-light"><i data-lucide="rotate-ccw"></i> Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="fw-semibold text-secondary">Daftar Transaksi Penjualan</span>
        <span class="text-xs text-secondary">Total: {{ $sales->total() }} transaksi</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Merchant</th>
                    <th>Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-center">Pembayaran</th>
                    <th class="text-center col-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    <tr>
                        <td>{{ method_exists($sales, 'firstItem') ? $sales->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td><span class="badge-status bg-primary-subtle text-primary">{{ $sale->transaction_number }}</span></td>
                        <td>{{ $sale->transaction_date?->format('d/m/Y') ?? '-' }}</td>
                        <td><span class="text-secondary">{{ $sale->merchant_code }}</span></td>
                        <td class="fw-medium">{{ $sale->product?->product_name ?? '-' }}</td>
                        <td class="text-center">{{ $sale->qty }}</td>
                        <td class="text-end fw-semibold">Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</td>
                        <td class="text-center"><span class="badge-status {{ $sale->paymentStatusBadge() }}">{{ $sale->paymentMethodLabel() }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i data-lucide="eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i data-lucide="inbox"></i>
                                <p>Belum ada data transaksi penjualan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sales->total() > 0)
    <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
        <span class="text-xs text-secondary">Menampilkan {{ $sales->firstItem() }}-{{ $sales->lastItem() }} dari {{ $sales->total() }} data</span>
        @if($sales->hasPages())
            {{ $sales->links() }}
        @endif
    </div>
    @endif
</div>
@endsection
