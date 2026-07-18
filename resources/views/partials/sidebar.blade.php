@php $role = auth()->user()?->role ?? ''; @endphp
<aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">SC</div>
        <span class="sidebar-brand-text">SmartCatalog</span>
    </a>
    <nav class="sidebar-nav" aria-label="Navigasi utama">
        <div class="nav-section-label">Menu</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="{{ request()->routeIs('dashboard') ? 'page' : 'false' }}">
                    <i data-lucide="layout-dashboard"></i><span>Dashboard</span>
                </a>
            </li>
        </ul>
        @if(in_array($role, ['developer', 'owner', 'pegawai']))
        <div class="nav-section-label">Master Data</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('merchants.index') }}" class="nav-link {{ request()->routeIs('merchants.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('merchants.*') ? 'page' : 'false' }}">
                    <i data-lucide="store"></i><span>Merchant</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('products.*') ? 'page' : 'false' }}">
                    <i data-lucide="package"></i><span>Produk</span>
                </a>
            </li>
        </ul>
        <div class="nav-section-label">Transaksi</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('sales.*') ? 'page' : 'false' }}">
                    <i data-lucide="receipt"></i><span>Penjualan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('stock.*') ? 'page' : 'false' }}">
                    <i data-lucide="package-check"></i><span>Stok</span>
                </a>
            </li>
        </ul>
        @endif
        @if(in_array($role, ['developer', 'owner']))
        <div class="nav-section-label">Laporan</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('report.index') }}" class="nav-link {{ request()->routeIs('report.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('report.*') ? 'page' : 'false' }}">
                    <i data-lucide="bar-chart-3"></i><span>Laporan</span>
                </a>
            </li>
        </ul>
        @endif
        @if($role === 'developer')
        <div class="nav-section-label">Pengaturan</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('settings.users.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" aria-current="{{ request()->routeIs('settings.*') ? 'page' : 'false' }}">
                    <i data-lucide="users"></i><span>User Mgmt</span>
                </a>
            </li>
        </ul>
        @endif
    </nav>
</aside>
