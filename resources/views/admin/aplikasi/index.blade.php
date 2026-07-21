@extends('admin.layouts.app')
@section('title', 'Manajemen Aplikasi')
@section('page-title', 'Aplikasi & Sistem')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
.aplikasi-header {
flex-direction: column !important;
align-items: flex-start !important;
gap: 1rem !important;
}

.aplikasi-header h1 {
font-size: 1.25rem !important;
}

.aplikasi-header .btn {
width: 100% !important;
justify-content: center !important;
}

.stats-grid {
grid-template-columns: 1fr !important;
}
}
</style>

{{-- Header Section --}}
<div class="aplikasi-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Aplikasi & Sistem</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola aplikasi dan sistem informasi PKK Kabupaten Toba</p>
    </div>
    <a href="{{ route('admin.aplikasi.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Aplikasi
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Total Aplikasi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#38a169,#2f855a);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Aplikasi Aktif</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['active'] }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#dd6b20,#c05621);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Maintenance</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['maintenance'] }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22h20"/><path d="M12 2v20"/><path d="M12 22V2"/><path d="M2 12h20"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Dalam Pengembangan</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['development'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards Baru untuk Visibility --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    {{-- Tampil di Beranda --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#14b8a6,#0d9488);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Tampil di Beranda</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['show_in_beranda'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tampil di Footer --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                </svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Tampil di Footer</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['show_in_footer'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tampil di Floating --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Tampil di Floating</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['show_in_floating'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

{{-- TABS & SEARCH --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1.5rem;flex-wrap:wrap">
    <div class="tabs-container" style="flex:1;min-width:0;display:flex;align-items:flex-end;gap:0.25rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem">
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
    <div style="flex-shrink:0;display:flex;align-items:center;gap:1rem;margin-bottom:0.5rem">
        {{-- Per Page Dropdown --}}
        <form method="GET" action="{{ route('admin.aplikasi.index') }}" style="display:flex;align-items:center;gap:0.5rem">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <label style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;font-weight:500">Tampilkan:</label>
            <div style="position:relative">
                <select name="per_page" onchange="this.form.submit()" style="padding:0.5rem 2.5rem 0.5rem 0.75rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;min-width:80px;transition:all 0.2s;cursor:pointer;background:white;appearance:none;-webkit-appearance:none;-moz-appearance:none" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                </select>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
        </form>

        {{-- Search Form --}}
        <form method="GET" action="{{ route('admin.aplikasi.index') }}" style="flex-shrink:0">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <div style="position:relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted)">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aplikasi..." style="padding:0.5rem 0.75rem 0.5rem 2.5rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;width:250px;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
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
<div class="card" style="padding:0;overflow:hidden;border:1px solid rgba(0,0,0,0.06);border-radius:12px">

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
                    return '<div style="font-weight:600;color:var(--text-dark)">' . $value . '</div><small style="color:var(--text-muted);font-size:0.85rem">' . $item->short_name . '</small>';
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
                    return '<span style="color:var(--text-muted);font-size:0.85rem">-</span>';
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
            'rowActions' => function($item) {
                return view('admin.aplikasi.partials.action-buttons', ['app' => $item])->render();
            }
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
            'rowActions' => function($item) {
                return view('admin.aplikasi.partials.action-buttons', ['app' => $item])->render();
            }
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
            'rowActions' => function($item) {
                return view('admin.aplikasi.partials.action-buttons', ['app' => $item])->render();
            }
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
            'rowActions' => function($item) {
                return view('admin.aplikasi.partials.action-buttons', ['app' => $item])->render();
            }
        ])
    </div>
</div>
@endsection
