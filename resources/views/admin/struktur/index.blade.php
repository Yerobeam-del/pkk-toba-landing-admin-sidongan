@extends('admin.layouts.app')
@section('title', 'Manajemen Struktur')
@section('page-title', 'Struktur Organisasi')

@section('content')

<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .struktur-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }

    .struktur-header h1 {
        font-size: 1.25rem !important;
    }

    .struktur-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }

    /* FIX: Toolbar responsive */
    .struktur-toolbar {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 1rem !important;
    }

    .struktur-toolbar .tabs-container {
        width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
        border-bottom: 1px solid rgba(0,0,0,0.06) !important;
        padding-bottom: 0.5rem !important;
    }

    .struktur-toolbar .tabs-container::-webkit-scrollbar {
        height: 4px;
    }

    .struktur-toolbar .tabs-container::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 4px;
    }

    .struktur-toolbar .tab-btn {
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }

    .struktur-toolbar .toolbar-controls {
        width: 100% !important;
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 0.75rem !important;
    }

    .struktur-toolbar .toolbar-controls form {
        width: 100% !important;
    }

    .struktur-toolbar .toolbar-controls input[type="text"] {
        width: 100% !important;
    }

    .struktur-toolbar .toolbar-controls select {
        width: 100% !important;
    }
}
</style>

<div style="margin-bottom:2rem">

    {{-- Header Section --}}
    <div class="struktur-header">
        <div style="flex:1;min-width:0">
            <h1>Struktur Organisasi</h1>
            <p>Kelola data sesuai bagan organisasi asli PKK Kabupaten Toba</p>
        </div>
        <a href="{{ route('admin.struktur.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Anggota
        </a>
    </div>

    {{-- TABS & SEARCH --}}
    <div class="struktur-toolbar" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1.5rem;flex-wrap:wrap">
        <div class="tabs-container" style="flex:1;min-width:0;display:flex;align-items:flex-end;gap:0.25rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem">
            @php
                $tabs = [
                    'pengurus' => ['label' => 'Pengurus Inti', 'count' => $pengurusCount],
                    'pokja1' => ['label' => 'POKJA I', 'count' => $pokja1Count],
                    'pokja2' => ['label' => 'POKJA II', 'count' => $pokja2Count],
                    'pokja3' => ['label' => 'POKJA III', 'count' => $pokja3Count],
                    'pokja4' => ['label' => 'POKJA IV', 'count' => $pokja4Count],
                ];
                $currentTab = request('tab', 'pengurus');
            @endphp
            @foreach($tabs as $key => $tabData)
                @php
                    $isActive = $currentTab === $key;
                    $url = request()->fullUrlWithQuery([
                        'tab' => $key,
                        'page_pengurus' => 1,
                        'page_pokja1' => 1,
                        'page_pokja2' => 1,
                        'page_pokja3' => 1,
                        'page_pokja4' => 1
                    ]);
                @endphp
                <a href="{{ $url }}" class="tab-btn {{ $isActive ? 'active' : '' }}"
                style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:8px;text-decoration:none;color:{{ $isActive ? 'var(--primary)' : 'var(--text-muted)' }};background:{{ $isActive ? 'rgba(13, 148, 136, 0.1)' : 'transparent' }};border:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;border-bottom:2px solid {{ $isActive ? 'var(--primary)' : 'transparent' }}"
                onmouseover="if(!this.classList.contains('active')){this.style.background='rgba(13, 148, 136, 0.05)';this.style.color='var(--primary)'}"
                onmouseout="if(!this.classList.contains('active')){this.style.background='transparent';this.style.color='var(--text-muted)'}">
                    {{ $tabData['label'] }}
                    @if($tabData['count'] > 0)
                        <span style="background:rgba(0,0,0,0.05);color:var(--text-muted);padding:2px 8px;border-radius:12px;font-size:0.75rem">{{ $tabData['count'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Search Form & Per Page --}}
        <div class="toolbar-controls" style="flex-shrink:0;display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem">
            {{-- Per Page Dropdown --}}
            <form method="GET" action="{{ route('admin.struktur.index') }}" style="display:flex;align-items:center;gap:0.5rem">
                <input type="hidden" name="tab" value="{{ $currentTab }}">
                <label style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;font-weight:500">Tampilkan:</label>
                <div style="position:relative">
                    <select name="per_page" onchange="this.form.submit()" style="padding:0.5rem 2.5rem 0.5rem 0.75rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;min-width:80px;transition:all 0.2s;cursor:pointer;background:white;appearance:none;-webkit-appearance:none;-moz-appearance:none" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </form>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.struktur.index') }}" style="flex-shrink:0">
                <input type="hidden" name="tab" value="{{ $currentTab }}">
                <div style="position:relative">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted)">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau jabatan..." style="padding:0.5rem 0.75rem 0.5rem 2.5rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;width:250px;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                    @if(request('search'))
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);text-decoration:none" title="Hapus pencarian">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="struktur-card">
        <div class="table-container" style="padding:0">

            @php
                $strukturColumns = [
                    [
                        'key' => 'photo_path',
                        'label' => 'Foto',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>',
                        'type' => 'image',
                        'initial_key' => 'name'
                    ],
                    [
                        'key' => 'name',
                        'label' => 'Nama',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                    ],
                    [
                        'key' => 'position',
                        'label' => 'Jabatan',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
                        'type' => 'callback',
                        'callback' => function($item, $value) {
                            return '<span class="position-badge" style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(13,148,166,0.1);color:var(--primary-dark);border:1px solid rgba(13,148,166,0.2)">' . $value . '</span>';
                        }
                    ],
                ];
            @endphp

            {{-- Tab 1: Pengurus Inti --}}
            <div id="tab-pengurus" class="tab-content" style="display: {{ $currentTab === 'pengurus' ? 'block' : 'none' }}">
                @include('admin.partials.table', [
                    'data' => $pengurusInti,
                    'columns' => $strukturColumns,
                    'emptyMessage' => 'Belum ada data pengurus inti.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy',
                    'actions' => ['edit', 'delete']
                ])
            </div>

            {{-- Tab 2: Pokja I --}}
            <div id="tab-pokja1" class="tab-content" style="display: {{ $currentTab === 'pokja1' ? 'block' : 'none' }}">
                @include('admin.partials.table', [
                    'data' => $pokja1,
                    'columns' => $strukturColumns,
                    'emptyMessage' => 'Belum ada anggota di Pokja I.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy',
                    'actions' => ['edit', 'delete']
                ])
            </div>

            {{-- Tab 3: Pokja II --}}
            <div id="tab-pokja2" class="tab-content" style="display: {{ $currentTab === 'pokja2' ? 'block' : 'none' }}">
                @include('admin.partials.table', [
                    'data' => $pokja2,
                    'columns' => $strukturColumns,
                    'emptyMessage' => 'Belum ada anggota di Pokja II.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy',
                    'actions' => ['edit', 'delete']
                ])
            </div>

            {{-- Tab 4: Pokja III --}}
            <div id="tab-pokja3" class="tab-content" style="display: {{ $currentTab === 'pokja3' ? 'block' : 'none' }}">
                @include('admin.partials.table', [
                    'data' => $pokja3,
                    'columns' => $strukturColumns,
                    'emptyMessage' => 'Belum ada anggota di Pokja III.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy',
                    'actions' => ['edit', 'delete']
                ])
            </div>

            {{-- Tab 5: Pokja IV --}}
            <div id="tab-pokja4" class="tab-content" style="display: {{ $currentTab === 'pokja4' ? 'block' : 'none' }}">
                @include('admin.partials.table', [
                    'data' => $pokja4,
                    'columns' => $strukturColumns,
                    'emptyMessage' => 'Belum ada anggota di Pokja IV.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy',
                    'actions' => ['edit', 'delete']
                ])
            </div>

        </div>
    </div>
</div>
@endsection
