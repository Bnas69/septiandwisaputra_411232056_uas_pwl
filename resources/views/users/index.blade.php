@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="users"></i> Manajemen User</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">User Management</li></ol></nav>
    </div>
    <a href="{{ route('settings.users.create') }}" class="btn btn-primary"><i data-lucide="plus"></i> Tambah User</a>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>User</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-center col-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $userAvatarIcons = [1 => 'store', 2 => 'leaf', 3 => 'star', 4 => 'heart'];
                        $userAvatarIcon = $userAvatarIcons[$user->avatar ?? 1] ?? 'user';
                    @endphp
                    <tr>
                        <td>{{ method_exists($users, 'firstItem') ? $users->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm avatar-color-{{ $user->avatar ?? 1 }}"><i data-lucide="{{ $userAvatarIcon }}" style="width:14px;height:14px;"></i></div>
                                <span class="fw-semibold">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td><span class="text-secondary">{{ $user->username }}</span></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'developer')
                                <span class="badge-status bg-primary-subtle text-primary">Developer</span>
                            @elseif($user->role === 'owner')
                                <span class="badge-status bg-info-subtle text-info">Owner</span>
                            @elseif($user->role === 'pegawai')
                                <span class="badge-status bg-success-subtle text-success">Pegawai</span>
                            @else
                                <span class="badge-status bg-secondary-subtle text-secondary">User</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status === 'active')
                                <span class="badge-status badge-active">Active</span>
                            @elseif($user->status === 'inactive')
                                <span class="badge-status badge-inactive">Inactive</span>
                            @else
                                <span class="badge-status badge-suspended">Suspended</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('settings.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                <i data-lucide="pencil"></i>
                            </a>
                            <form action="{{ route('settings.users.destroy', $user->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Hapus">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i data-lucide="inbox"></i>
                                <p>Belum ada data user.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->total() > 0)
    <div class="card-footer d-flex justify-content-between align-items-center py-2 px-3">
        <span class="text-xs text-secondary">Menampilkan {{ $users->firstItem() }}-{{ $users->lastItem() }} dari {{ $users->total() }} data</span>
        @if($users->hasPages())
            {{ $users->links() }}
        @endif
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var form = this.closest('.delete-form');
        Swal.fire({
            title: 'Hapus User?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
@endsection
