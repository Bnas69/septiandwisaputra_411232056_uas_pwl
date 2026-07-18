@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="user"></i> Profil Saya</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Profil</li></ol></nav>
    </div>
</div>

@php
    $avatarColors = [
        1 => ['label' => 'Indigo', 'icon' => 'store', 'color' => 'primary'],
        2 => ['label' => 'Emerald', 'icon' => 'leaf', 'color' => 'success'],
        3 => ['label' => 'Amber', 'icon' => 'star', 'color' => 'warning'],
        4 => ['label' => 'Rose', 'icon' => 'heart', 'color' => 'danger'],
    ];
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i data-lucide="user" class="me-1"></i> Edit Profil</div>
            <div class="card-body">
                <x-error-alert />

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label fw-semibold profile-label">Pilih Avatar</label>
                        <div class="avatar-picker">
                            @foreach($avatarColors as $id => $info)
                                <label class="avatar-picker-item">
                                    <input type="radio" name="avatar" value="{{ $id }}" {{ old('avatar', $user->avatar ?? 1) == $id ? 'checked' : '' }}>
                                    <div class="avatar avatar-xl avatar-color-{{ $id }}"><i data-lucide="{{ $info['icon'] }}" style="width:24px;height:24px;"></i></div>
                                    <small class="text-secondary">{{ $info['label'] }}</small>
                                </label>
                            @endforeach
                        </div>
                        @error('avatar')
                            <div class="text-danger hint-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="auth-field">
                                <label for="name">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="auth-field">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="auth-field">
                                <label>Username</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->username }}" readonly disabled>
                                <small class="text-secondary hint-text">Username tidak dapat diubah.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="auth-field">
                                <label>Role</label>
                                <input type="text" class="form-control bg-light" value="{{ ucfirst($user->role) }}" readonly disabled>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <button type="submit" class="btn btn-primary"><i data-lucide="check"></i> Perbarui Profil</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center">
                @php
                    $previewIcons = [1 => 'store', 2 => 'leaf', 3 => 'star', 4 => 'heart'];
                    $previewIcon = $previewIcons[$user->avatar ?? 1] ?? 'user';
                @endphp
                <div class="avatar avatar-xl mx-auto mb-3 avatar-color-{{ $user->avatar ?? 1 }}"><i data-lucide="{{ $previewIcon }}" style="width:24px;height:24px;"></i></div>
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-secondary mb-3 profile-email-text">{{ $user->email }}</p>

                @if($user->role === 'developer')
                    <span class="badge-status bg-primary-subtle text-primary">Developer</span>
                @elseif($user->role === 'owner')
                    <span class="badge-status bg-info-subtle text-info">Owner</span>
                @elseif($user->role === 'pegawai')
                    <span class="badge-status bg-success-subtle text-success">Pegawai</span>
                @else
                    <span class="badge-status bg-secondary-subtle text-secondary">User</span>
                @endif

                <div class="mt-4 text-start">
                    <small class="text-secondary d-block mb-2 profile-date-text"><i data-lucide="calendar" class="profile-date-icon me-1"></i> Bergabung: {{ $user->created_at?->format('d M Y') ?? '-' }}</small>
                    @if($user->last_login_at)
                        <small class="text-secondary d-block profile-date-text"><i data-lucide="clock" class="profile-date-icon me-1"></i> Login terakhir: {{ $user->last_login_at->diffForHumans() }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
var avatarIcons = { 1: 'store', 2: 'leaf', 3: 'star', 4: 'heart' };
document.querySelectorAll('.avatar-picker input[type="radio"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var preview = document.querySelector('.avatar-xl');
        var newId = parseInt(this.value);
        if (preview) {
            var classes = preview.className.split(' ').filter(function(c) { return !c.startsWith('avatar-color-'); });
            classes.push('avatar-color-' + newId);
            preview.className = classes.join(' ');
            var icon = preview.querySelector('i');
            if (icon) {
                icon.setAttribute('data-lucide', avatarIcons[newId] || 'user');
                if (window.lucide) lucide.createIcons({ nodes: [icon] });
            }
        }
    });
});
</script>
@endpush
@endsection
