@props([
    'data' => [],
    'columns' => [],
    'emptyMessage' => 'Belum ada data',
    'editRoute' => null,
    'deleteRoute' => null,
    'showRoute' => null,
    'actions' => ['edit', 'delete', 'show'],
    'rowActions' => null,
    'emptyIcon' => 'users' // Default: users (orang)
])

@php
    // Cek apakah data adalah paginator instance
    $isPaginator = $data instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $paginator = $isPaginator ? $data : null;

    // Tentukan ikon SVG berdasarkan nilai emptyIcon
    $emptySvg = '';
    if ($emptyIcon === 'monitor') {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>';
    } elseif ($emptyIcon === 'newspaper') {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>';
    } elseif ($emptyIcon === 'file') {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>';
    } elseif ($emptyIcon === 'template') {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>';
    } elseif ($emptyIcon === 'shield') {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>';
    } elseif ($emptyIcon === 'database') {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>';
    } else {
        $emptySvg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
    }
@endphp

<div class="desktop-table-view">
    @if(count($data) > 0)
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th style="{{ ($column['align'] ?? 'left') === 'right' ? 'text-align: right' : 'text-align: left' }}">
                            @if(isset($column['icon']))
                                <span class="header-icon">
                                    {!! $column['icon'] !!}
                                </span>
                            @endif
                            <span>{{ $column['label'] }}</span>
                        </th>
                    @endforeach

                    @if(count($actions) > 0)
                        <th class="text-right">
                            <span>Aksi</span>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    @foreach($columns as $column)
                        @php
                            $value = data_get($item, $column['key']);
                            $columnType = $column['type'] ?? 'text';
                        @endphp

                        <td style="padding: 1rem; {{ ($column['align'] ?? 'left') === 'right' ? 'text-align: right' : 'text-align: left' }}">
                            @if($columnType === 'image')
                                @if($value)
                                    <img src="{{ asset('storage/' . $value) }}" alt="{{ data_get($item, $column['alt_key'] ?? 'name') }}" class="member-photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div class="member-photo-placeholder" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #0d9488); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
                                        {{ strtoupper(substr(data_get($item, $column['initial_key'] ?? 'name'), 0, 1)) }}
                                    </div>
                                @endif

                            @elseif($columnType === 'badge')
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; {{ $column['badge_style'] ?? 'background: rgba(34,197,94,0.1); color: #166534;' }}">
                                    @if(isset($column['badge_icon']))
                                        {!! $column['badge_icon'] !!}
                                    @endif
                                    {{ $value }}
                                </span>

                            @elseif($columnType === 'callback')
                                {!! $column['callback']($item, $value) !!}

                            @elseif($columnType === 'html')
                                {!! $value !!}

                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach

                    {{-- Actions column --}}
                    @if(count($actions) > 0)
                        <td class="actions-cell" style="padding: 1rem; text-align: right;">
                            <div class="actions-container" style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                @if(in_array('show', $actions) && $showRoute)
                                    <a href="{{ route($showRoute, $item) }}" class="action-btn" title="Lihat" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#eff6ff'; this.style.color='#2563eb'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                @endif

                                @if(in_array('edit', $actions) && $editRoute)
                                    <a href="{{ route($editRoute, $item) }}" class="action-btn btn-edit" title="Edit" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#eff6ff'; this.style.color='#2563eb'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if(in_array('delete', $actions) && $deleteRoute)
                                    <button type="button" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes(data_get($item, 'name')) }}')" class="action-btn btn-delete" title="Hapus" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#fef2f2'; this.style.color='#ef4444'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            <line x1="10" y1="11" x2="10" y2="17"/>
                                            <line x1="14" y1="11" x2="14" y2="17"/>
                                        </svg>
                                    </button>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route($deleteRoute, $item) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif

                                @if(isset($rowActions))
                                    {!! $rowActions($item) !!}
                                @endif
                            </div>
                        </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PAGINATION DESKTOP - Otomatis muncul jika data adalah paginator --}}
    @if($isPaginator && $paginator->hasPages())
    <div style="margin-top:1.5rem;padding:1rem;border-top:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
        <div style="display:flex;gap:0.4rem">
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();

                if ($lastPage <= 5) {
                    $pages = range(1, $lastPage);
                } else {
                    if ($currentPage <= 3) {
                        $pages = [1, 2, 3, 4, '...', $lastPage];
                    } elseif ($currentPage >= $lastPage - 2) {
                        $pages = [1, '...', $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
                    } else {
                        $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
                    }
                }
            @endphp

            {{-- Previous Button --}}
            @if($paginator->onFirstPage())
                <button disabled style="padding:0.5rem 0.9rem;background:#fff;color:var(--text-muted);border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;font-weight:500;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0.25rem;cursor:default;opacity:0.5">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                    <span class="desktop-only">Previous</span>
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" style="padding:0.5rem 0.9rem;background:#fff;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0.875rem;font-weight:500;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0.25rem;transition:all 0.2s" onmouseover="this.style.background='#f8fafc';this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                    <span class="desktop-only">Previous</span>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($pages as $page)
                @if($page === '...')
                    <span style="padding:0.5rem 0.25rem;color:var(--text-muted);font-size:0.875rem">...</span>
                @elseif($page == $currentPage)
                    <button style="padding:0.5rem 0.9rem;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;border:1px solid var(--primary);border-radius:8px;font-size:0.875rem;font-weight:600;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0.25rem;cursor:default;box-shadow:0 2px 8px rgba(20,184,166,0.3)">
                        {{ $page }}
                    </button>
                @else
                    <a href="{{ $paginator->url($page) }}" style="padding:0.5rem 0.9rem;background:#fff;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0.875rem;font-weight:500;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0.25rem;transition:all 0.2s" onmouseover="this.style.background='#f8fafc';this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                        {{ $page }}
                    </a>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" style="padding:0.5rem 0.9rem;background:#fff;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0.875rem;font-weight:500;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0.25rem;transition:all 0.2s" onmouseover="this.style.background='#f8fafc';this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                    <span class="desktop-only">Next</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            @else
                <button disabled style="padding:0.5rem 0.9rem;background:#fff;color:var(--text-muted);border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;font-weight:500;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0.25rem;cursor:default;opacity:0.5">
                    <span class="desktop-only">Next</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            @endif
        </div>

        <div style="font-size:0.85rem;color:var(--text-muted)">
            Menampilkan <strong>{{ $paginator->firstItem() }}</strong> - <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
        </div>div>
    </div>
    @endif

    @else
        <div class="empty-state">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon">
                    {!! $emptySvg !!}
                </div>
            </div>
            <h3>Belum Ada Data</h3>
            <p>{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

<div class="mobile-card-view">
    @if(count($data) > 0)
        <div class="mobile-card-wrapper">
            @foreach($data as $item)
            <div class="member-card" style="padding:1.25rem;margin-bottom:1rem;background:#fff;border-radius:12px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04)">

                {{-- Nama Aplikasi di Atas (Center) --}}
                @php
                    $nameColumn = collect($columns)->first(fn($col) => in_array($col['key'], ['name', 'title', 'subject']));
                @endphp
                @if($nameColumn)
                    <div style="text-align:center;margin-bottom:1rem">
                        <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;line-height:1.3">
                            {{ data_get($item, $nameColumn['key']) }}
                        </div>
                        @if($nameColumn['key'] === 'name' && $item->short_name)
                            <div style="font-size:0.85rem;color:var(--text-muted);margin-top:0.25rem">{{ $item->short_name }}</div>
                        @endif
                    </div>
                @endif

                {{-- Icon/Logo di Tengah --}}
                @php
                    $iconColumn = collect($columns)->first(fn($col) => in_array($col['key'], ['icon', 'photo_path', 'logo']));
                @endphp
                @if($iconColumn)
                    @php
                        $iconValue = data_get($item, $iconColumn['key']);
                    @endphp
                    <div style="display:flex;justify-content:center;margin-bottom:1.25rem">
                        @if($iconColumn['type'] === 'image')
                            @if($iconValue)
                                <img src="{{ asset('storage/' . $iconValue) }}" alt="{{ $nameColumn ? data_get($item, $nameColumn['key']) : 'Icon' }}"
                                     style="width:80px;height:80px;border-radius:12px;object-fit:cover;background:#f8fafc;box-shadow:0 4px 12px rgba(0,0,0,0.08)">
                            @else
                                <div style="width:80px;height:80px;border-radius:12px;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.5rem;box-shadow:0 4px 12px rgba(0,0,0,0.08)">
                                    {{ strtoupper(substr($nameColumn ? data_get($item, $nameColumn['key']) : 'X', 0, 2)) }}
                                </div>
                            @endif
                        @elseif($iconColumn['type'] === 'callback' && isset($iconColumn['callback']))
                            <div style="width:80px;height:80px;border-radius:12px;overflow:hidden;background:#f8fafc;box-shadow:0 4px 12px rgba(0,0,0,0.08);display:flex;align-items:center;justify-content:center">
                                {!! $iconColumn['callback']($item, $iconValue) !!}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Info Grid: Tampilkan semua kolom kecuali icon dan name --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem">
                    @foreach($columns as $column)
                        @if(!in_array($column['key'], ['icon', 'photo_path', 'logo', 'name', 'title', 'subject']))
                            @php
                                $value = data_get($item, $column['key']);
                                $columnType = $column['type'] ?? 'text';
                            @endphp
                            <div style="padding:0.75rem;background:#f8fafc;border-radius:8px">
                                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem">{{ $column['label'] }}</div>
                                <div style="font-weight:600;color:var(--text-dark);font-size:0.9rem">
                                    @if($columnType === 'callback' && isset($column['callback']))
                                        {!! $column['callback']($item, $value) !!}
                                    @elseif($columnType === 'badge')
                                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;{{ $column['badge_style'] ?? 'background:rgba(34,197,94,0.1);color:#166534;' }}">
                                            @if(isset($column['badge_icon']))
                                                {!! $column['badge_icon'] !!}
                                            @endif
                                            {{ $value }}
                                        </span>
                                    @elseif($columnType === 'html')
                                        {!! $value !!}
                                    @else
                                        {{ $value ?? '-' }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Action Buttons --}}
                <div class="member-card-actions" style="display:flex;gap:0.5rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06)">
                    @if(in_array('show', $actions) && $showRoute)
                        <a href="{{ route($showRoute, $item) }}" class="action-btn btn-show" title="Lihat" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;transition:all 0.2s;cursor:pointer;text-decoration:none" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <span style="margin-left:0.5rem;font-weight:600">Lihat</span>
                        </a>
                    @endif
                    @if(in_array('edit', $actions) && $editRoute)
                        <a href="{{ route($editRoute, $item) }}" class="action-btn btn-edit" title="Edit" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;transition:all 0.2s;cursor:pointer;text-decoration:none" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span style="margin-left:0.5rem;font-weight:600">Edit</span>
                        </a>
                    @endif
                    @if(in_array('delete', $actions) && $deleteRoute)
                        <button type="button" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes(data_get($item, 'name') ?? data_get($item, 'title') ?? data_get($item, 'subject')) }}')" class="action-btn btn-delete" title="Hapus" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;border:none;cursor:pointer;transition:all 0.2s" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                            <span style="margin-left:0.5rem;font-weight:600">Hapus</span>
                        </button>
                        <form id="delete-form-{{ $item->id }}" action="{{ route($deleteRoute, $item) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Mobile Pagination --}}
        @if($isPaginator && $paginator->hasPages())
        <div style="margin-top:1.5rem;padding:1rem;border-top:1px solid rgba(0,0,0,0.06);text-align:center">
            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:0.75rem">
                Menampilkan <strong>{{ $paginator->firstItem() }}</strong> - <strong>{{ $paginator->lastItem() }}</strong> dari <strong>{{ $paginator->total() }}</strong> data
            </div>
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                if ($lastPage <= 5) {
                    $pages = range(1, $lastPage);
                } else {
                    if ($currentPage <= 3) {
                        $pages = [1, 2, 3, 4, '...', $lastPage];
                    } elseif ($currentPage >= $lastPage - 2) {
                        $pages = [1, '...', $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
                    } else {
                        $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
                    }
                }
            @endphp
            <div style="display:flex;justify-content:center;gap:0.3rem;flex-wrap:wrap;align-items:center">
                @if($paginator->onFirstPage())
                    <button disabled style="padding:0.5rem 0.7rem;background:#fff;color:var(--text-muted);border:1px solid #e2e8f0;border-radius:8px;font-size:0.8rem;min-width:36px;display:inline-flex;align-items:center;justify-content:center;opacity:0.5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" style="padding:0.5rem 0.7rem;background:#fff;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0.8rem;min-width:36px;display:inline-flex;align-items:center;justify-content:center;transition:all 0.2s" onmouseover="this.style.background='#f8fafc';this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif
                @foreach($pages as $page)
                    @if($page === '...')
                        <span style="padding:0.5rem 0.25rem;color:var(--text-muted);font-size:0.8rem">...</span>
                    @elseif($page == $currentPage)
                        <button style="padding:0.5rem 0.7rem;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;border:1px solid var(--primary);border-radius:8px;font-size:0.8rem;font-weight:600;min-width:36px;display:inline-flex;align-items:center;justify-content:center;cursor:default;box-shadow:0 2px 8px rgba(20,184,166,0.3)">
                            {{ $page }}
                        </button>
                    @else
                        <a href="{{ $paginator->url($page) }}" style="padding:0.5rem 0.7rem;background:#fff;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0.8rem;font-weight:500;min-width:36px;display:inline-flex;align-items:center;justify-content:center;transition:all 0.2s" onmouseover="this.style.background='#f8fafc';this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
                @if($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" style="padding:0.5rem 0.7rem;background:#fff;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0.8rem;min-width:36px;display:inline-flex;align-items:center;justify-content:center;transition:all 0.2s" onmouseover="this.style.background='#f8fafc';this.style.borderColor='var(--primary)';this.style.color='var(--primary)'" onmouseout="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.style.color='var(--text-dark)'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                @else
                    <button disabled style="padding:0.5rem 0.7rem;background:#fff;color:var(--text-muted);border:1px solid #e2e8f0;border-radius:8px;font-size:0.8rem;min-width:36px;display:inline-flex;align-items:center;justify-content:center;opacity:0.5">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                @endif
            </div>
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon">
                    {!! $emptySvg !!}
                </div>
            </div>
            <h3>Belum Ada Data</h3>
            <p>{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

<script>
if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            Toast.confirm(
                `Data <strong>"${name}"</strong> akan dihapus secara permanen.`,
                {
                    title: 'Hapus Data?',
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
            if (confirm(`Hapus data "${name}"?`)) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        }
    };
}
</script>
