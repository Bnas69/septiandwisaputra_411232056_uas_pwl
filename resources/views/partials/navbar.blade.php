@php
    $avatarId = auth()->user()->avatar ?? 1;
    $avatarIcons = [1 => 'store', 2 => 'leaf', 3 => 'star', 4 => 'heart'];
    $avatarIcon = $avatarIcons[$avatarId] ?? 'user';
@endphp
<header class="top-navbar">
    <div class="navbar-left">
        <button class="btn-sidebar-toggle" id="btnSidebarToggle" aria-label="Toggle sidebar">
            <i data-lucide="menu"></i>
        </button>
    </div>
    <div class="navbar-right">
        <div class="navbar-clock d-none d-md-flex">
            <i data-lucide="clock"></i>
            <span id="clockDate"></span>
            <span class="clock-time" id="clockTime"></span>
        </div>
        <div class="dropdown user-dropdown">
            <button class="dropdown-toggle" data-bs-toggle="dropdown">
                <div class="user-avatar avatar-color-{{ $avatarId }}"><i data-lucide="{{ $avatarIcon }}" style="width:16px;height:16px;"></i></div>
                <div class="user-info d-none d-md-flex">
                    <span class="user-name">{{ auth()->user()->name ?? 'User' }}</span>
                    <span class="user-role">{{ ucfirst(auth()->user()->role ?? '') }}</span>
                </div>
                <i data-lucide="chevron-down" class="detail-header-icon"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('profile') }}"><i data-lucide="user"></i> Profil</a></li>
                <li><a class="dropdown-item" href="{{ route('change.password.form') }}"><i data-lucide="key-round"></i> Ubah Password</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" id="logout-form">
                        @csrf
                        <button type="button" class="dropdown-item text-danger" onclick="confirmLogout()">
                            <i data-lucide="log-out"></i> Keluar
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>
