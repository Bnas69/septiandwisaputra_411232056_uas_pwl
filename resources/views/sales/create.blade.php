@extends('layouts.app')
@section('title', 'Tambah Penjualan')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="shopping-cart"></i> Tambah Penjualan</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Penjualan</a></li><li class="breadcrumb-item active">Tambah</li></ol></nav>
    </div>
</div>

<x-error-alert />

<div class="form-card">
    <form action="{{ route('sales.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="form-section-title">Informasi Transaksi</div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label for="transaction_number">No. Transaksi</label>
                    <input type="text" name="transaction_number" id="transaction_number" class="form-control bg-light" value="{{ $transactionNumber }}" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="transaction_date">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="transaction_date" id="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror" value="{{ old('transaction_date', today()->format('Y-m-d')) }}" max="{{ today()->format('Y-m-d') }}" required>
                    @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="product_id">Produk <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->product_name }} (Stok: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="qty">Qty <span class="text-danger">*</span></label>
                    <input type="number" name="qty" id="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', 1) }}" min="1" max="9999" required>
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section-title mt-4">Harga & Total</div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="auth-field">
                    <label for="price_display">Harga Satuan</label>
                    <input type="text" id="price_display" class="form-control bg-light" value="Rp 0" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="auth-field">
                    <label for="subtotal_display">Subtotal</label>
                    <input type="text" id="subtotal_display" class="form-control bg-light" value="Rp 0" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="auth-field">
                    <label for="grand_total_display">Grand Total</label>
                    <input type="text" id="grand_total_display" class="form-control bg-light fw-bold text-primary" value="Rp 0" readonly>
                </div>
            </div>
        </div>

        <div class="form-section-title mt-4">Informasi Merchant</div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label for="merchant_code">Kode Merchant <span class="text-danger">*</span></label>
                    <input type="text" name="merchant_code" id="merchant_code" class="form-control @error('merchant_code') is-invalid @enderror" placeholder="MRC-001" value="{{ old('merchant_code') }}" required maxlength="20">
                    @error('merchant_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section-title mt-4">Pembayaran</div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="auth-field">
                    <label for="payment_method">Metode Pembayaran <span class="text-danger">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                        <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-4">
                <div class="auth-field">
                    <label for="payment_ref">No. Referensi <span class="text-xs text-secondary">(opsional)</span></label>
                    <input type="text" name="payment_ref" id="payment_ref" class="form-control" placeholder="No. transfer / kode QRIS" value="{{ old('payment_ref') }}" maxlength="100">
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('sales.index') }}" class="btn btn-light px-4"><i data-lucide="x"></i> Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i data-lucide="check"></i> Simpan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var productSelect = document.getElementById('product_id');
    var qtyInput = document.getElementById('qty');
    var priceDisplay = document.getElementById('price_display');
    var subtotalDisplay = document.getElementById('subtotal_display');
    var grandTotalDisplay = document.getElementById('grand_total_display');

    function updateTotals() {
        var option = productSelect.options[productSelect.selectedIndex];
        var price = parseInt(option.dataset.price) || 0;
        var qty = parseInt(qtyInput.value) || 0;
        var subtotal = price * qty;
        priceDisplay.value = formatRp(price);
        subtotalDisplay.value = formatRp(subtotal);
        grandTotalDisplay.value = formatRp(subtotal);
    }

    productSelect.addEventListener('change', updateTotals);
    qtyInput.addEventListener('input', updateTotals);
    updateTotals();
});
</script>
@endpush
@endsection
