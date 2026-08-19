{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Manajemen Aplikasi')
@section('page-title', 'Aplikasi & Sistem')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-aplikasi-index.css') }}">


{{-- Header Section --}}
<div class="aplikasi-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title-tight">Aplikasi & Sistem</h1>
        <p class="u-muted">Kelola aplikasi dan sistem informasi PKK Kabupaten Toba</p>
    </div>
    <a href="{{ route('admin.aplikasi.create') }}" class="btn btn-primary u-inline-flex-gap-2-nowrap">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Aplikasi
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid u-a4">
    <div class="stat-card u-badge-blue">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Total Aplikasi</p>
                <p class="u-h1-hero">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card u-badge-green-solid">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Aplikasi Aktif</p>
                <p class="u-h1-hero">{{ $stats['active'] }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#dd6b20,#c05621);color:#fff">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Maintenance</p>
                <p class="u-h1-hero">{{ $stats['maintenance'] }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22h20"/><path d="M12 2v20"/><path d="M12 22V2"/><path d="M2 12h20"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Dalam Pengembangan</p>
                <p class="u-h1-hero">{{ $stats['development'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards Baru untuk Visibility --}}
<div class="stats-grid u-a4">
    {{-- Tampil di Beranda --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#14b8a6,#0d9488);color:#fff">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Tampil di Beranda</p>
                <p class="u-h1-hero">{{ $stats['show_in_beranda'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tampil di Footer --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                </svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Tampil di Footer</p>
                <p class="u-h1-hero">{{ $stats['show_in_footer'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tampil di Floating --}}
    <div class="stat-card u-a29">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Tampil di Floating</p>
                <p class="u-h1-hero">{{ $stats['show_in_floating'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="u-mb-4">
    <div class="tabs-container u-tabs-row">
        @php
            $tabs = [
                'all' => ['label' => 'Semua Aplikasi', 'count' => $stats['total']],
                'active' => ['label' => 'Aktif', 'count' => $stats['active']],
                'maintenance' => ['label' => 'Maintenance', 'count' => $stats['maintenance']],
                'development' => ['label' => 'Pengembangan', 'count' => $stats['development']],
            ];
        @endphp
        @foreach($tabs as $key => $tabData)
            @php
                $isActive = $currentTab === $key;
                $url = request()->fullUrlWithQuery([
                    'tab' => $key,
                    'page_all' => 1,
                    'page_active' => 1,
                    'page_maintenance' => 1,
                    'page_development' => 1,
                    'search' => request('search')
                ]);
            @endphp
            <a href="{{ $url }}" class="tab-btn {{ $isActive ? 'active' : '' }}"
               style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:8px;text-decoration:none;color:{{ $isActive ? 'var(--primary)' : 'var(--text-muted)' }};background:{{ $isActive ? 'rgba(13, 148, 136, 0.1)' : 'transparent' }};border:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;border-bottom:2px solid {{ $isActive ? 'var(--primary)' : 'transparent' }};white-space:nowrap">
                {{ $tabData['label'] }}
                @if($tabData['count'] > 0)
                    <span class="u-badge-soft">{{ $tabData['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>
</div>

{{-- Search & Tampilkan --}}
<div class="u-header-row-wrap">
    {{-- Search Form --}}
    <form class="u-flex-1-min-200" method="GET" action="{{ route('admin.aplikasi.index') }}">
        <input type="hidden" name="tab" value="{{ $currentTab }}">
        <div class="u-relative">
            <svg class="u-position-left" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input class="u-input-icon-left" type="text" name="search" value="{{ request('search') }}" placeholder="Cari aplikasi...">
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

    {{-- Per Page Dropdown --}}
    <form class="u-flex-center-gap-2-shrink" method="GET" action="{{ route('admin.aplikasi.index') }}">
        <input type="hidden" name="tab" value="{{ $currentTab }}">
        <label class="u-a3">Tampilkan:</label>
        <div class="u-relative">
            <select name="per_page" style="padding:0.5rem 2.5rem 0.5rem 0.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-size:0.9rem;min-width:80px;cursor:pointer;background:white;appearance:none;-webkit-appearance:none;-moz-appearance:none;transition:all 0.2s">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
            <svg class="u-select-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>
    </form>
</div>

{{-- Main Card --}}
<div class="card u-a11">

    @php
        $appColumns = [
            [
                'key' => 'icon',
                'label' => 'Logo',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    if ($value) {
                        return '<img src="' . asset('storage/' . $value) . '" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#f8fafc">';
                    }
                    return '<div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem">' . strtoupper(substr($item->short_name, 0, 2)) . '</div>';
                }
            ],
            [
                'key' => 'name',
                'label' => 'Nama Aplikasi',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '<div class="u-a30">' . $value . '</div><small class="u-a31">' . $item->short_name . '</small>';
                }
            ],
            [
                'key' => 'category',
                'label' => 'Kategori',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    $bgColor = $value == 'aplikasi' ? 'rgba(20,184,166,0.1)' : 'rgba(59,130,246,0.1)';
                    $textColor = $value == 'aplikasi' ? 'var(--primary)' : '#2563eb';
                    return '<span style="background:' . $bgColor . ';color:' . $textColor . ';padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">' . ucfirst($value) . '</span>';
                }
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return view('admin.aplikasi.partials.status-badge', ['app' => $item])->render();
                }
            ],
            [
                'key' => 'url',
                'label' => 'URL',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    if ($value && $value !== '#') {
                        return '<a href="' . $value . '" target="_blank" style="color:var(--primary);text-decoration:none;font-size:0.85rem;border-bottom:1px dotted var(--primary)">' . \Str::limit($value, 25) . '</a>';
                    }
                    return '<span class="u-a31">-</span>';
                }
            ],
            [
                'key' => 'sort_order',
                'label' => 'Urutan',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-2"/></svg>',
            ],
        ];
    @endphp

    {{-- Tab: Semua Aplikasi --}}
    <div id="tab-all" class="tab-content" style="display: {{ $currentTab === 'all' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $allApps,
            'columns' => $appColumns,
            'emptyMessage' => 'Belum ada aplikasi. Silakan tambah aplikasi pertama Anda.',
            'editRoute' => 'admin.aplikasi.edit',
            'deleteRoute' => 'admin.aplikasi.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'monitor'
        ])
    </div>

    {{-- Tab: Aplikasi Aktif --}}
    <div id="tab-active" class="tab-content" style="display: {{ $currentTab === 'active' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $activeApps,
            'columns' => collect($appColumns)->reject(fn($col) => in_array($col['key'], ['category', 'sort_order']))->values()->all(),
            'emptyMessage' => 'Belum ada aplikasi aktif.',
            'editRoute' => 'admin.aplikasi.edit',
            'deleteRoute' => 'admin.aplikasi.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'monitor'
        ])
    </div>

    {{-- Tab: Maintenance --}}
    <div id="tab-maintenance" class="tab-content" style="display: {{ $currentTab === 'maintenance' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $maintenanceApps,
            'columns' => collect($appColumns)->reject(fn($col) => in_array($col['key'], ['category', 'sort_order']))->values()->all(),
            'emptyMessage' => 'Tidak ada aplikasi dalam maintenance.',
            'editRoute' => 'admin.aplikasi.edit',
            'deleteRoute' => 'admin.aplikasi.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'monitor'
        ])
    </div>

    {{-- Tab: Pengembangan --}}
    <div id="tab-development" class="tab-content" style="display: {{ $currentTab === 'development' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $developmentApps,
            'columns' => collect($appColumns)->reject(fn($col) => in_array($col['key'], ['category', 'sort_order']))->values()->all(),
            'emptyMessage' => 'Belum ada aplikasi dalam pengembangan.',
            'editRoute' => 'admin.aplikasi.edit',
            'deleteRoute' => 'admin.aplikasi.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'monitor'
        ])
    </div>
</div>
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
