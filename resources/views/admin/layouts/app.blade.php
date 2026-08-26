{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    
    <link rel="stylesheet" href="{{ asset('assets/shared/css/utilities.css') }}">
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - PKK Kabupaten Toba')</title>

    {{-- Favicon untuk Tab Browser --}}
    <link rel="icon" type="image/svg+xml" id="favicon" href="{{ asset('assets/admin/images/favicon-admin.svg') }}">
    <link rel="alternate icon" type="image/svg+xml" href="{{ asset('assets/admin/images/favicon-admin.svg') }}">

    {{-- Dark Mode: apply class SEBELUM CSS render agar tidak flicker --}}
    <script>
    (function(){
        var k='admin-dark-mode',s=localStorage.getItem(k);
        if(s==='true'||(s===null&&window.matchMedia&&window.matchMedia('(prefers-color-scheme:dark)').matches)){document.documentElement.classList.add('dark-mode');}
    })();
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Admin -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/layout.css') }}">

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-...">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    @stack('styles')
</head>
<body data-session-success="{{ session('success') }}"
      data-session-error="{{ session('error') }}"
      data-session-warning="{{ session('warning') }}"
      data-session-info="{{ session('info') }}">

    <div class="admin-layout" id="adminLayout">

        <!-- Overlay for Mobile -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}" alt="Logo PKK" class="logo-img">
                </div>
                <div class="sidebar-title">
                    <h1>Admin Panel</h1>
                    <small>PKK Kab. Toba</small>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section-title">Menu Utama</div>

                {{-- Beranda (Semua user bisa akses) --}}
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tip="Beranda">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </div>
                    <span class="nav-text">Beranda</span>
                </a>

                {{-- Kelola Beranda --}}
                @if(auth()->user()->hasPermission('manage-hero-slider'))
                <a href="{{ route('admin.hero-sliders.index') }}" class="nav-item {{ request()->routeIs('admin.hero-sliders.*') ? 'active' : '' }}" data-tip="Kelola Beranda">
                    <div class="nav-icon-box">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <span class="nav-text">Kelola Beranda</span>
                </a>
                @endif

                {{-- Struktur --}}
                @if(auth()->user()->hasPermission('manage-struktur'))
                <a href="{{ route('admin.struktur.index') }}" class="nav-item {{ request()->routeIs('admin.struktur.*') ? 'active' : '' }}" data-tip="Struktur">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span class="nav-text">Struktur</span>
                </a>
                @endif

                {{-- Aplikasi --}}
                @if(auth()->user()->hasPermission('manage-aplikasi'))
                <a href="{{ route('admin.aplikasi.index') }}" class="nav-item {{ request()->routeIs('admin.aplikasi.*') ? 'active' : '' }}" data-tip="Aplikasi">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <span class="nav-text">Aplikasi</span>
                </a>
                @endif

                {{-- Berita --}}
                @if(auth()->user()->hasPermission('manage-berita'))
                <a href="{{ route('admin.berita.index') }}" class="nav-item {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}" data-tip="Berita">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                    </div>
                    <span class="nav-text">Berita</span>
                </a>
                @endif

                {{-- Desa
                @if(auth()->user()->hasPermission('manage-desa'))
                <a href="{{ route('admin.desa.index') }}" class="nav-item {{ request()->routeIs('admin.desa.*') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <span class="nav-text">Desa</span>
                </a>
                @endif --}}

                {{-- SK & Dokumen --}}
                @if(auth()->user()->hasPermission('manage-dokumen'))
                <a href="{{ route('admin.sk.index') }}" class="nav-item {{ request()->routeIs('admin.sk.*') ? 'active' : '' }}" data-tip="SK & Dokumen">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <span class="nav-text">SK & Dokumen</span>
                </a>
                @endif

                {{-- Template --}}
                @if(auth()->user()->hasPermission('manage-template'))
                <a href="{{ route('admin.template.index') }}" class="nav-item {{ request()->routeIs('admin.template.*') ? 'active' : '' }}" data-tip="Template">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                    <span class="nav-text">Template</span>
                </a>
                @endif

                {{-- Tentang --}}
                @if(auth()->user()->hasPermission('manage-tentang'))
                <a href="{{ route('admin.tentang.index') }}" class="nav-item {{ request()->routeIs('admin.tentang.*') ? 'active' : '' }}" data-tip="Tentang">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <span class="nav-text">Tentang</span>
                </a>
                @endif

                {{-- Section Separator --}}
                @if(auth()->user()->hasPermission('manage-users'))
                <div class="sidebar-section-separator">
                    <div class="nav-section-title">Sistem</div>

                    {{-- Manajemen Akun --}}
                    <a href="{{ route('admin.user-management.index') }}" class="nav-item {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}" data-tip="Manajemen Akun">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <span class="nav-text">Manajemen Akun</span>
                    </a>

                    {{-- Data SIDONGAN (Hanya untuk Super Admin) --}}
                    @if(auth()->user()->sidongan_role === 'super_admin')
                    <a href="{{ route('admin.sidongan-data.index') }}" class="nav-item {{ request()->routeIs('admin.sidongan-data.*') ? 'active' : '' }}" data-tip="Data SIDONGAN">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                            </svg>
                        </div>
                        <span class="nav-text">Data SIDONGAN</span>
                    </a>
                    @endif

                    {{-- Manajemen Data SIEDA (Hanya Super Admin — hard delete permanen) --}}
                    @if(auth()->user()->sidongan_role === 'super_admin')
                    <a href="{{ route('admin.sieda-data.index') }}" class="nav-item {{ request()->routeIs('admin.sieda-data.*') ? 'active' : '' }}" data-tip="Manajemen SIEDA">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                                <path d="M8 16l4-4 4 4" opacity="0.5"/>
                            </svg>
                        </div>
                        <span class="nav-text">Manajemen SIEDA</span>
                    </a>
                    @endif
                </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="toggle-btn" id="toggleBtn" title="Toggle Sidebar">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="9" y1="3" x2="9" y2="21"></line>
                        </svg>
                    </button>
                    <button class="toggle-btn" id="darkModeToggle" title="Toggle Dark Mode">
                        <svg id="darkModeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                        </svg>
                    </button>
                </div>
                <div class="header-right">

                    {{-- User Profile Button --}}
                    <button type="button" class="user-profile-btn" aria-haspopup="true" aria-expanded="false" aria-controls="userMenu">

                        {{-- User Info --}}
                        <div class="user-text">
                            <span class="user-text-name">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="user-text-role">{{ Auth::user()->role?->display_name ?? 'Administrator' }}</span>
                        </div>

                        {{-- User Avatar --}}
                        <div class="user-avatar">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Dropdown Arrow --}}
                        <svg id="userMenuArrow" class="user-menu-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="userMenu" class="user-menu" role="menu" aria-labelledby="userMenuBtn">

                        {{-- Menu Header --}}
                        <div class="user-menu-header">
                            <div class="user-menu-header-title">Akun Saya</div>
                            <div class="user-menu-header-email">{{ Auth::user()->email }}</div>
                        </div>

                        {{-- Menu Items --}}
                        <div class="user-menu-body">
                            <a href="{{ route('admin.profile.edit') }}" class="user-menu-item" role="menuitem">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Edit Profil</span>
                            </a>
                        </div>

                        {{-- Divider --}}
                        <div class="user-menu-divider"></div>

                        {{-- Logout --}}
                        <form method="POST" action="{{ route('logout') }}" class="user-menu-footer">
                            @csrf
                            <button type="submit" class="user-menu-logout" role="menuitem">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="content-area">
                @include('admin.partials.breadcrumb')
                @yield('content')
            </main>

            {{-- Admin Footer --}}
            <footer class="admin-footer">
                <div class="admin-footer-inner">
                    <div class="admin-footer-left">
                        &copy; {{ date('Y') }} <strong>PKK Kabupaten Toba</strong>. All rights reserved.
                    </div>
                    <div class="admin-footer-right">
                        <span>Version 1.0.0</span>
                        <span class="admin-footer-divider">|</span>
                        <span>Developed by <strong>IT DEL</strong></span>
                        <span class="admin-footer-divider">|</span>
                        <a href="{{ route('admin.dashboard') }}" class="admin-footer-link">Dashboard</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Toast Notification System --}}
    {{-- Toast dipakai bersama Admin Panel & SIDONGAN, jadi berkasnya di assets/shared --}}
    <script src="{{ asset('assets/shared/js/toast.js') }}"></script>

    {{-- Pesan flash session (success/error/warning/info) — lihat data-* pada <body> --}}
    <script src="{{ asset('assets/admin/js/toast-messages.js') }}"></script>

    {{-- Layout: sidebar, tooltip, dropdown user --}}
    <script src="{{ asset('assets/admin/js/layout.js') }}"></script>

    {{-- Shared functions: checkbox, password, email check, form loading --}}
    <script src="{{ asset('assets/admin/js/admin-shared.js') }}"></script>

    {{-- Dark Mode Toggle — toggle button + icon + favicon (class sudah diaplikasikan di <head>) --}}
    <script>
    (function() {
        var STORAGE_KEY = 'admin-dark-mode';
        var html = document.documentElement;
        var toggle = document.getElementById('darkModeToggle');
        var icon = document.getElementById('darkModeIcon');
        var favicon = document.getElementById('favicon');

        var SUN_SVG = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        var MOON_SVG = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
        var FAVICON_LIGHT = '{{ asset("assets/admin/images/favicon-admin.svg") }}';
        var FAVICON_DARK = '{{ asset("assets/admin/images/favicon-admin-dark.svg") }}';

        var isDark = html.classList.contains('dark-mode');

        // Sync icon & favicon (class sudah ada dari <head> script)
        if (icon) icon.innerHTML = isDark ? SUN_SVG : MOON_SVG;
        if (favicon) favicon.href = isDark ? FAVICON_DARK : FAVICON_LIGHT;

        function setTheme(dark) {
            isDark = dark;
            if (dark) {
                html.classList.add('dark-mode');
                if (icon) icon.innerHTML = SUN_SVG;
                if (favicon) favicon.href = FAVICON_DARK;
            } else {
                html.classList.remove('dark-mode');
                if (icon) icon.innerHTML = MOON_SVG;
                if (favicon) favicon.href = FAVICON_LIGHT;
            }
        }

        // Toggle button
        if (toggle) {
            toggle.addEventListener('click', function() {
                setTheme(!html.classList.contains('dark-mode'));
                localStorage.setItem(STORAGE_KEY, isDark);
            });
        }

        // Listen for OS theme changes (only when user has NOT set a manual preference)
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                if (localStorage.getItem(STORAGE_KEY) === null) {
                    setTheme(e.matches);
                }
            });
        }
    })();
    </script>

    {{-- Keyboard Shortcuts --}}
    <script>
    (function() {
        document.addEventListener('keydown', function(e) {
            // Ctrl+K = Focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const search = document.querySelector('input[name="search"]');
                if (search) search.focus();
            }
            // Escape = Close modals
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay, [style*="position:fixed"][style*="inset:0"]').forEach(m => {
                    if (m.id && (m.id.includes('Modal') || m.id.includes('modal'))) {
                        m.style.display = 'none';
                    }
                });
            }
        });
    })();
    </script>

    @stack('scripts')
</body>
</html>
