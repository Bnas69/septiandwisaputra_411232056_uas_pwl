@extends('layouts.app')
@section('title', 'Laporan')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="trending-up"></i> Laporan</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Laporan</li></ol></nav>
    </div>
</div>

<div class="form-card mb-4">
    <form method="GET" action="{{ route('report.index') }}" id="filter-form">
        <div class="row g-3 align-items-end">
            <div class="col-md-2">
                <div class="auth-field mb-0">
                    <label for="date_from">Dari Tanggal</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="auth-field mb-0">
                    <label for="date_to">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="auth-field mb-0">
                    <label for="product_id">Produk</label>
                    <select class="form-select" id="product_id" name="product_id">
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
                    <input type="text" class="form-control" id="merchant_code" name="merchant_code" placeholder="MRC-001" value="{{ request('merchant_code') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i data-lucide="filter"></i> Filter</button>
                <a href="{{ route('report.index') }}" class="btn btn-light"><i data-lucide="rotate-ccw"></i> Reset</a>
            </div>
        </div>
    </form>
</div>

<span class="d-none" id="route-excel">{{ route('report.excel') }}</span>
<span class="d-none" id="route-pdf">{{ route('report.pdf') }}</span>

<div class="d-flex gap-2 mb-4">
    <button type="button" class="btn btn-success" id="export-excel-btn"><i data-lucide="file-spreadsheet"></i> Export Excel</button>
    <button type="button" class="btn btn-danger" id="export-pdf-btn"><i data-lucide="file-text"></i> Export PDF</button>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-success-subtle mx-auto mb-3"><i data-lucide="file-spreadsheet"></i></div>
                <h5 class="fw-bold">Export Excel</h5>
                <p class="text-secondary mb-0 detail-desc">Mengunduh data laporan penjualan dalam format spreadsheet dengan detail transaksi lengkap mencakup nomor transaksi, tanggal, merchant, produk, qty, harga, dan subtotal.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="stat-icon bg-danger-subtle mx-auto mb-3"><i data-lucide="file-text"></i></div>
                <h5 class="fw-bold">Export PDF</h5>
                <p class="text-secondary mb-0 detail-desc">Mengunduh laporan penjualan dalam format PDF yang siap cetak dengan ringkasan total pendapatan, total qty, dan jumlah transaksi.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport(url) {
    var form = document.getElementById('filter-form');
    var clone = form.cloneNode(true);
    clone.action = url;
    clone.method = 'GET';
    document.body.appendChild(clone);
    clone.submit();
    setTimeout(function() { clone.remove(); }, 100);
}
document.getElementById('export-excel-btn').addEventListener('click', function(e) {
    e.preventDefault();
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading...';
    exportReport(document.getElementById('route-excel').textContent);
    setTimeout(function() {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="file-spreadsheet"></i> Export Excel';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }, 5000);
});
document.getElementById('export-pdf-btn').addEventListener('click', function(e) {
    e.preventDefault();
    var btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading...';
    exportReport(document.getElementById('route-pdf').textContent);
    setTimeout(function() {
        btn.disabled = false;
        btn.innerHTML = '<i data-lucide="file-text"></i> Export PDF';
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }, 5000);
});
</script>
@endpush
@endsection
