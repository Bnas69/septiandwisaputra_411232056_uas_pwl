@extends('layouts.auth')
@section('title', 'Login')
@section('content')
<div class="form-brand">
    <div class="form-brand-icon"><i data-lucide="log-in"></i></div>
    <h2>Selamat Datang</h2>
    <p>Masuk ke akun SmartCatalog Anda</p>
</div>
<form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="auth-field">
        <label for="email">Email <span class="required">*</span></label>
        <div class="input-wrap @error('email') has-error @enderror">
            <i data-lucide="mail" class="input-icon"></i>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required autofocus autocomplete="email">
        </div>
        @error('email')<span class="error-text error-show">{{ $message }}</span>@enderror
    </div>
    <div class="auth-field">
        <label for="password">Password <span class="required">*</span></label>
        <div class="input-wrap @error('password') has-error @enderror">
            <i data-lucide="lock" class="input-icon"></i>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
            <button type="button" class="input-toggle" aria-label="Toggle password visibility"><i data-lucide="eye"></i></button>
        </div>
        @error('password')<span class="error-text error-show">{{ $message }}</span>@enderror
    </div>
    <div class="auth-row">
        <label class="auth-check"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Ingat saya</label>
        <a href="{{ route('password.request') }}" class="auth-link">Lupa password?</a>
    </div>
    <button type="submit" class="btn-submit"><i data-lucide="arrow-right-to-line"></i><span class="btn-text">Masuk</span><div class="spinner"></div></button>
</form>
<div class="auth-footer">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></div>
@endsection
