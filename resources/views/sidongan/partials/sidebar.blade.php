@php
    $currentUser = auth()->guard('sidongan')->user();
@endphp

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('assets\sidongan\images\Logo-SIDONGAN.svg') }}" 
                alt="SIDONGAN" 
                class="sidebar-logo-img"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <i class="fas fa-file-signature" style="display:none;"></i>
        </div>
        <div class="sidebar-title">
            <h1>SIDONGAN</h1>
            <small>PKK Kabupaten Toba</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-title">Menu Utama</div>
        
        <a href="{{ route('sidongan.dashboard') }}" class="nav-item {{ request()->routeIs('sidongan.dashboard') ? 'active' : '' }}">
            <div class="nav-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </div>
            <span class="nav-text">Dashboard</span>
        </a>
        
        {{-- MENU KHUSUS SEKRETARIS --}}
        @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
        @php
            $isSuratActive = request()->routeIs('sidongan.documents.*');
        @endphp
        <div class="nav-item-wrapper" style="margin-bottom: 0.25rem;">
            <a href="javascript:void(0)" onclick="toggleSuratMenu(event)" class="nav-item has-submenu {{ $isSuratActive ? 'active' : '' }}" style="justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div class="nav-icon-box">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <span class="nav-text">Surat</span>
                </div>
                <svg id="suratArrow" class="nav-text" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition: transform 0.2s; {{ $isSuratActive ? 'transform: rotate(180deg);' : '' }}">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </a>
            <div id="suratSubmenu" class="surat-submenu" style="display: {{ $isSuratActive ? 'block' : 'none' }}; padding-left: 1.25rem; margin-top: 0.25rem;">
                <a href="{{ route('sidongan.documents.index') }}" class="nav-item {{ request()->routeIs('sidongan.documents.*') && !request()->routeIs('sidongan.documents.create') ? 'active' : '' }}" style="font-size: 0.85rem; padding: 0.5rem 0.75rem; margin-bottom: 0.125rem;">
                    <span class="nav-text">Daftar Surat</span>
                </a>
                <a href="{{ route('sidongan.documents.create') }}" class="nav-item {{ request()->routeIs('sidongan.documents.create') ? 'active' : '' }}" style="font-size: 0.85rem; padding: 0.5rem 0.75rem; margin-bottom: 0.125rem;">
                    <span class="nav-text">Buat Surat Baru</span>
                </a>
            </div>
        </div>

        <a href="{{ route('sidongan.lapor_kegiatan.index') }}" class="nav-item {{ request()->routeIs('sidongan.lapor_kegiatan*') ? 'active' : '' }}">
            <div class="nav-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="9 19 9 19"/>
                    <line x1="15" y1="19" x2="15" y2="19"/>
                </svg>
            </div>
            <span class="nav-text">Lapor Kegiatan</span>
        </a>
        @endif

        {{-- MENU KHUSUS KETUA PKK --}}
        @if($currentUser && $currentUser->hasSidonganRole('ketua'))
            <a href="{{ route('sidongan.documents.index') }}" class="nav-item {{ request()->routeIs('sidongan.documents.*') ? 'active' : '' }}">
                <div class="nav-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </div>
                <span class="nav-text">Surat</span>
            </a>
            <a href="{{ route('sidongan.disposisi') }}" class="nav-item {{ request()->routeIs('sidongan.disposisi*') ? 'active' : '' }}">
                <div class="nav-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="nav-text">Disposisi Surat</span>
            </a>
            <a href="{{ route('sidongan.verifikasi') }}" class="nav-item {{ request()->routeIs('sidongan.verifikasi*') ? 'active' : '' }}">
                <div class="nav-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span class="nav-text">Verifikasi Laporan</span>
            </a>
        @endif

        {{-- MENU KHUSUS BENDAHARA & KETUA POKJA --}}
        @if($currentUser && ($currentUser->hasSidonganRole('bendahara') || $currentUser->isSidonganPokja() || $currentUser->hasSidonganRole('staf_ahli_1') || $currentUser->hasSidonganRole('staf_ahli_2')))
            <a href="{{ route('sidongan.documents.index') }}" class="nav-item {{ request()->routeIs('sidongan.documents.*') ? 'active' : '' }}">
                <div class="nav-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                </div>
                <span class="nav-text">Daftar Surat</span>
            </a>
            
            <a href="{{ route('sidongan.lapor_kegiatan.index') }}" class="nav-item {{ request()->routeIs('sidongan.lapor_kegiatan*') ? 'active' : '' }}">
                <div class="nav-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <span class="nav-text">Lapor Kegiatan</span>
            </a>
        @endif

        {{-- Menu Umum --}}
        <a href="{{ route('sidongan.arsip') }}" class="nav-item {{ request()->routeIs('sidongan.arsip') ? 'active' : '' }}">
            <div class="nav-icon-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            </div>
            <span class="nav-text">Arsip Surat</span>
        </a>
    </nav>
</aside>