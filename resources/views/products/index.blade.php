@extends('layouts.app')
@section('title', 'Produk')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="package"></i> Produk</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Produk</li></ol></nav>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i data-lucide="plus"></i> Tambah Produk</a>
</div>

<div class="table-card">
    <div class="table-card-header">
        <form method="GET" action="{{ route('products.index') }}" class="d-flex">
            <div class="input-group search-box">
                <span class="input-group-text bg-light border-end-0"><i data-lucide="search"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="Cari kode atau nama produk..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary"><i data-lucide="search"></i></button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th class="text-end">Harga</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Stok Min</th>
                    <th class="text-center">Status</th>
                    <th class="text-center col-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $stockRow = $product->stock == 0 ? 'row-stock-critical'
                            : ($product->stock <= $product->minimum_stock ? 'row-stock-warning' : '');
                    @endphp
                    <tr class="{{ $stockRow }}">
                        <td>{{ method_exists($products, 'firstItem') ? $products->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td><span class="font-monospace fw-semibold">{{ $product->product_code }}</span></td>
                        <td class="fw-medium">{{ $product->product_name }}</td>
                        <td><span class="text-secondary">{{ $product->category ?? '-' }}</span></td>
                        <td class="text-end">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($product->stock == 0)
                                <span class="fw-semibold text-danger">{{ $product->stock }}</span>
                            @elseif($product->stock <= $product->minimum_stock)
                                <span class="fw-semibold text-warning">{{ $product->stock }}</span>
                            @else
                                <span class="fw-semibold text-success">{{ $product->stock }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $product->minimum_stock }}</td>
                        <td class="text-center">
                            @if($product->stock == 0)
                                <span class="badge-status badge-low-stock">Stok Habis</span>
                            @elseif($product->stock <= $product->minimum_stock)
                                <span class="badge-status badge-warn-stock">Stok Menipis</span>
                            @else
                                <span class="badge-status badge-normal-stock">Normal</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i data-lucide="square-pen"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus" data-id="{{ $product->id }}" data-name="{{ $product->product_name }}" onclick="deleteProduct(this.dataset.id, this.dataset.name)">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i data-lucide="inbox"></i>
                                <p>Belum ada data produk.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->total() > 0)
    <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
        <span class="text-xs text-secondary">Menampilkan {{ $products->firstItem() }}-{{ $products->lastItem() }} dari {{ $products->total() }} data</span>
        @if($products->hasPages())
            {{ $products->links() }}
        @endif
    </div>
    @endif
</div>

@foreach($products as $product)
    <form id="delete-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="delete-form-hidden">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function deleteProduct(id, name) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: 'Produk "' + name + '" akan dihapus permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
@endsection
