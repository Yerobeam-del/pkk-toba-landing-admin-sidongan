@extends('admin.layouts.app')
@section('title', 'Template PKK')
@section('page-title', 'Template PKK')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .template-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    .template-header h1 { font-size: 1.25rem !important; }
    .template-header .btn { width: 100% !important; justify-content: center !important; }
    .stats-grid { grid-template-columns: 1fr !important; }
    .tabs-container { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
    .tabs-container::-webkit-scrollbar { height: 4px; }
    .tabs-container::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
    .tab-btn { white-space: nowrap !important; flex-shrink: 0 !important; }
}

/* Desktop: show table, hide card */
@media (min-width: 769px) {
    .desktop-table-view { display: block !important; }
    .mobile-card-view { display: none !important; }
}
</style>

{{-- Header Section --}}
<div class="template-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Template PKK</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola template dokumen resmi PKK</p>
    </div>
    <a href="{{ route('admin.template.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Template
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Total Template</p>
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
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Published</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['published'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Draft</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['draft'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- TABS & SEARCH --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1.5rem;flex-wrap:wrap">
    <div class="tabs-container" style="flex:1;min-width:0;display:flex;align-items:flex-end;gap:0.25rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem">
        @php
            $tabs = [
                'all' => ['label' => 'Semua Template', 'count' => $stats['total']],
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
        <form method="GET" action="{{ route('admin.template.index') }}" style="display:flex;align-items:center;gap:0.5rem">
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
        <form method="GET" action="{{ route('admin.template.index') }}" style="flex-shrink:0">
            <input type="hidden" name="tab" value="{{ $currentTab }}">
            <div style="position:relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted)">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari template..." style="padding:0.5rem 0.75rem 0.5rem 2.5rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;width:250px;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
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
        $templateColumns = [
            [
                'key' => 'name',
                'label' => 'Nama Template',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '
                        <div style="display:flex;gap:0.75rem;align-items:flex-start">
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <line x1="3" y1="9" x2="21" y2="9"/>
                                    <line x1="9" y1="21" x2="9" y2="9"/>
                                </svg>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.15rem">' . \Str::limit($value, 50) . '</div>
                                <div style="font-size:0.8rem;color:var(--text-muted)">' . $item->file_name . '</div>
                            </div>
                        </div>
                    ';
                }
            ],
            [
                'key' => 'upload_date',
                'label' => 'Tanggal',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return $value ? $value->format('d M Y') : '-';
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
                        return '<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Published</span>';
                    }
                    return '<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(234,179,8,0.1);color:#92400e"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="12" x2="16" y2="12"/></svg>Draft</span>';
                }
            ],
        ];
    @endphp

    {{-- Tab: Semua Template --}}
    <div id="tab-all" class="tab-content" style="display: {{ $currentTab === 'all' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $allTemplates,
            'columns' => $templateColumns,
            'emptyMessage' => 'Belum ada template. Silakan tambah template pertama Anda.',
            'editRoute' => 'admin.template.edit',
            'deleteRoute' => 'admin.template.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'file',
            'rowActions' => function($item) {
                return '
                    <a href="' . route('admin.template.show', $item) . '" target="_blank" class="action-btn" title="Preview" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background=\'#eff6ff\';this.style.color=\'#2563eb\'" onmouseout="this.style.background=\'transparent\';this.style.color=\'#94a3b8\'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                ';
            }
        ])
    </div>

    {{-- Tab: Published --}}
    <div id="tab-published" class="tab-content" style="display: {{ $currentTab === 'published' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $publishedTemplates,
            'columns' => collect($templateColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Belum ada template yang published.',
            'editRoute' => 'admin.template.edit',
            'deleteRoute' => 'admin.template.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'file',
            'rowActions' => function($item) {
                return '
                    <a href="' . route('admin.template.show', $item) . '" target="_blank" class="action-btn" title="Preview" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background=\'#eff6ff\';this.style.color=\'#2563eb\'" onmouseout="this.style.background=\'transparent\';this.style.color=\'#94a3b8\'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                ';
            }
        ])
    </div>

    {{-- Tab: Draft --}}
    <div id="tab-draft" class="tab-content" style="display: {{ $currentTab === 'draft' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $draftTemplates,
            'columns' => collect($templateColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada template dalam draft.',
            'editRoute' => 'admin.template.edit',
            'deleteRoute' => 'admin.template.destroy',
            'actions' => ['edit', 'delete'],
            'emptyIcon' => 'file',
            'rowActions' => function($item) {
                return '
                    <a href="' . route('admin.template.show', $item) . '" target="_blank" class="action-btn" title="Preview" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background=\'#eff6ff\';this.style.color=\'#2563eb\'" onmouseout="this.style.background=\'transparent\';this.style.color=\'#94a3b8\'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </a>
                ';
            }
        ])
    </div>
</div>

<script>
// Konfirmasi Hapus
if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            Toast.confirm(
                `Template <strong>"${name}"</strong> akan dihapus secara permanen.`,
                {
                    title: 'Hapus Template?',
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
            if (confirm(`Hapus template "${name}"?`)) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        }
    };
}
</script>
@endsection
