@extends('admin.layouts.app')
@section('title', 'Manajemen Data SIDONGAN')
@section('page-title', 'Manajemen Data SIDONGAN')

@section('content')
<style>
@media (max-width: 768px) {
    .sidongan-header { flex-direction: column !important; align-items: flex-start !important; gap: 1rem !important; }
    .sidongan-header h1 { font-size: 1.25rem !important; }
    .sidongan-header .btn { width: 100% !important; justify-content: center !important; }
    .stats-grid { grid-template-columns: 1fr !important; }
    .tabs-container { overflow-x: auto !important; -webkit-overflow-scrolling: touch; }
    .tabs-container::-webkit-scrollbar { height: 4px; }
    .tabs-container::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
    .tab-btn { white-space: nowrap !important; flex-shrink: 0 !important; }

    /* Collapse filter grid ke 1 kolom di mobile */
    .filter-form > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }

    .filter-form { flex-direction: column !important; }
    .filter-form > div, .filter-form select, .filter-form input { width: 100% !important; min-width: 100% !important; }

    /* Button container di filter full width */
    .filter-form button, .filter-form a[style*="padding"] {
        width: 100% !important;
        justify-content: center !important;
    }

    /* Cleanup grid collapse */
    .cleanup-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

{{-- Header Section --}}
<div class="sidongan-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Manajemen Data SIDONGAN</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola dan bersihkan data surat dari sistem SIDONGAN</p>
    </div>
    <a href="{{ route('sidongan.dashboard') }}" target="_blank" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Buka SIDONGAN
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem">
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Total Surat</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#eab308,#ca8a04);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Menunggu Disposisi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['menunggu_disposisi'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#f97316,#ea580c);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Berjalan</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['berjalan'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Menunggu Verifikasi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['menunggu_verifikasi'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Selesai</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['selesai'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#a855f7,#9333ea);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Diarsipkan</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['diarsipkan'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:linear-gradient(135deg,#ec4899,#db2777);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Storage</p>
                <p style="font-size:1.5rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['storage_used'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Tambahan --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    <div class="stat-card" style="background:#fff;border:1px solid rgba(0,0,0,0.06);padding:1.25rem">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(59,130,246,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.25rem 0">Total Laporan Kegiatan</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1;color:var(--text-dark)">{{ $stats['total_laporan'] }}</p>
            </div>
        </div>
    </div>
    <div class="stat-card" style="background:#fff;border:1px solid rgba(0,0,0,0.06);padding:1.25rem">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(168,85,247,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.25rem 0">Total Notifikasi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1;color:var(--text-dark)">{{ $stats['total_notifikasi'] }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Filter Section --}}
<div class="card" style="padding:1.25rem;margin-bottom:1.5rem">
    <form method="GET" action="{{ route('admin.sidongan-data.index') }}" class="filter-form">
        <input type="hidden" name="tab" value="{{ $currentTab }}">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, nomor, pengirim..." style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(20,184,166,0.1)'" onblur="this.style.borderColor='rgba(0,0,0,0.08)';this.style.boxShadow='none'">
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Kategori</label>
                <select name="category_id" style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem;background:#fff;cursor:pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;align-items:end;gap:0.5rem">
                <button type="submit" class="btn btn-primary" style="flex:1;padding:0.625rem 1rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:0.5rem"><polyline points="20 6 9 17 4 12"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.sidongan-data.index') }}" style="padding:0.625rem 1rem;background:#f1f5f9;color:var(--text-dark);border:none;border-radius:8px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                </a>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:1rem;align-items:end">
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem">
            </div>
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem">
            </div>
            <div style="display:flex;align-items:center;gap:1rem">
                <form method="GET" action="{{ route('admin.sidongan-data.index') }}" style="display:flex;align-items:center;gap:0.5rem">
                    <input type="hidden" name="tab" value="{{ $currentTab }}">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                    <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ request('date_to') }}">
                    <label style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;font-weight:500">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()" style="padding:0.5rem 2rem 0.5rem 0.75rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;cursor:pointer;background:white">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
            </div>
        </div>
    </form>
</div>

{{-- Cleanup Section --}}
<div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem 0;display:flex;align-items:center;gap:0.5rem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f97316" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        Pembersihan Data
    </h3>
    <p style="color:var(--text-muted);margin:0 0 1.5rem 0;font-size:0.9rem">Hapus data yang tidak diperlukan untuk mengoptimalkan sistem</p>
    <div class="cleanup-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem">
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Surat Arsip" data-message="Semua surat yang sudah diarsipkan akan dihapus permanen beserta file-nya. Tindakan ini tidak dapat dibatalkan!">
            @csrf
            <input type="hidden" name="action" value="delete_archived">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#a855f7,#9333ea);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(168,85,247,0.3)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Arsip</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['diarsipkan'] }} surat</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Surat Selesai" data-message="Semua surat berstatus 'selesai' akan dihapus permanen beserta file-nya. Tindakan ini tidak dapat dibatalkan!">
            @csrf
            <input type="hidden" name="action" value="delete_completed">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(34,197,94,0.3)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Selesai</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['selesai'] }} surat</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Laporan Kegiatan" data-message="Semua laporan kegiatan akan dihapus permanen beserta file foto-nya. Tindakan ini tidak dapat dibatalkan!">
            @csrf
            <input type="hidden" name="action" value="delete_all_reports">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(59,130,246,0.3)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Laporan</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['total_laporan'] }} laporan</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Notifikasi" data-message="Semua notifikasi akan dihapus permanen. User akan kehilangan riwayat notifikasi mereka.">
            @csrf
            <input type="hidden" name="action" value="delete_all_notifications">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#ec4899,#db2777);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(236,72,153,0.3)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Notifikasi</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['total_notifikasi'] }} notifikasi</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" id="deleteOldForm" data-title="Hapus Data Lama" data-message="Surat selesai/arsip yang lebih lama dari jumlah hari yang ditentukan akan dihapus permanen.">
            @csrf
            <input type="hidden" name="action" value="delete_old">
            <input type="hidden" name="confirm" value="1">
            <div style="padding:1rem;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;border-radius:10px">
                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Data Lama</div>
                        <div style="font-size:0.85rem;opacity:0.9">Berdasarkan umur data</div>
                    </div>
                </div>
                <div style="display:flex;gap:0.5rem">
                    <input type="number" name="days" value="365" min="1" placeholder="Hari" style="flex:1;padding:0.5rem;border:none;border-radius:6px;font-size:0.875rem">
                    <button type="submit" style="padding:0.5rem 1rem;background:rgba(255,255,255,0.2);color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;transition:all 0.2s" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABS --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;gap:1.5rem;flex-wrap:wrap">
    <div class="tabs-container" style="flex:1;min-width:0;display:flex;align-items:flex-end;gap:0.25rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem;overflow-x:auto">
        @php
            $tabs = [
                'all' => ['label' => 'Semua Surat', 'count' => $stats['total']],
                'menunggu_disposisi' => ['label' => 'Menunggu Disposisi', 'count' => $stats['menunggu_disposisi']],
                'berjalan' => ['label' => 'Berjalan', 'count' => $stats['berjalan']],
                'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'count' => $stats['menunggu_verifikasi']],
                'selesai' => ['label' => 'Selesai', 'count' => $stats['selesai']],
                'diarsipkan' => ['label' => 'Diarsipkan', 'count' => $stats['diarsipkan']],
            ];
        @endphp
        @foreach($tabs as $key => $tabData)
            @php
                $isActive = $currentTab === $key;
                $url = request()->fullUrlWithQuery([
                    'tab' => $key,
                    'page_all' => 1, 'page_menunggu_disposisi' => 1, 'page_berjalan' => 1,
                    'page_menunggu_verifikasi' => 1, 'page_selesai' => 1, 'page_diarsipkan' => 1,
                ]);
            @endphp
            <a href="{{ $url }}" class="tab-btn {{ $isActive ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:8px;text-decoration:none;color:{{ $isActive ? 'var(--primary)' : 'var(--text-muted)' }};background:{{ $isActive ? 'rgba(13, 148, 136, 0.1)' : 'transparent' }};border:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;border-bottom:2px solid {{ $isActive ? 'var(--primary)' : 'transparent' }}" onmouseover="if(!this.classList.contains('active')){this.style.background='rgba(13, 148, 136, 0.05)';this.style.color='var(--primary)'}" onmouseout="if(!this.classList.contains('active')){this.style.background='transparent';this.style.color='var(--text-muted)'}">
                {{ $tabData['label'] }}
                @if($tabData['count'] > 0)
                    <span style="background:rgba(0,0,0,0.05);color:var(--text-muted);padding:2px 8px;border-radius:12px;font-size:0.75rem">{{ $tabData['count'] }}</span>
                @endif
            </a>
        @endforeach
    </div>
</div>

{{-- Main Card with Tabs --}}
<div class="card" style="padding:0;overflow:hidden;border:1px solid rgba(0,0,0,0.06);border-radius:12px">
    @php
        $sidonganColumns = [
            [
                'key' => 'agenda_number',
                'label' => 'No. Agenda',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '<span style="font-family:monospace;font-size:0.875rem;font-weight:600;color:#3b82f6">' . $value . '</span>';
                }
            ],
            [
                'key' => 'subject',
                'label' => 'Perihal',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return '<div style="font-weight:600;color:var(--text-dark);margin-bottom:0.25rem">' . \Str::limit($value, 50) . '</div><div style="font-size:0.85rem;color:var(--text-muted)">' . $item->document_number . '</div>';
                }
            ],
            [
                'key' => 'sender',
                'label' => 'Pengirim',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
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
                'key' => 'status',
                'label' => 'Status',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    $statusConfig = [
                        'menunggu_disposisi' => ['bg' => 'rgba(234,179,8,0.1)', 'text' => '#92400e', 'label' => 'Menunggu Disposisi'],
                        'berjalan' => ['bg' => 'rgba(249,115,22,0.1)', 'text' => '#c2410c', 'label' => 'Berjalan'],
                        'menunggu_verifikasi' => ['bg' => 'rgba(99,102,241,0.1)', 'text' => '#4338ca', 'label' => 'Menunggu Verifikasi'],
                        'selesai' => ['bg' => 'rgba(34,197,94,0.1)', 'text' => '#166534', 'label' => 'Selesai'],
                        'diarsipkan' => ['bg' => 'rgba(168,85,247,0.1)', 'text' => '#6b21a8', 'label' => 'Diarsipkan'],
                    ];
                    $config = $statusConfig[$value] ?? ['bg' => 'rgba(100,116,139,0.1)', 'text' => '#475569', 'label' => $value];
                    return '<span style="display:inline-block;padding:0.375rem 0.75rem;background:' . $config['bg'] . ';color:' . $config['text'] . ';border-radius:20px;font-size:0.75rem;font-weight:600">' . $config['label'] . '</span>';
                }
            ],
            [
                'key' => 'creator.name',
                'label' => 'Pembuat',
                'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                'type' => 'callback',
                'callback' => function($item, $value) {
                    return $value ?: '-';
                }
            ],
        ];
    @endphp

    {{-- Tab: Semua Surat --}}
    <div id="tab-all" class="tab-content" style="display: {{ $currentTab === 'all' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $allDocs,
            'columns' => $sidonganColumns,
            'emptyMessage' => 'Tidak ada data surat. Silakan gunakan filter untuk mencari data.',
            'editRoute' => 'admin.sidongan-data.show',
            'deleteRoute' => 'admin.sidongan-data.destroy',
            'actions' => ['show', 'delete'],
            'emptyIcon' => 'database',
            'showRoute' => 'admin.sidongan-data.show',
        ])
    </div>

    {{-- Tab: Menunggu Disposisi --}}
    <div id="tab-menunggu_disposisi" class="tab-content" style="display: {{ $currentTab === 'menunggu_disposisi' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $menungguDisposisiDocs,
            'columns' => collect($sidonganColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada surat yang menunggu disposisi.',
            'editRoute' => 'admin.sidongan-data.show',
            'deleteRoute' => 'admin.sidongan-data.destroy',
            'actions' => ['show', 'delete'],
            'emptyIcon' => 'database',
            'showRoute' => 'admin.sidongan-data.show',
        ])
    </div>

    {{-- Tab: Berjalan --}}
    <div id="tab-berjalan" class="tab-content" style="display: {{ $currentTab === 'berjalan' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $berjalanDocs,
            'columns' => collect($sidonganColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada surat yang sedang berjalan.',
            'editRoute' => 'admin.sidongan-data.show',
            'deleteRoute' => 'admin.sidongan-data.destroy',
            'actions' => ['show', 'delete'],
            'emptyIcon' => 'database',
            'showRoute' => 'admin.sidongan-data.show',
        ])
    </div>

    {{-- Tab: Menunggu Verifikasi --}}
    <div id="tab-menunggu_verifikasi" class="tab-content" style="display: {{ $currentTab === 'menunggu_verifikasi' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $menungguVerifikasiDocs,
            'columns' => collect($sidonganColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada surat yang menunggu verifikasi.',
            'editRoute' => 'admin.sidongan-data.show',
            'deleteRoute' => 'admin.sidongan-data.destroy',
            'actions' => ['show', 'delete'],
            'emptyIcon' => 'database',
            'showRoute' => 'admin.sidongan-data.show',
        ])
    </div>

    {{-- Tab: Selesai --}}
    <div id="tab-selesai" class="tab-content" style="display: {{ $currentTab === 'selesai' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $selesaiDocs,
            'columns' => collect($sidonganColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada surat yang selesai.',
            'editRoute' => 'admin.sidongan-data.show',
            'deleteRoute' => 'admin.sidongan-data.destroy',
            'actions' => ['show', 'delete'],
            'emptyIcon' => 'database',
            'showRoute' => 'admin.sidongan-data.show',
        ])
    </div>

    {{-- Tab: Diarsipkan --}}
    <div id="tab-diarsipkan" class="tab-content" style="display: {{ $currentTab === 'diarsipkan' ? 'block' : 'none' }}">
        @include('admin.partials.table', [
            'data' => $diarsipkanDocs,
            'columns' => collect($sidonganColumns)->reject(fn($col) => $col['key'] === 'status')->values()->all(),
            'emptyMessage' => 'Tidak ada surat yang diarsipkan.',
            'editRoute' => 'admin.sidongan-data.show',
            'deleteRoute' => 'admin.sidongan-data.destroy',
            'actions' => ['show', 'delete'],
            'emptyIcon' => 'database',
            'showRoute' => 'admin.sidongan-data.show',
        ])
    </div>
</div>

{{-- Hidden Form untuk Delete --}}
<form id="deleteForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

<script>
// Konfirmasi Cleanup
document.querySelectorAll('.cleanup-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const title = this.dataset.title;
        const message = this.dataset.message;
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            Toast.confirm(message, {
                title: title,
                confirmText: 'Ya, Hapus Sekarang',
                cancelText: 'Batal',
                type: 'danger'
            }).then((confirmed) => {
                if (confirmed) this.submit();
            });
        } else {
            if (confirm(message)) this.submit();
        }
    });
});

// Konfirmasi Delete Single
function confirmDeleteItem(id, name) {
    const message = `Apakah Anda yakin ingin menghapus surat "<strong>${name}</strong>" secara permanen?<br><small style="color:#64748b">File, laporan, dan notifikasi terkait juga akan dihapus.</small>`;
    if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
        Toast.confirm(message, {
            title: 'Hapus Permanen',
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger'
        }).then((confirmed) => {
            if (confirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/admin/sidongan-data/${id}`;
                form.submit();
            }
        });
    } else {
        if (confirm(`Hapus surat "${name}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/sidongan-data/${id}`;
            form.submit();
        }
    }
}
</script>
@endsection
