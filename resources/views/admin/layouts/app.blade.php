<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - PKK Kabupaten Toba')</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Admin -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" integrity="sha512-...">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 80px;
            --header-height: 64px;
            --primary: #14b8a6;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: rgba(20,184,166,0.15);
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* RESET & BASE */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #334155;
            overflow-x: hidden;
        }

        /* LAYOUT WRAPPER */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--text-light);
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            transition: var(--transition);
            border-right: 1px solid var(--border);
        }

        .admin-layout.collapsed .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        /* Sidebar Header */
        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar-logo {
            width: 44px;
            height: 44px;
            background: transparent;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            padding: 4px;
        }

        .sidebar-logo .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            filter: brightness(0) invert(1);
        }

        .sidebar-title {
            transition: var(--transition);
            opacity: 1;
            width: auto;
        }

        .admin-layout.collapsed .sidebar-title {
            opacity: 0;
            width: 0;
        }

        .sidebar-title h1 { font-size: 1rem; font-weight: 700; line-height: 1.2; }
        .sidebar-title small { font-size: 0.7rem; color: var(--text-muted); font-weight: 500; }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            padding: 1rem 0.75rem;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            padding: 0.5rem 0.5rem;
            margin-top: 1rem;
            white-space: nowrap;
            transition: var(--transition);
        }

        .admin-layout.collapsed .nav-section-title {
            opacity: 0;
            height: 0;
            padding: 0;
            margin: 0;
        }

        /* MAIN CONTENT WRAPPER */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .admin-layout.collapsed .main-wrapper {
            margin-left: var(--sidebar-collapsed-width);
        }

        /* TOP HEADER */
        .top-header {
            height: var(--header-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .toggle-btn {
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            background: transparent;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            color: #64748b;
            transition: var(--transition);
        }

        .toggle-btn:hover { background: #f1f5f9; color: var(--primary); }
        .toggle-btn svg { width: 20px; height: 20px; }

        .header-right { display: flex; align-items: center; gap: 1rem; }
        .user-info { font-size: 0.9rem; font-weight: 600; color: #334155; }

        /* CONTENT AREA */
        .content-area {
            padding: 2rem;
            flex: 1;
            width: 100%;
            overflow-x: hidden;
        }

        /* Mobile Responsive */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .admin-layout.mobile-open .sidebar { transform: translateX(0); }
            .main-wrapper { margin-left: 0 !important; }
            .sidebar-overlay {
                display: none; position: fixed; inset: 0;
                background: rgba(0,0,0,0.5); z-index: 999;
                transition: opacity 0.3s;
            }
            .admin-layout.mobile-open .sidebar-overlay { display: block; opacity: 1; }
            .admin-layout.collapsed .sidebar { width: var(--sidebar-width); }
            .admin-layout.collapsed .nav-item .nav-text { opacity: 1; width: auto; position: static; box-shadow: none; }
        }

        /* User Profile Dropdown Animation */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .user-text { display: none !important; }
        }

        .user-profile-btn:hover .user-text span:first-child {
            color: var(--primary);
        }

        .admin-layout.collapsed .sidebar-logo {
            width: 36px;
            height: 36px;
            padding: 4px;
        }

        .admin-layout.collapsed .sidebar-logo .logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* ==========================================
           FIX CKEDITOR LIST DISPLAY
           ========================================== */

        /* Editor content area */
        .ck.ck-content ul,
        .ck.ck-content ol {
            padding-left: 2.5rem !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            list-style-position: outside !important;
        }

        .ck.ck-content ul li,
        .ck.ck-content ol li {
            margin: 0.5rem 0 !important;
            padding-left: 0.25rem !important;
            line-height: 1.8 !important;
        }

        /* Ensure list markers are visible */
        .ck.ck-content ul {
            list-style-type: disc !important;
        }

        .ck.ck-content ol {
            list-style-type: decimal !important;
        }

        /* Fix for nested lists */
        .ck.ck-content ul ul,
        .ck.ck-content ol ol,
        .ck.ck-content ul ol,
        .ck.ck-content ol ul {
            padding-left: 2rem !important;
            margin: 0.5rem 0 !important;
        }

        /* Blockquote fix */
        .ck.ck-content blockquote {
            padding: 0.5rem 1rem 0.5rem 1.5rem !important;
            margin: 1rem 0 !important;
            border-left: 4px solid #e2e8f0 !important;
            background: #f8fafc !important;
        }

        /* Also fix for frontend display */
        .news-detail-content ul,
        .news-detail-content ol {
            padding-left: 2rem !important;
            margin: 1rem 0 !important;
            list-style-position: outside !important;
        }

        .news-detail-content ul li,
        .news-detail-content ol li {
            margin: 0.5rem 0 !important;
            line-height: 1.8 !important;
        }

        /* ==========================================
        ADMIN FOOTER
        ========================================== */
        .admin-footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 1.5rem 2rem;
            position: sticky;
            bottom: 0;
            z-index: 100;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.05);
        }

        .admin-footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .admin-footer-left {
            color: #64748b;
            font-size: 0.875rem;
        }

        .admin-footer-left strong {
            color: #334155;
            font-weight: 600;
        }

        .admin-footer-right {
            display: flex;
            gap: 1.5rem;
            font-size: 0.875rem;
            color: #64748b;
            align-items: center;
        }

        .admin-footer-right strong {
            color: var(--primary);
            font-weight: 600;
        }

        .admin-footer-divider {
            color: rgba(0,0,0,0.15);
        }

        .admin-footer-link {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .admin-footer-link:hover {
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .admin-footer {
                padding: 1.25rem 1.5rem;
            }

            .admin-footer-inner {
                flex-direction: column;
                text-align: center;
                gap: 0.75rem;
            }

            .admin-footer-right {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

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
                <div class="nav-section-title">Main Navigation</div>

                {{-- Beranda (Semua user bisa akses) --}}
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </div>
                    <span class="nav-text">Beranda</span>
                </a>

                {{-- Kelola Beranda --}}
                @if(auth()->user()->hasPermission('manage-hero-slider'))
                <a href="{{ route('admin.hero-sliders.index') }}" class="nav-item {{ request()->routeIs('admin.hero-sliders.*') ? 'active' : '' }}">
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
                <a href="{{ route('admin.struktur.index') }}" class="nav-item {{ request()->routeIs('admin.struktur.*') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span class="nav-text">Struktur</span>
                </a>
                @endif

                {{-- Aplikasi --}}
                @if(auth()->user()->hasPermission('manage-aplikasi'))
                <a href="{{ route('admin.aplikasi.index') }}" class="nav-item {{ request()->routeIs('admin.aplikasi.*') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <span class="nav-text">Aplikasi</span>
                </a>
                @endif

                {{-- Berita --}}
                @if(auth()->user()->hasPermission('manage-berita'))
                <a href="{{ route('admin.berita.index') }}" class="nav-item {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
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
                <a href="{{ route('admin.sk.index') }}" class="nav-item {{ request()->routeIs('admin.sk.*') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <span class="nav-text">SK & Dokumen</span>
                </a>
                @endif

                {{-- Template --}}
                @if(auth()->user()->hasPermission('manage-template'))
                <a href="{{ route('admin.template.index') }}" class="nav-item {{ request()->routeIs('admin.template.*') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                    <span class="nav-text">Template</span>
                </a>
                @endif

                {{-- Tentang --}}
                @if(auth()->user()->hasPermission('manage-tentang'))
                <a href="{{ route('admin.tentang.index') }}" class="nav-item {{ request()->routeIs('admin.tentang.*') ? 'active' : '' }}">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </div>
                    <span class="nav-text">Tentang</span>
                </a>
                @endif

                {{-- Section Separator --}}
                @if(auth()->user()->hasPermission('manage-users'))
                <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.4); padding: 0.5rem 0.5rem; margin-bottom: 0.5rem;">
                        System
                    </div>

                    {{-- Manajemen Akun --}}
                    <a href="{{ route('admin.user-management.index') }}" class="nav-item {{ request()->routeIs('admin.user-management.*') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <span class="nav-text">Manajemen Akun</span>
                    </a>

                    {{-- Data SIDONGAN --}}
                    <a href="{{ route('admin.sidongan-data.index') }}" class="nav-item {{ request()->routeIs('admin.sidongan-data.*') ? 'active' : '' }}">
                        <div class="nav-icon-box">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                            </svg>
                        </div>
                        <span class="nav-text">Data SIDONGAN</span>
                    </a>
                </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-wrapper">
            <header class="top-header">
                <button class="toggle-btn" id="toggleBtn" title="Toggle Sidebar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                </button>
                <div class="header-right" style="position:relative">

                    {{-- User Profile Button --}}
                    <button onclick="toggleUserMenu()" class="user-profile-btn" style="display:flex;align-items:center;gap:0.75rem;background:none;border:none;cursor:pointer;padding:0.5rem 0.75rem;border-radius:8px;transition:background 0.2s" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">

                        {{-- User Info --}}
                        <div class="user-text" style="text-align:right;display:flex;flex-direction:column;align-items:flex-end">
                            <span style="font-weight:600;font-size:0.9rem;color:#334155;line-height:1.2">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span style="font-size:0.7rem;color:#94a3b8">{{ Auth::user()->role?->display_name ?? 'Administrator' }}</span>
                        </div>

                        {{-- User Avatar --}}
                        <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,0.1)">
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" style="width:100%;height:100%;object-fit:cover">
                            @else
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Dropdown Arrow --}}
                        <svg id="userMenuArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="transition:transform 0.2s">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div id="userMenu" style="display:none;position:absolute;right:0;top:calc(100% + 0.5rem);background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);min-width:220px;z-index:1000;animation:slideIn 0.2s ease">

                        {{-- Menu Header --}}
                        <div style="padding:0.75rem 1rem;border-bottom:1px solid #f1f5f9">
                            <div style="font-weight:600;font-size:0.9rem;color:#334155">Akun Saya</div>
                            <div style="font-size:0.75rem;color:#94a3b8">{{ Auth::user()->email }}</div>
                        </div>

                        {{-- Menu Items --}}
                        <div style="padding:0.5rem 0">
                            <a href="{{ route('admin.profile.edit') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;color:#334155;text-decoration:none;transition:background 0.2s;font-size:0.9rem" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Edit Profil</span>
                            </a>

                            <a href="{{ route('admin.profile.password') }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;color:#334155;text-decoration:none;transition:background 0.2s;font-size:0.9rem" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                                <span>Ubah Password</span>
                            </a>
                        </div>

                        {{-- Divider --}}
                        <div style="border-top:1px solid #f1f5f9;margin:0.5rem 0"></div>

                        {{-- Logout --}}
                        <form method="POST" action="{{ route('logout') }}" style="padding:0.5rem 0">
                            @csrf
                            <button type="submit" style="width:100%;display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;background:none;border:none;cursor:pointer;color:#ef4444;transition:background 0.2s;text-align:left;font-size:0.9rem" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const layout = document.getElementById('adminLayout');
            const toggleBtn = document.getElementById('toggleBtn');
            const overlay = document.getElementById('sidebarOverlay');
            const navItems = document.querySelectorAll('.nav-item');

            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const isMobile = window.innerWidth <= 1024;

            if (!isMobile && isCollapsed) {
                layout.classList.add('collapsed');
            }

            toggleBtn.addEventListener('click', () => {
                if (window.innerWidth <= 1024) {
                    layout.classList.toggle('mobile-open');
                } else {
                    layout.classList.toggle('collapsed');
                    localStorage.setItem('sidebarCollapsed', layout.classList.contains('collapsed'));
                }
            });

            navItems.forEach(item => {
                item.addEventListener('click', () => {
                    if (window.innerWidth <= 1024) {
                        layout.classList.remove('mobile-open');
                    }
                });
            });

            overlay.addEventListener('click', () => {
                layout.classList.remove('mobile-open');
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 1024) {
                    layout.classList.remove('mobile-open');
                    if (localStorage.getItem('sidebarCollapsed') === 'true') {
                        layout.classList.add('collapsed');
                    } else {
                        layout.classList.remove('collapsed');
                    }
                } else {
                    layout.classList.remove('collapsed');
                }
            });
        });

        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            const arrow = document.getElementById('userMenuArrow');

            if (menu.style.display === 'block') {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            } else {
                menu.style.display = 'block';
                arrow.style.transform = 'rotate(180deg)';
            }
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userMenu');
            const btn = e.target.closest('.user-profile-btn');

            if (!btn && menu.style.display === 'block') {
                menu.style.display = 'none';
                document.getElementById('userMenuArrow').style.transform = 'rotate(0deg)';
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const menu = document.getElementById('userMenu');
                menu.style.display = 'none';
                document.getElementById('userMenuArrow').style.transform = 'rotate(0deg)';
            }
        });
    </script>

    {{-- Toast Notification System --}}
    <script src="{{ asset('assets/admin/js/toast.js') }}"></script>

    {{-- Auto Show Session Messages --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Toast.success('{{ session('success') }}');
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Toast.error('{{ session('error') }}');
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Toast.warning('{{ session('warning') }}');
        });
    </script>
    @endif

    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Toast.info('{{ session('info') }}');
        });
    </script>
    @endif

    @stack('scripts')
</body>
</html>
