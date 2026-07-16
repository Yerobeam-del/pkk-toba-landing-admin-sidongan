@extends('admin.layouts.app')
@section('title', 'SK & Dokumen')
@section('page-title', 'SK & Dokumen')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .sk-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .sk-header h1 {
        font-size: 1.25rem !important;
    }
    
    .sk-header .btn {
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
    
    .mobile-card-view .doc-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: #fff;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    
    .mobile-card-view .doc-card-header {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .mobile-card-view .doc-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(139,92,246,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .mobile-card-view .doc-card-info {
        flex: 1;
        min-width: 0;
    }
    
    .mobile-card-view .doc-card-name {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.15rem;
        font-size: 0.95rem;
    }
    
    .mobile-card-view .doc-card-filename {
        font-size: 0.75rem;
        color: var(--text-muted);
        word-break: break-all;
    }
    
    .mobile-card-view .doc-card-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
    }
    
    .mobile-card-view .doc-card-meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .mobile-card-view .doc-card-meta-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-transform: uppercase;
    }
    
    .mobile-card-view .doc-card-meta-value {
        font-weight: 600;
        color: var(--text-dark);
    }
    
    .mobile-card-view .doc-card-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    
    .mobile-card-view .doc-card-actions a,
    .mobile-card-view .doc-card-actions button {
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
    
    .mobile-card-view .doc-card-actions a:hover {
        background: #eff6ff;
        color: #2563eb;
    }
    
    .mobile-card-view .doc-card-actions button:hover {
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
<div class="sk-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">SK & Dokumen</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola surat keputusan dan dokumen dari pusat</p>
    </div>
    <a href="{{ route('admin.sk.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Dokumen
    </a>
</div>

{{-- Filter Form --}}
<div class="card" style="margin-bottom:1.5rem;padding:1.25rem">
    <form id="filterForm" method="GET" action="{{ route('admin.sk.index') }}" class="filter-form" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:center">
        
        {{-- Search Input with Icon --}}
        <div style="position:relative;flex:1;min-width:250px">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                 style="position:absolute;left:0.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" id="searchInput" name="search" placeholder="Cari nama dokumen..." 
                   value="{{ request('search') }}" 
                   class="form-control" 
                   style="width:100%;padding:0.65rem 1rem 0.65rem 2.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-family:inherit;font-size:0.9rem" 
                   autocomplete="off">
        </div>
        
        {{-- Status Dropdown --}}
        <div style="position:relative;min-width:180px">
            <select id="statusSelect" name="status" class="form-control" 
                    style="width:100%;padding:0.65rem 2.5rem 0.65rem 1rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.75rem center;background-size:18px;cursor:pointer">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status')==='published'?'selected':'' }}>Published</option>
                <option value="draft" {{ request('status')==='draft'?'selected':'' }}>Draft</option>
            </select>
        </div>
        
        {{-- Reset Button --}}
        @if(request('search') || request('status'))
            <a href="{{ route('admin.sk.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                Reset
            </a>
        @endif
        
        {{-- Filter Status Indicator --}}
        <span id="filterStatus" style="font-size:0.85rem;color:var(--text-muted);display:none;margin-left:auto">
            Filter diterapkan
        </span>
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
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Nama Dokumen</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Tanggal</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Ukuran</th>
                        <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Status</th>
                        <th style="padding:0.875rem 1rem;text-align:right;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08);width:140px">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($dokumens as $index => $doc)
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                        <td style="padding:0.875rem 1rem;color:var(--text-muted);font-size:0.9rem">{{ ($dokumens->currentPage()-1)*$dokumens->perPage() + $index+1 }}</td>
                        <td style="padding:0.875rem 1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:10px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <div style="flex:1;min-width:0">
                                    <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.15rem">{{ Str::limit($doc->name, 50) }}</div>
                                    <div style="font-size:0.8rem;color:var(--text-muted)">{{ $doc->file_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:0.875rem 1rem;color:var(--text-muted);font-size:0.9rem">{{ $doc->document_date?->format('d M Y') ?? '-' }}</td>
                        <td style="padding:0.875rem 1rem">
                            <span style="background:#f1f5f9;padding:0.35rem 0.65rem;border-radius:6px;font-size:0.8rem;color:var(--text-muted);font-weight:500">{{ $doc->file_size }}</span>
                        </td>
                        <td style="padding:0.875rem 1rem">
                            @if($doc->status === 'published')
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
                            <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex">
                                <a href="{{ $doc->file_url }}" target="_blank" class="btn-view" title="Preview" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.sk.edit', $doc) }}" class="btn-edit" title="Edit" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <button type="button" onclick="confirmDeleteSk({{ $doc->id }}, '{{ addslashes(Str::limit($doc->name, 40)) }}')" class="btn-del" title="Hapus" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;border:none;cursor:pointer" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                                <form id="delete-sk-{{ $doc->id }}" action="{{ route('admin.sk.destroy', $doc) }}" method="POST" style="display:none">
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
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </div>
                                <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Belum ada dokumen</h3>
                                <p style="font-size:0.9rem;margin:0 0 1rem">Mulai tambahkan dokumen pertama Anda</p>
                                <a href="{{ route('admin.sk.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Tambah Dokumen Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($dokumens->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem">
            <div style="color:var(--text-muted);font-size:0.9rem">
                Menampilkan {{ $dokumens->firstItem() }}-{{ $dokumens->lastItem() }} dari {{ $dokumens->total() }} dokumen
            </div>
            {!! $dokumens->withQueryString()->links() !!}
        </div>
        @endif
    </div>
</div>

{{-- Mobile Card View --}}
<div class="mobile-card-view" style="padding:0">
    @forelse($dokumens as $index => $doc)
    <div class="doc-card">
        <div class="doc-card-header">
            <div class="doc-card-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div class="doc-card-info">
                <div class="doc-card-name">{{ Str::limit($doc->name, 50) }}</div>
                <div class="doc-card-filename">{{ $doc->file_name }}</div>
            </div>
        </div>
        <div class="doc-card-meta">
            <div class="doc-card-meta-item">
                <span class="doc-card-meta-label">Tanggal</span>
                <span class="doc-card-meta-value">{{ $doc->document_date?->format('d M Y') ?? '-' }}</span>
            </div>
            <div class="doc-card-meta-item">
                <span class="doc-card-meta-label">Ukuran</span>
                <span class="doc-card-meta-value">{{ $doc->file_size }}</span>
            </div>
            <div class="doc-card-meta-item">
                <span class="doc-card-meta-label">Status</span>
                <span class="doc-card-meta-value">
                    @if($doc->status === 'published')
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
            <div class="doc-card-meta-item">
                <span class="doc-card-meta-label">No. Urut</span>
                <span class="doc-card-meta-value">{{ ($dokumens->currentPage()-1)*$dokumens->perPage() + $index+1 }}</span>
            </div>
        </div>
        <div class="doc-card-actions">
            <a href="{{ $doc->file_url }}" target="_blank" title="Preview" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </a>
            <a href="{{ route('admin.sk.edit', $doc) }}" title="Edit" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </a>
            <button type="button" onclick="confirmDeleteSk({{ $doc->id }}, '{{ addslashes(Str::limit($doc->name, 40)) }}')" title="Hapus" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
            </button>
            <form id="delete-sk-{{ $doc->id }}" action="{{ route('admin.sk.destroy', $doc) }}" method="POST" style="display:none">
                @csrf 
                @method('DELETE')
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
        <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
        </div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Belum ada dokumen</h3>
        <p style="font-size:0.9rem;margin:0 0 1rem">Mulai tambahkan dokumen pertama Anda</p>
        <a href="{{ route('admin.sk.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Dokumen Pertama
        </a>
    </div>
    @endforelse
    
    {{-- Pagination for Mobile --}}
    @if($dokumens->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:0.5rem">
        <div style="color:var(--text-muted);font-size:0.85rem">
            {{ $dokumens->firstItem() }}-{{ $dokumens->lastItem() }} dari {{ $dokumens->total() }}
        </div>
        {!! $dokumens->withQueryString()->links() !!}
    </div>
    @endif
</div>

<script>
// ==========================================
// FILTER FUNCTIONALITY
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const searchInput = document.getElementById('searchInput');
    const statusSelect = document.getElementById('statusSelect');
    const filterStatus = document.getElementById('filterStatus');
    
    let debounceTimer;
    
    function submitFilter() {
        filterStatus.style.display = 'inline';
        setTimeout(() => { filterStatus.style.display = 'none'; }, 1500);
        form.submit();
    }
    
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(submitFilter, 600);
    });
    
    statusSelect.addEventListener('change', submitFilter);
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitFilter();
        }
    });
});

// ==========================================
// DELETE CONFIRMATION DENGAN TOAST
// ==========================================
async function confirmDeleteSk(id, name) {
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Dokumen <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                {
                    title: 'Hapus Dokumen?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
            
            if (confirmed) {
                document.getElementById('delete-sk-' + id).submit();
            }
        } else {
            // Fallback
            if (confirm(`Hapus dokumen "${name}"?`)) {
                document.getElementById('delete-sk-' + id).submit();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (confirm(`Hapus dokumen "${name}"?`)) {
            document.getElementById('delete-sk-' + id).submit();
        }
    }
}
</script>

@endsection