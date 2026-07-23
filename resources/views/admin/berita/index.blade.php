@extends('admin.layouts.app')
@section('title', 'Manajemen Berita')
@section('page-title', 'Daftar Berita')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .berita-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    .berita-header h1 { font-size: 1.25rem !important; }
    .berita-header .btn { width: 100% !important; justify-content: center !important; }
    .stats-grid { grid-template-columns: 1fr !important; }
    .tabs-container { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
    .tabs-container::-webkit-scrollbar { height: 4px; }
    .tabs-container::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
    .tab-btn { white-space: nowrap !important; flex-shrink: 0 !important; }

    /* Search & Tampilkan adjustments for mobile */
    .berita-search-wrapper {
        width: 100% !important;
        min-width: 100% !important;
    }
    .berita-search-input {
        width: 100% !important;
    }
    .berita-perpage-wrapper {
        width: 100% !important;
        justify-content: flex-end !important;
    }
    .berita-form-wrapper {
        width: auto !important;
    }
}
</style>

{{-- Header Section --}}
<div class="berita-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Daftar Berita</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola semua berita dan kegiatan PKK Kabupaten Toba</p>
    </div>
    <a href="{{ route('admin.berita.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Berita
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Total Berita</p>
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
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Dipublikasi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['published'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Draft</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['draft'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div style="margin-bottom:1rem">
    <div class="tabs-container" style="display:flex;align-items:flex-end;gap:0.25rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem;overflow-x:auto">
        @php
            $tabs = [
                'all' => ['label' => 'Semua Berita', 'count' => $stats['total']],
                'published' => ['label' => 'Dipublikasi', 'count' => $stats['published']],
                'draft' => ['label' => 'Draft', 'count' => $stats['draft']],
            ];
        @endphp
        @foreach($tabs as $key => $tabData)
            @php
                $isActive = $currentTab === $key;
                $url = request()->fullUrlWithQuery([
                    'tab' => $key,
                    'page_all' => 1,
                    'page_published' => 1,
                    'page_draft' => 1,
                    'search' => request('search')
                ]);
            @endphp
            <a href="{{ $url }}" class="tab-btn {{ $isActive ? 'active' : '' }}"
               style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:8px;text-decoration:none;color:{{ $isActive ? 'var(--primary)' : 'var(--text-muted)' }};background:{{ $isActive ? 'rgba(13, 148, 136, 0.1)' : 'transparent' }};border:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;border-bottom:2px solid {{ $isActive ? 'var(--primary)' : 'transparent' }};white-space:nowrap"
               onmouseover="if(!this.classList.contains('active')){this.style.background='rgba(13, 148, 136, 0.05)';this.style.color='var(--primary)'}"
               onmouseout="if(!this.classList.contains('active')){this.style.background='transparent';this.style.color='var(--text-muted)'}">
                {{ $tabData['label'] }}
                @if($tabData['count'] > 0)
                    <span style="background:rgba(0,0,0,0.05);color:var(--text-muted);padding:2px 8px;border-radius:12px;font-size:0.75rem">{{ $tabData['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>
</div>

{{-- Search & Tampilkan --}}
<div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    {{-- Search Form --}}
    <div class="berita-search-wrapper" style="flex:1;min-width:200px">
        <form method="GET" action="{{ route('admin.berita.index') }}">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <div style="position:relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted)">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="berita-search-input" placeholder="Cari judul atau kategori..." style="padding:0.5rem 0.75rem 0.5rem 2.5rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-size:0.9rem;width:100%;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='rgba(0,0,0,0.06)';this.style.boxShadow='none'">
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

    {{-- Per Page Dropdown --}}
    <div class="berita-perpage-wrapper" style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0">
        <form method="GET" action="{{ route('admin.berita.index') }}" class="berita-form-wrapper" style="display:flex;align-items:center;gap:0.5rem">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <label style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;font-weight:500">Tampilkan:</label>
            <div style="position:relative">
                <select name="per_page" onchange="this.form.submit()" style="padding:0.5rem 2.5rem 0.5rem 0.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-size:0.9rem;min-width:80px;transition:all 0.2s;cursor:pointer;background:white;appearance:none;-webkit-appearance:none;-moz-appearance:none" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='rgba(0,0,0,0.06)';this.style.boxShadow='none'">
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
    </div>
</div>

{{-- Main Card --}}
<div class="card" style="padding:0;overflow:hidden;border:1px solid rgba(0,0,0,0.06);border-radius:12px">

    @php
        $beritaColumns = [
            [
                'key' => 'title',
                'label' => 'Judul Berita',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    $imgHtml = $item->image_path
                        ? '<img src="' . asset('storage/' . $item->image_path) . '" style="width:60px;height:45px;object-fit:cover;border-radius:6px;background:#f8fafc" onerror="this.style.display=\'none\'">'
                        : '<div style="width:60px;height:45px;border-radius:6px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#94a3b8"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div>';

                    return '
                        <div style="display:flex;gap:1rem;align-items:flex-start">
                            ' . $imgHtml . '
                            <div style="flex:1;min-width:0">
                                <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.25rem">' . \Str::limit($value, 50) . '</div>
                                <p style="color:var(--text-muted);font-size:0.85rem;margin:0;line-height:1.4">' . \Str::limit($item->excerpt, 80) . '</p>
                            </div>
                        </div>
                    ';
                }
            ],
            [
                'key' => 'category',
                'label' => 'Kategori',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '<span style="background:rgba(59,130,246,0.1);color:#2563eb;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">' . $value . '</span>';
                }
            ],
            [
                'key' => 'published_at',
                'label' => 'Tanggal',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return $value ? \Carbon\Carbon::parse($value)->locale('id')->translatedFormat('d F Y') : '-';
                }
            ],
            [
                'key' => 'is_published',
                'label' => 'Status',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    if ($value) {
                        return '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Publik</span>';
                    }
                    return '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(234,179,8,0.1);color:#92400e"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Draft</span>';
                }
            ],
        ];
    @endphp

    {{-- Tab: Semua Berita --}}
    <div id="tab-all" class="tab-content" style="display: {{ $currentTab === 'all' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $allNews,
            'columns' => $beritaColumns,
            'emptyMessage' => 'Belum ada berita. Silakan tambah berita pertama Anda.',
            'editRoute' => 'admin.berita.edit',
            'deleteRoute' => 'admin.berita.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'newspaper'
        ])
    </div>

    {{-- Tab: Dipublikasi --}}
    <div id="tab-published" class="tab-content" style="display: {{ $currentTab === 'published' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $publishedNews,
            'columns' => collect($beritaColumns)->reject(fn($col) => $col['key'] === 'is_published')->values()->all(),
            'emptyMessage' => 'Belum ada berita yang dipublikasi.',
            'editRoute' => 'admin.berita.edit',
            'deleteRoute' => 'admin.berita.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'newspaper'
        ])
    </div>

    {{-- Tab: Draft --}}
    <div id="tab-draft" class="tab-content" style="display: {{ $currentTab === 'draft' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $draftNews,
            'columns' => collect($beritaColumns)->reject(fn($col) => $col['key'] === 'is_published')->values()->all(),
            'emptyMessage' => 'Tidak ada berita dalam draft.',
            'editRoute' => 'admin.berita.edit',
            'deleteRoute' => 'admin.berita.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'newspaper'
        ])
    </div>
</div>

<script>
// Konfirmasi Hapus (menggunakan ID form dari partial table)
if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            Toast.confirm(
                `Berita <strong>"${name}"</strong> akan dihapus secara permanen.`,
                {
                    title: 'Hapus Berita?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            ).then(function(confirmed) {
                if (confirmed) {
                    const form = document.getElementById('delete-form-' + id);
                    if (form) form.submit();
                }
            });
        } else {
            if (confirm(`Hapus berita "${name}"?`)) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        }
    };
}
</script>
@endsection
