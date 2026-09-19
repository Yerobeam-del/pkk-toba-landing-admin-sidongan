{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Sidebar mode Area Sistem. Dipakai via @section('sysSidebar')
     dari view sistem yang mendefinisikan $sysArea:
       ['key' => 'akun'|'data-sieda'|'data-sidongan', 'label' => ..., 'desc' => ...]
     Isi: judul area + navigasi antar area + tombol ganti ruang kerja.
     ============================================================ --}}

<div class="nav-section-title">{{ $sysArea['label'] ?? 'Area Sistem' }}</div>

<a href="{{ route('admin.user-management.index') }}"
   class="sys-nav-item {{ ($sysArea['key'] ?? '') === 'akun' ? 'active' : '' }}">
    <div class="nav-icon-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </div>
    <span class="nav-text">Manajemen Akun</span>
</a>

@if (auth()->user()->isSuperAdmin())
    <a href="{{ route('admin.sieda-data.index') }}"
       class="sys-nav-item {{ ($sysArea['key'] ?? '') === 'data-sieda' ? 'active' : '' }}">
        <div class="nav-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
            </svg>
        </div>
        <span class="nav-text">Manajemen Data SIEDA</span>
    </a>

    <a href="{{ route('admin.sidongan-data.index') }}"
       class="sys-nav-item {{ ($sysArea['key'] ?? '') === 'data-sidongan' ? 'active' : '' }}">
        <div class="nav-icon-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        </div>
        <span class="nav-text">Manajemen Data SIDONGAN</span>
    </a>
@endif

<a href="{{ route('workspace.pilih') }}" class="sys-switch-btn">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"/>
    </svg>
    <span class="nav-text">Ganti Area Kerja</span>
</a>
