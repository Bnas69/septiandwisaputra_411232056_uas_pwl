@extends('layouts.app')
@section('title', 'Tambah User')
@section('content')
<div class="page-header">
    <div>
        <h4><i data-lucide="user-plus"></i> Tambah User</h4>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li><li class="breadcrumb-item"><a href="{{ route('settings.users.index') }}">User Management</a></li><li class="breadcrumb-item active">Tambah</li></ol></nav>
    </div>
</div>

<x-error-alert />

<div class="form-card">
    <form action="{{ route('settings.users.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="form-section-title">Informasi Akun</div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required autofocus maxlength="255">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="username">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Username" required maxlength="30" pattern="[a-zA-Z0-9_]{3,30}">
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-secondary hint-text">Gunakan huruf kecil, angka, atau underscore.</small>
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="email@domain.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="role">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                        <option value="pegawai" {{ old('role') === 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="developer" {{ old('role') === 'developer' ? 'selected' : '' }}>Developer</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-section-title mt-4">Keamanan</div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="auth-field">
                    <label for="password">Password <span class="text-danger">*</span></label>
                    <div class="input-wrap @error('password') has-error @enderror">
                        <i data-lucide="lock" class="input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="Min. 8 karakter" required minlength="8">
                        <button type="button" class="input-toggle">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-text error-show">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="auth-field">
                    <label for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                    <div class="input-wrap">
                        <i data-lucide="lock" class="input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required>
                        <button type="button" class="input-toggle">
                            <i data-lucide="eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('settings.users.index') }}" class="btn btn-light px-4"><i data-lucide="x"></i> Batal</a>
            <button type="submit" class="btn btn-primary px-4"><i data-lucide="check"></i> Simpan</button>
        </div>
    </form>
</div>


@endsection
