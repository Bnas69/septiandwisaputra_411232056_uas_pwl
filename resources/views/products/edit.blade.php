@extends('layouts.app')
@section('title', 'Edit Produk')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="square-pen"></i> Edit Produk</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    </div>
</div>

<x-error-alert />

<div class="form-card">
    <form action="{{ route('products.update', $product) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="form-section-title">Informasi Produk</div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label for="product_code">Kode Produk <span class="text-danger">*</span></label>
                    <input type="text" name="product_code" id="product_code" class="form-control @error('product_code') is-invalid @enderror" placeholder="PRD-000001" value="{{ old('product_code', $product->product_code) }}" required autofocus>
                    @error('product_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="product_name">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="product_name" id="product_name" class="form-control @error('product_name') is-invalid @enderror" placeholder="Nama produk" value="{{ old('product_name', $product->product_name) }}" required>
                    @error('product_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="category">Kategori <span class="text-danger">*</span></label>
                    <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" placeholder="Pilih atau ketik kategori" value="{{ old('category', $product->category) }}" list="category-list" required>
                    <datalist id="category-list">
                        @if($categories->isNotEmpty())
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        @endif
                    </datalist>
                    @error('category')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="price">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="Harga dalam Rupiah" value="{{ old('price', $product->price) }}" min="100" max="999999999" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="stock">Stok <span class="text-danger">*</span></label>
                    <input type="number" name="stock" id="stock" class="form-control @error('stock') is-invalid @enderror" placeholder="Jumlah stok" value="{{ old('stock', $product->stock) }}" min="0" max="99999" required>
                    @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="minimum_stock">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" name="minimum_stock" id="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" placeholder="Stok minimum" value="{{ old('minimum_stock', $product->minimum_stock) }}" min="1" max="1000" required>
                    @error('minimum_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-light px-4"><i data-lucide="x"></i> Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i data-lucide="check"></i> Perbarui</button>
        </div>
    </form>
</div>
@endsection
