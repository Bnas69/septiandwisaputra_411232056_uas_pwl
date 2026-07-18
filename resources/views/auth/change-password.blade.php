@extends('layouts.app')
@section('title', 'Ubah Password')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="key-round"></i> Ubah Password</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item active">Ubah Password</li></ol></nav>
    </div>
</div>

<div class="form-card form-card-narrow">
    <form method="POST" action="{{ route('change.password') }}">
        @csrf
        @method('PUT')

        <div class="form-section-title">Perbarui Password Akun Anda</div>

        <div class="auth-field">
            <label for="current_password">Password Saat Ini <span class="required">*</span></label>
            <div class="input-wrap @error('current_password') has-error @enderror">
                <i data-lucide="lock" class="input-icon"></i>
                <input type="password" id="current_password" name="current_password" placeholder="Masukkan password saat ini" required autocomplete="current-password">
                <button type="button" class="input-toggle" aria-label="Toggle"><i data-lucide="eye"></i></button>
            </div>
            @error('current_password')<span class="error-text error-show">{{ $message }}</span>@enderror
        </div>

        <div class="auth-field">
            <label for="password">Password Baru <span class="required">*</span></label>
            <div class="input-wrap @error('password') has-error @enderror">
                <i data-lucide="lock" class="input-icon"></i>
                <input type="password" id="password" name="password" placeholder="Min. 8 karakter" required autocomplete="new-password">
                <button type="button" class="input-toggle" aria-label="Toggle"><i data-lucide="eye"></i></button>
            </div>
            @error('password')<span class="error-text error-show">{{ $message }}</span>@enderror
            <div class="password-strength-bar" id="strengthBar"><div class="fill"></div></div>
            <div id="password-hint" class="validation-hint"></div>
        </div>

        <div class="auth-field">
            <label for="password_confirmation">Konfirmasi Password Baru <span class="required">*</span></label>
            <div class="input-wrap @error('password_confirmation') has-error @enderror">
                <i data-lucide="lock" class="input-icon"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" required autocomplete="new-password">
                <button type="button" class="input-toggle" aria-label="Toggle"><i data-lucide="eye"></i></button>
            </div>
            @error('password_confirmation')<span class="error-text error-show">{{ $message }}</span>@enderror
        </div>

        <button type="submit" class="btn-submit mt-2"><span class="btn-text">Ubah Password</span><div class="spinner"></div></button>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('password').addEventListener('input', function() {
    updateStrengthBar('strengthBar', 'password-hint', this.value);
});
</script>
@endpush
@endsection
