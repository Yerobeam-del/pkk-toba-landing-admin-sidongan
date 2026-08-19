{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'SK & Dokumen')
@section('page-title', 'SK & Dokumen')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-sk-index.css') }}">


{{-- Header Section --}}
<div class="sk-header u-a12">
    <div class="u-flex-1-min">
        <h1 class="u-page-title-tight">SK & Dokumen</h1>
        <p class="u-muted">Kelola surat keputusan dan dokumen dari pusat</p>
    </div>
    <a href="{{ route('admin.sk.create') }}" class="btn btn-primary u-inline-flex-gap-2-nowrap">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Dokumen
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid u-a4">
    <div class="stat-card u-badge-blue">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Total Dokumen</p>
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
                <p class="u-subtitle">Published</p>
                <p class="u-h1-hero">{{ $stats['published'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card u-a29">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">Draft</p>
                <p class="u-h1-hero">{{ $stats['draft'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="u-mb-4">
    <div class="tabs-container u-tabs-row">
        @php
            $tabs = [
                'all' => ['label' => 'Semua Dokumen', 'count' => $stats['total']],
                'published' => ['label' => 'Published', 'count' => $stats['published']],
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
    <div class="sk-search-wrapper u-flex-1-min-200">
        <form method="GET" action="{{ route('admin.sk.index') }}">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <div class="u-relative">
                <svg class="u-position-left" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="sk-search-input u-input-icon-left" placeholder="Cari nama dokumen...">
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
    <div class="sk-perpage-wrapper u-flex-center-gap-2-shrink">
        <form method="GET" action="{{ route('admin.sk.index') }}" class="sk-form-wrapper u-flex-center-gap-2">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <label class="u-a3">Tampilkan:</label>
            <div class="u-relative">
                <select class="u-select-mini" name="per_page">
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
</div>

{{-- Main Card --}}
<div class="card u-a11">

    @php
        $dokumenColumns = [
            [
                'key' => 'name',
                'label' => 'Nama Dokumen',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '
                        <div style="display:flex;gap:0.75rem;align-items:flex-start">
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                            </div>
                            <div class="u-flex-1-min">
                                <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.15rem">' . \Str::limit($value, 50) . '</div>
                                <div class="u-text-muted-xs2">' . $item->file_name . '</div>
                            </div>
                        </div>
                    ';
                }
            ],
            [
                'key' => 'document_date',
                'label' => 'Tanggal',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return $value ? \Carbon\Carbon::parse($value)->locale('id')->translatedFormat('d F Y') : '-';
                }
            ],
            [
                'key' => 'file_size',
                'label' => 'Ukuran',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>',
            ],
            [
                'key' => 'status',
                'label' => 'Status',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    if ($value === 'published') {
                        return '<span class="u-a57"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Published</span>';
                    }
                    return '<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(234,179,8,0.1);color:#92400e"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Draft</span>';
                }
            ],
        ];
    @endphp

    {{-- Tab: Semua Dokumen --}}
    <div id="tab-all" class="tab-content" style="display: {{ $currentTab === 'all' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $allDocs,
            'columns' => $dokumenColumns,
            'emptyMessage' => 'Belum ada dokumen. Silakan tambah dokumen pertama Anda.',
            'editRoute' => 'admin.sk.edit',
            'deleteRoute' => 'admin.sk.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'file',
            'rowActions' => function($item) {
                return '
                    <a href="' . route('admin.sk.show', $item) . '" target="_blank" class="action-btn u-a18" title="Preview">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                ';
            }
        ])
    </div>

    {{-- Tab: Published --}}
    <div id="tab-published" class="tab-content" style="display: {{ $currentTab === 'published' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $publishedDocs,
            'columns' => collect($dokumenColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Belum ada dokumen yang published.',
            'editRoute' => 'admin.sk.edit',
            'deleteRoute' => 'admin.sk.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'file',
            'rowActions' => function($item) {
                return '
                    <a href="' . route('admin.sk.show', $item) . '" target="_blank" class="action-btn u-a18" title="Preview">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                ';
            }
        ])
    </div>

    {{-- Tab: Draft --}}
    <div id="tab-draft" class="tab-content" style="display: {{ $currentTab === 'draft' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $draftDocs,
            'columns' => collect($dokumenColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada dokumen dalam draft.',
            'editRoute' => 'admin.sk.edit',
            'deleteRoute' => 'admin.sk.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'file',
            'rowActions' => function($item) {
                return '
                    <a href="' . route('admin.sk.show', $item) . '" target="_blank" class="action-btn u-a18" title="Preview">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                ';
            }
        ])
    </div>
</div>

    <script src="{{ asset('assets/admin/js/admin-sk-index.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
