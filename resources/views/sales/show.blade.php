@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="receipt"></i> Detail Transaksi</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Penjualan</a></li><li class="breadcrumb-item active">Detail</li></ol></nav>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.receipt', $sale->id) }}" class="btn btn-success" target="_blank"><i data-lucide="file-text"></i> Resi</a>
        <button onclick="window.print();" class="btn btn-outline-secondary"><i data-lucide="printer"></i> Cetak</button>
        <a href="{{ route('sales.index') }}" class="btn btn-light"><i data-lucide="arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span class="badge-status bg-primary-subtle text-primary detail-header-badge">{{ $sale->transaction_number }}</span>
            <div class="d-flex align-items-center gap-2">
                <span class="badge-status {{ $sale->paymentStatusBadge() }}">{{ ucfirst($sale->payment_status) }}</span>
                <span class="text-xs text-secondary"><i data-lucide="calendar" class="detail-header-icon"></i> {{ $sale->transaction_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label>No. Transaksi</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->transaction_number }}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="auth-field">
                    <label>Tanggal Transaksi</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->transaction_date?->format('d/m/Y') ?? '-' }}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="auth-field">
                    <label>Kode Merchant</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->merchant_code }}" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="auth-field">
                    <label>Produk</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->product?->product_name ?? '-' }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="auth-field">
                    <label>Qty</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->qty }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="auth-field">
                    <label>Harga Satuan</label>
                    <input type="text" class="form-control bg-light" value="Rp {{ number_format($sale->price, 0, ',', '.') }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="auth-field">
                    <label>Subtotal</label>
                    <input type="text" class="form-control bg-light" value="Rp {{ number_format($sale->subtotal, 0, ',', '.') }}" readonly>
                </div>
            </div>
        </div>

        <div class="form-section-title mt-4">Pembayaran</div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="auth-field">
                    <label>Metode Pembayaran</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->paymentMethodLabel() }}" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="auth-field">
                    <label>Status Pembayaran</label>
                    <input type="text" class="form-control bg-light" value="{{ ucfirst($sale->payment_status) }}" readonly>
                </div>
            </div>
            @if($sale->payment_ref)
            <div class="col-md-4">
                <div class="auth-field">
                    <label>No. Referensi</label>
                    <input type="text" class="form-control bg-light" value="{{ $sale->payment_ref }}" readonly>
                </div>
            </div>
            @endif
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end">
            <div class="text-end">
                <div class="text-xs text-secondary mb-1">Grand Total</div>
                <div class="fw-bold text-primary detail-grand-total">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
