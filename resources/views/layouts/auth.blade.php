<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') — SmartCatalog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @stack('styles')
</head>
<body>
    <div class="auth-split">
        <div class="auth-visual">
            <div class="auth-visual-overlay"></div>
            <div class="visual-content">
                <div class="visual-logo"><i data-lucide="store"></i></div>
                <h1 class="visual-title">SmartCatalog</h1>
                <p class="visual-subtitle">Sistem manajemen inventaris dan penjualan cerdas untuk bisnis Anda.</p>
                <div class="visual-features">
                    <div class="visual-feature"><i data-lucide="bar-chart-3"></i><span>Dashboard analitik real-time</span></div>
                    <div class="visual-feature"><i data-lucide="package"></i><span>Manajemen produk &amp; stok</span></div>
                    <div class="visual-feature"><i data-lucide="receipt"></i><span>Transaksi penjualan terpadu</span></div>
                    <div class="visual-feature"><i data-lucide="shield-check"></i><span>Keamanan &amp; kontrol akses</span></div>
                </div>
            </div>
        </div>
        <div class="auth-form-side">
            @if(session('success'))
                <div class="auth-alert auth-alert-success" role="alert"><i data-lucide="circle-check"></i>{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i data-lucide="triangle-alert"></i>{{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="auth-alert auth-alert-warning" role="alert"><i data-lucide="circle-alert"></i>{{ session('warning') }}</div>
            @endif
            @yield('content')
            <div class="auth-footer">&copy; {{ date('Y') }} SmartCatalog. All rights reserved.</div>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    @vite('resources/js/app.js')
    @stack('scripts')
</body>
</html>
