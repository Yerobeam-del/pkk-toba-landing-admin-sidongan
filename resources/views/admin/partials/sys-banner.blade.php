{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Banner konteks Area Sistem. Dipakai via @section('sysBanner')
     dari view sistem yang mendefinisikan $sysArea:
       ['key' => ..., 'label' => ..., 'desc' => ...]
     ============================================================ --}}

<div class="sys-context-banner">
    <div class="sys-context-icon">
        @if (($sysArea['key'] ?? '') === 'akun')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        @elseif (($sysArea['key'] ?? '') === 'data-sieda')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <ellipse cx="12" cy="5" rx="9" ry="3"/>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
            </svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        @endif
    </div>
    <div class="sys-context-text">
        <strong>{{ $sysArea['label'] ?? 'Area Sistem' }}</strong>
        <span>{{ $sysArea['desc'] ?? '' }}</span>
    </div>
    <a href="{{ route('workspace.pilih') }}" class="sys-context-switch">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 3H5a2 2 0 0 0-2 2v3"/>
            <path d="M21 8V5a2 2 0 0 0-2-2h-3"/>
            <path d="M3 16v3a2 2 0 0 0 2 2h3"/>
            <path d="M16 21h3a2 2 0 0 0 2-2v-3"/>
        </svg>
        Ganti Area
    </a>
</div>
