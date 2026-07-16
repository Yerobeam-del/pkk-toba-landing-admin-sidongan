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
    
    .template-header h1 {
        font-size: 1.25rem !important;
    }
    
    .template-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .filter-form {
        flex-direction: column !important;
    }
    
    .filter-form > div,
    .filter-form select {
        width: 100% !important;
        min-width: 100% !important;
    }
    
    /* Hide desktop table, show mobile cards */
    .desktop-table-view {
        display: none !important;
    }
    
    .mobile-card-view {
        display: block !important;
    }
    
    .mobile-card-view .template-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: #fff;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    
    .mobile-card-view .template-card-header {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .mobile-card-view .template-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(139,92,246,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .mobile-card-view .template-card-info {
        flex: 1;
        min-width: 0;
    }
    
    .mobile-card-view .template-card-name {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.15rem;
        font-size: 0.95rem;
    }
    
    .mobile-card-view .template-card-filename {
        font-size: 0.75rem;
        color: var(--text-muted);
        word-break: break-all;
    }
    
    .mobile-card-view .template-card-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
    }
    
    .mobile-card-view .template-card-meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .mobile-card-view .template-card-meta-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
    }
    
    .mobile-card-view .template-card-meta-value {
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .mobile-card-view .template-card-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    
    .mobile-card-view .template-card-actions a,
    .mobile-card-view .template-card-actions button {
        flex: 1;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #94a3b8;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
    }
    
    .mobile-card-view .template-card-actions a:hover {
        background: #eff6ff;
        color: #2563eb;
    }
    
    .mobile-card-view .template-card-actions button:hover {
        background: #fef2f2;
        color: #ef4444;
    }
}

/* Desktop: show table, hide card */
@media (min-width: 769px) {
    .desktop-table-view {
        display: block !important;
    }
    
    .mobile-card-view {
        display: none !important;
    }
}
</style>

{{-- Header Section --}}
<div class="template-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Template PKK</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola template dokumen resmi PKK</p>
    </div>
    <a href="{{ route('admin.template.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Template
    </a>
</div>

{{-- Filter Form --}}
<div class="card" style="margin-bottom:1.5rem;padding:1.25rem">
    <form id="filterForm" method="GET" action="{{ route('admin.template.index') }}" class="filter-form" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center">
        
        {{-- Search Input with Icon --}}
        <div style="position:relative;flex:1;min-width:250px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                 style="position:absolute;left:0.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" 
                   placeholder="Cari template..." 
                   style="width:100%;padding:0.65rem 1rem 0.65rem 2.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-family:inherit;font-size:0.9rem" 
                   oninput="clearTimeout(window._t);window._t=setTimeout(()=>this.form.submit(),600)">
        </div>
        
        {{-- Status Dropdown --}}
        <div style="position:relative;min-width:180px">
            <select name="status" class="form-control" 
                    style="width:100%;padding:0.65rem 2.5rem 0.65rem 1rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.75rem center;background-size:18px;cursor:pointer" 
                    onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status')==='published'?'selected':'' }}>Published</option>
                <option value="draft" {{ request('status')==='draft'?'selected':'' }}>Draft</option>
            </select>
        </div>
        
        {{-- Reset Button --}}
        @if(request('search')||request('status'))
        <a href="{{ route('admin.template.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;display:inline-flex;align-items:center;gap:0.5rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Reset
        </a>
        @endif
    </form>
</div>

{{-- Desktop Table View --}}
<div class="desktop-table-view">
    <div class="card" style="padding:0;overflow:hidden;border:1px solid rgba(0,0,0,0.06);border-radius:12px">
        <div class="table-container" style="padding:1rem">
            <table style="width:100%;border-collapse:collapse">
                <thead style="background:#f8fafc">
                    <tr>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08);width:60px">No</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Nama Template</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Tanggal</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Ukuran</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Status</th>
                        <th style="padding:0.875rem 1rem;text-align:right;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08);width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $i=>$t)
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                        <td style="padding:0.875rem 1rem;color:var(--text-muted);font-size:0.9rem">{{ ($templates->currentPage()-1)*$templates->perPage()+$i+1 }}</td>
                        <td style="padding:0.875rem 1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:10px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                        <line x1="9" y1="21" x2="9" y2="9"/>
                                    </svg>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.15rem">{{ Str::limit($t->name, 50) }}</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted)">{{ $t->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:0.875rem 1rem;color:var(--text-muted);font-size:0.9rem">{{ $t->upload_date?->format('d M Y') ?? '-' }}</td>
                        <td style="padding:0.875rem 1rem">
                            <span style="background:#f1f5f9;padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;color:var(--text-muted);font-weight:500">{{ $t->file_size }}</span>
                        </td>
                        <td style="padding:0.875rem 1rem">
                            @if($t->status==='published')
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    Published
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(234,179,8,0.1);color:#92400e">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td style="padding:0.875rem 1rem;text-align:right">
                            <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex;align-items:center">
                                <a href="{{ $t->file_url }}" target="_blank" title="Preview" 
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                
                                <a href="{{ route('admin.template.edit', $t) }}" title="Edit" 
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                
                                <button type="button" onclick="confirmDeleteTemplate({{ $t->id }}, '{{ addslashes(Str::limit($t->name, 40)) }}')" title="Hapus" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;border:none;cursor:pointer"
                                        onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                                
                                <form id="delete-template-{{ $t->id }}" action="{{ route('admin.template.destroy', $t) }}" method="POST" style="display:none">
                                    @csrf 
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
                                <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                                        <line x1="3" y1="9" x2="21" y2="9"/>
                                        <line x1="9" y1="21" x2="9" y2="9"/>
                                    </svg>
                                </div>
                                <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Belum ada template</h3>
                                <p style="font-size:0.9rem;margin:0 0 1rem">Mulai tambahkan template dokumen pertama Anda</p>
                                <a href="{{ route('admin.template.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Tambah Template Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($templates->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem">
            <div style="color:var(--text-muted);font-size:0.9rem">
                Menampilkan {{ $templates->firstItem() }}-{{ $templates->lastItem() }} dari {{ $templates->total() }} template
            </div>
            {!! $templates->withQueryString()->links() !!}
        </div>
        @endif
    </div>
</div>

{{-- Mobile Card View --}}
<div class="mobile-card-view" style="padding:0">
    @forelse($templates as $i=>$t)
    <div class="template-card">
        <div class="template-card-header">
            <div class="template-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
            </div>
            <div class="template-card-info">
                <div class="template-card-name">{{ Str::limit($t->name, 50) }}</div>
                <div class="template-card-filename">{{ $t->file_name }}</div>
            </div>
        </div>
        <div class="template-card-meta">
            <div class="template-card-meta-item">
                <span class="template-card-meta-label">Tanggal</span>
                <span class="template-card-meta-value">{{ $t->upload_date?->format('d M Y') ?? '-' }}</span>
            </div>
            <div class="template-card-meta-item">
                <span class="template-card-meta-label">Ukuran</span>
                <span class="template-card-meta-value">{{ $t->file_size }}</span>
            </div>
            <div class="template-card-meta-item">
                <span class="template-card-meta-label">Status</span>
                <span class="template-card-meta-value">
                    @if($t->status==='published')
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Published
                        </span>
                    @else
                        <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;background:rgba(234,179,8,0.1);color:#92400e">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
                            Draft
                        </span>
                    @endif
                </span>
            </div>
            <div class="template-card-meta-item">
                <span class="template-card-meta-label">No. Urut</span>
                <span class="template-card-meta-value">{{ ($templates->currentPage()-1)*$templates->perPage()+$i+1 }}</span>
            </div>
        </div>
        <div class="template-card-actions">
            <a href="{{ $t->file_url }}" target="_blank" title="Preview" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <a href="{{ route('admin.template.edit', $t) }}" title="Edit" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            <button type="button" onclick="confirmDeleteTemplate({{ $t->id }}, '{{ addslashes(Str::limit($t->name, 40)) }}')" title="Hapus" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </button>
            <form id="delete-template-{{ $t->id }}" action="{{ route('admin.template.destroy', $t) }}" method="POST" style="display:none">
                @csrf 
                @method('DELETE')
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
        <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <line x1="3" y1="9" x2="21" y2="9"/>
                <line x1="9" y1="21" x2="9" y2="9"/>
            </svg>
        </div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Belum ada template</h3>
        <p style="font-size:0.9rem;margin:0 0 1rem">Mulai tambahkan template dokumen pertama Anda</p>
        <a href="{{ route('admin.template.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Template Pertama
        </a>
    </div>
    @endforelse
    
    {{-- Pagination for Mobile --}}
    @if($templates->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem">
        <div style="color:var(--text-muted);font-size:0.85rem">
            {{ $templates->firstItem() }}-{{ $templates->lastItem() }} dari {{ $templates->total() }}
        </div>
        {!! $templates->withQueryString()->links() !!}
    </div>
    @endif
</div>

<script>
// ==========================================
// DELETE CONFIRMATION DENGAN TOAST
// ==========================================
async function confirmDeleteTemplate(id, name) {
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Template <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                {
                    title: 'Hapus Template?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
            
            if (confirmed) {
                document.getElementById('delete-template-' + id).submit();
            }
        } else {
            // Fallback
            if (confirm(`Hapus template "${name}"?`)) {
                document.getElementById('delete-template-' + id).submit();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (confirm(`Hapus template "${name}"?`)) {
            document.getElementById('delete-template-' + id).submit();
        }
    }
}
</script>

@endsection