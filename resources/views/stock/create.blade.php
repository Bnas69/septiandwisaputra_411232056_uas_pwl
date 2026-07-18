@extends('layouts.app')
@section('title', 'Catat Stok')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="circle-plus"></i> Catat Stok</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stok</a></li><li class="breadcrumb-item active">Tambah</li></ol></nav>
    </div>
</div>

<x-error-alert />

<div class="form-card">
    <form action="{{ route('stock.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="form-section-title">Informasi Stok</div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label for="stock_code">Kode Stok</label>
                    <input type="text" name="stock_code" id="stock_code" class="form-control bg-light" value="{{ $stockCode }}" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="type">Tipe <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">-- Pilih Tipe --</option>
                        <option value="in" {{ old('type') === 'in' ? 'selected' : '' }}>Barang Masuk</option>
                        <option value="out" {{ old('type') === 'out' ? 'selected' : '' }}>Barang Keluar</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="product_id">Produk <span class="text-danger">*</span></label>
                    <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->product_name }} (Stok: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="qty">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="qty" id="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty') }}" min="1" max="99999" required>
                    @error('qty')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="stock_date">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="stock_date" id="stock_date" class="form-control @error('stock_date') is-invalid @enderror" value="{{ old('stock_date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required>
                    @error('stock_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('stock.index') }}" class="btn btn-light px-4"><i data-lucide="x"></i> Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i data-lucide="check"></i> Simpan</button>
        </div>
    </form>
</div>
@endsection
