@extends('layouts.auth')
@section('title', 'Reset Password')
@section('content')
<div class="form-brand">
    <div class="form-brand-icon"><i data-lucide="shield-check"></i></div>
    <h2>Reset Password</h2>
    <p>Masukkan password baru Anda</p>
</div>
<form method="POST" action="{{ route('password.update') }}">
    @csrf @method('PUT')
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email }}">
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
        <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
        <div class="input-wrap">
            <i data-lucide="lock" class="input-icon"></i>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required autocomplete="new-password">
            <button type="button" class="input-toggle" aria-label="Toggle"><i data-lucide="eye"></i></button>
        </div>
    </div>
    <button type="submit" class="btn-submit"><span class="btn-text">Reset Password</span><div class="spinner"></div></button>
</form>
<div class="auth-footer"><a href="{{ route('login') }}" class="auth-link">Kembali ke login</a></div>

@push('scripts')
<script>
document.getElementById('password').addEventListener('input', function() {
    updateStrengthBar('strengthBar', 'password-hint', this.value);
});
</script>
@endpush
@endsection
