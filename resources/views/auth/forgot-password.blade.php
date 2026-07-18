@extends('layouts.auth')
@section('title', 'Lupa Password')
@section('content')
<div class="form-brand">
    <div class="form-brand-icon"><i data-lucide="key-round"></i></div>
    <h2>Lupa Password</h2>
    <p>Masukkan email Anda untuk menerima link reset password</p>
</div>
@if(session('status'))
    <div class="auth-alert auth-alert-info"><i data-lucide="info"></i>{{ session('status') }}</div>
@endif
<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="auth-field">
        <label for="email">Email <span class="required">*</span></label>
        <div class="input-wrap @error('email') has-error @enderror">
            <i data-lucide="mail" class="input-icon"></i>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required autofocus>
        </div>
        @error('email')<span class="error-text error-show">{{ $message }}</span>@enderror
    </div>
    <button type="submit" class="btn-submit"><span class="btn-text">Kirim Link Reset</span><div class="spinner"></div></button>
</form>
<div class="auth-footer"><a href="{{ route('login') }}" class="auth-link">Kembali ke login</a></div>
@endsection
