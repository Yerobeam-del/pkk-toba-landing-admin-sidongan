<aside class="sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="logo">PKK</div>
        <div>
            <h1>Admin Panel</h1>
            <small style="color:rgba(255,255,255,0.5)">Kabupaten Toba</small>
        </div>
    </div>
    <ul class="sidebar-nav">
        
        {{-- Beranda --}}
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Beranda
            </a>
        </li>

        {{-- Kelola Beranda (Hero Slider) --}}
        @if(auth()->user()->hasPermission('manage-hero-slider'))
        <li>
            <a href="{{ route('admin.hero-sliders.index') }}" class="{{ request()->routeIs('admin.hero-sliders.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Kelola Beranda
            </a>
        </li>
        @endif

        {{-- Struktur --}}
        @if(auth()->user()->hasPermission('manage-struktur'))
        <li>
            <a href="{{ route('admin.struktur.index') }}" class="{{ request()->routeIs('admin.struktur.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                Struktur
            </a>
        </li>
        @endif

        {{-- Aplikasi --}}
        @if(auth()->user()->hasPermission('manage-aplikasi'))
        <li>
            <a href="{{ route('admin.aplikasi.index') }}" class="{{ request()->routeIs('admin.aplikasi.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
                Aplikasi
            </a>
        </li>
        @endif

        {{-- Berita --}}
        @if(auth()->user()->hasPermission('manage-berita'))
        <li>
            <a href="{{ route('admin.berita.index') }}" class="{{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                    <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>
                </svg>
                Berita
            </a>
        </li>
        @endif

        {{-- Desa 
        @if(auth()->user()->hasPermission('manage-desa'))
        <li>
            <a href="{{ route('admin.desa.index') }}" class="{{ request()->routeIs('admin.desa.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Desa
            </a>
        </li>
        @endif --}}

        {{-- SK & Dokumen --}}
        @if(auth()->user()->hasPermission('manage-dokumen'))
        <li>
            <a href="{{ route('admin.sk.index') }}" class="{{ request()->routeIs('admin.sk.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
                SK & Dokumen
            </a>
        </li>
        @endif

        {{-- Template --}}
        @if(auth()->user()->hasPermission('manage-template'))
        <li>
            <a href="{{ route('admin.template.index') }}" class="{{ request()->routeIs('admin.template.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                Template
            </a>
        </li>
        @endif

        {{-- Tentang --}}
        @if(auth()->user()->hasPermission('manage-tentang'))
        <li>
            <a href="{{ route('admin.tentang.index') }}" class="{{ request()->routeIs('admin.tentang.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                Tentang
            </a>
        </li>
        @endif

    </ul>
</aside>