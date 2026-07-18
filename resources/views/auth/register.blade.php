@extends('layouts.auth')
@section('title', 'Daftar')
@section('content')
<div class="form-brand">
    <div class="form-brand-icon"><i data-lucide="user-plus"></i></div>
    <h2>Buat Akun Baru</h2>
    <p>Daftar untuk mulai menggunakan SmartCatalog</p>
</div>
<form method="POST" action="{{ route('register.post') }}">
    @csrf
    <div class="auth-field">
        <label for="name">Nama Lengkap <span class="required">*</span></label>
        <div class="input-wrap @error('name') has-error @enderror">
            <i data-lucide="user" class="input-icon"></i>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus autocomplete="name">
        </div>
        @error('name')<span class="error-text error-show">{{ $message }}</span>@enderror
    </div>
    <div class="auth-field">
        <label for="username">Username <span class="required">*</span></label>
        <div class="input-wrap @error('username') has-error @enderror">
            <i data-lucide="at-sign" class="input-icon"></i>
            <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="username" required autocomplete="username">
        </div>
        @error('username')<span class="error-text error-show">{{ $message }}</span>@enderror
        <div id="username-hint" class="validation-hint"></div>
    </div>
    <div class="auth-field">
        <label for="email">Email <span class="required">*</span></label>
        <div class="input-wrap @error('email') has-error @enderror">
            <i data-lucide="mail" class="input-icon"></i>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required autocomplete="email">
        </div>
        @error('email')<span class="error-text error-show">{{ $message }}</span>@enderror
        <div id="email-hint" class="validation-hint"></div>
    </div>
    <div class="auth-field">
        <label for="password">Password <span class="required">*</span></label>
        <div class="input-wrap @error('password') has-error @enderror">
            <i data-lucide="lock" class="input-icon"></i>
            <input type="password" id="password" name="password" placeholder="Min. 8 karakter" required autocomplete="new-password">
            <button type="button" class="input-toggle" aria-label="Toggle password visibility"><i data-lucide="eye"></i></button>
        </div>
        @error('password')<span class="error-text error-show">{{ $message }}</span>@enderror
        <div class="password-strength-bar" id="strengthBar"><div class="fill"></div></div>
        <div id="password-hint" class="validation-hint"></div>
    </div>
    <div class="auth-field">
        <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
        <div class="input-wrap @error('password_confirmation') has-error @enderror">
            <i data-lucide="lock" class="input-icon"></i>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required autocomplete="new-password">
            <button type="button" class="input-toggle" aria-label="Toggle password visibility"><i data-lucide="eye"></i></button>
        </div>
        @error('password_confirmation')
            <span class="error-text error-show">{{ $message }}</span>
        @enderror
    </div>
    <button type="submit" class="btn-submit"><span class="btn-text">Daftar</span><div class="spinner"></div></button>
</form>
<div class="auth-footer">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></div>

@push('scripts')
<script>
(function() {
    var urlU = '{{ route("api.check-username") }}';
    var urlE = '{{ route("api.check-email") }}';
    var usernameTimer = null, emailTimer = null;

    document.getElementById('username').addEventListener('input', function() {
        var val = this.value.trim().toLowerCase();
        var hint = document.getElementById('username-hint');
        clearTimeout(usernameTimer);
        if (!val) { hint.textContent = ''; hint.className = 'validation-hint'; return; }
        if (!/^[a-z0-9_]+$/.test(val)) { hint.textContent = 'Hanya huruf kecil, angka, underscore.'; hint.className = 'validation-hint invalid'; return; }
        if (val.length < 3) { hint.textContent = 'Minimal 3 karakter.'; hint.className = 'validation-hint invalid'; return; }
        hint.textContent = 'Mengecek...'; hint.className = 'validation-hint';
        usernameTimer = setTimeout(function() {
            fetch(urlU + '?username=' + encodeURIComponent(val)).then(function(r) { return r.json(); }).then(function(d) {
                hint.textContent = d.available ? 'Tersedia.' : 'Sudah digunakan.';
                hint.className = 'validation-hint ' + (d.available ? 'valid' : 'invalid');
            }).catch(function() { hint.textContent = ''; hint.className = 'validation-hint'; });
        }, 500);
    });

    document.getElementById('email').addEventListener('input', function() {
        var val = this.value.trim();
        var hint = document.getElementById('email-hint');
        clearTimeout(emailTimer);
        if (!val) { hint.textContent = ''; hint.className = 'validation-hint'; return; }
        hint.textContent = 'Mengecek...'; hint.className = 'validation-hint';
        emailTimer = setTimeout(function() {
            fetch(urlE + '?email=' + encodeURIComponent(val)).then(function(r) { return r.json(); }).then(function(d) {
                hint.textContent = d.available ? 'Tersedia.' : 'Sudah terdaftar.';
                hint.className = 'validation-hint ' + (d.available ? 'valid' : 'invalid');
            }).catch(function() { hint.textContent = ''; hint.className = 'validation-hint'; });
        }, 500);
    });

    document.getElementById('password').addEventListener('input', function() {
        updateStrengthBar('strengthBar', 'password-hint', this.value);
    });
})();
</script>
@endpush
@endsection
