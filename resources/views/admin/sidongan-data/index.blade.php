@extends('admin.layouts.app')
@section('title', 'Manajemen Data SIDONGAN')
@section('page-title', 'Manajemen Data SIDONGAN')

@section('content')

{{-- Header Section --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Manajemen Data SIDONGAN</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola dan bersihkan data surat dari sistem SIDONGAN</p>
    </div>
    <a href="{{ route('sidongan.dashboard') }}" target="_blank" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        Buka SIDONGAN
    </a>
</div>

{{-- Stats Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:2rem">
    {{-- Total Surat --}}
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

    {{-- Menunggu Disposisi --}}
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

    {{-- Berjalan --}}
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

    {{-- Menunggu Verifikasi --}}
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

    {{-- Selesai --}}
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

    {{-- Diarsipkan --}}
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

    {{-- Storage --}}
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
    {{-- Total Laporan Kegiatan --}}
    <div class="stat-card" style="background:#fff;border:1px solid rgba(0,0,0,0.06);padding:1.25rem">
        <div style="display:flex;align-items:center;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(59,130,246,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="9 19 9 19"/><line x1="15" y1="19" x2="15" y2="19"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.25rem 0">Total Laporan Kegiatan</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1;color:var(--text-dark)">{{ $stats['total_laporan'] }}</p>
            </div>
        </div>
    </div>

    {{-- Total Notifikasi --}}
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
    <form method="GET" action="{{ route('admin.sidongan-data.index') }}">
        <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:1rem;margin-bottom:1rem">
            {{-- Search --}}
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Judul, nomor, pengirim..."
                       style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem;transition:all 0.2s"
                       onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(20,184,166,0.1)'"
                       onblur="this.style.borderColor='rgba(0,0,0,0.08)';this.style.boxShadow='none'">
            </div>
            
            {{-- Status --}}
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Status</label>
                <select name="status" style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem;background:#fff;cursor:pointer">
                    <option value="">Semua Status</option>
                    <option value="menunggu_disposisi" {{ request('status') == 'menunggu_disposisi' ? 'selected' : '' }}>Menunggu Disposisi</option>
                    <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                    <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="diarsipkan" {{ request('status') == 'diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                </select>
            </div>
            
            {{-- Kategori --}}
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Kategori</label>
                <select name="category_id" style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem;background:#fff;cursor:pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:1rem;align-items:end">
            {{-- Dari Tanggal --}}
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem">
            </div>
            
            {{-- Sampai Tanggal --}}
            <div>
                <label style="display:block;font-size:0.75rem;font-weight:600;color:var(--text-muted);margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.5px">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       style="width:100%;padding:0.625rem 1rem;border:1px solid rgba(0,0,0,0.08);border-radius:8px;font-size:0.875rem">
            </div>
            
            {{-- Buttons --}}
            <div style="display:flex;gap:0.5rem">
                <button type="submit" class="btn btn-primary" style="padding:0.625rem 1.25rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s"
                        onmouseover="this.style.background='#0d9488';this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.background='var(--primary)';this.style.transform='translateY(0)'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:0.5rem"><polyline points="20 6 9 17 4 12"/></svg>
                    Filter
                </button>
                <a href="{{ route('admin.sidongan-data.index') }}" style="padding:0.625rem 1.25rem;background:#f1f5f9;color:var(--text-dark);border:none;border-radius:8px;font-weight:600;cursor:pointer;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center"
                   onmouseover="this.style.background='#e2e8f0'"
                   onmouseout="this.style.background='#f1f5f9'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline-block;vertical-align:middle;margin-right:0.5rem"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    Reset
                </a>
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
    
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1rem">
        {{-- Hapus Arsip --}}
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Surat Arsip" data-message="Semua surat yang sudah diarsipkan akan dihapus permanen beserta file-nya. Tindakan ini tidak dapat dibatalkan!">
            @csrf
            <input type="hidden" name="action" value="delete_archived">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#a855f7,#9333ea);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left;position:relative"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(168,85,247,0.3)'"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Arsip</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['diarsipkan'] }} surat</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0;margin-right:4px"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>

        {{-- Hapus Selesai --}}
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Surat Selesai" data-message="Semua surat berstatus 'selesai' akan dihapus permanen beserta file-nya. Tindakan ini tidak dapat dibatalkan!">
            @csrf
            <input type="hidden" name="action" value="delete_completed">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left;position:relative"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(34,197,94,0.3)'"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Selesai</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['selesai'] }} surat</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0;margin-right:4px"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>

        {{-- Hapus Laporan --}}
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Laporan Kegiatan" data-message="Semua laporan kegiatan akan dihapus permanen beserta file foto-nya. Tindakan ini tidak dapat dibatalkan!">
            @csrf
            <input type="hidden" name="action" value="delete_all_reports">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left;position:relative"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(59,130,246,0.3)'"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="9 19 9 19"/><line x1="15" y1="19" x2="15" y2="19"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Laporan</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['total_laporan'] }} laporan</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0;margin-right:4px"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>

        {{-- Hapus Notifikasi --}}
        <form method="POST" action="{{ route('admin.sidongan-data.cleanup') }}" class="cleanup-form" data-title="Hapus Semua Notifikasi" data-message="Semua notifikasi akan dihapus permanen. User akan kehilangan riwayat notifikasi mereka.">
            @csrf
            <input type="hidden" name="action" value="delete_all_notifications">
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="width:100%;padding:1rem;background:linear-gradient(135deg,#ec4899,#db2777);color:#fff;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s;text-align:left;position:relative"
                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 16px rgba(236,72,153,0.3)'"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    </div>
                    <div style="flex:1">
                        <div style="font-weight:600;margin-bottom:0.25rem">Hapus Notifikasi</div>
                        <div style="font-size:0.85rem;opacity:0.9">{{ $stats['total_notifikasi'] }} notifikasi</div>
                    </div>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.6;flex-shrink:0;margin-right:4px"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </button>
        </form>

        {{-- Hapus Data Lama --}}
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
                    <input type="number" name="days" value="365" min="1" placeholder="Hari"
                           style="flex:1;padding:0.5rem;border:none;border-radius:6px;font-size:0.875rem">
                    <button type="submit" style="padding:0.5rem 1rem;background:rgba(255,255,255,0.2);color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;transition:all 0.2s"
                            onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                        Hapus
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Daftar Surat --}}
<div class="card" style="padding:0;overflow:hidden">
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(0,0,0,0.06)">
        <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0;display:flex;align-items:center;gap:0.5rem">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Daftar Surat SIDONGAN
            <span style="font-size:0.85rem;font-weight:500;color:var(--text-muted);margin-left:0.5rem">({{ $documents->total() }} data)</span>
        </h3>
    </div>

    @if($documents->count() > 0)
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead style="background:#f8fafc">
                <tr style="text-align:left;border-bottom:1px solid rgba(0,0,0,0.06)">
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">No. Agenda</th>
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Perihal</th>
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Pengirim</th>
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Tanggal</th>
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Status</th>
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Pembuat</th>
                    <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr style="border-bottom:1px solid rgba(0,0,0,0.04);transition:background 0.2s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                    <td style="padding:1rem">
                        <span style="font-family:monospace;font-size:0.875rem;font-weight:600;color:#3b82f6">{{ $doc->agenda_number }}</span>
                    </td>
                    <td style="padding:1rem">
                        <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.25rem">{{ Str::limit($doc->subject, 50) }}</div>
                        <div style="font-size:0.85rem;color:var(--text-muted)">{{ $doc->document_number }}</div>
                    </td>
                    <td style="padding:1rem;color:var(--text-muted);font-size:0.875rem">{{ $doc->sender }}</td>
                    <td style="padding:1rem;color:var(--text-muted);font-size:0.875rem">{{ $doc->document_date ? $doc->document_date->format('d M Y') : '-' }}</td>
                    <td style="padding:1rem">
                        @php
                            $statusConfig = [
                                'menunggu_disposisi' => ['bg' => 'rgba(234,179,8,0.1)', 'text' => '#92400e', 'label' => 'Menunggu Disposisi'],
                                'berjalan' => ['bg' => 'rgba(249,115,22,0.1)', 'text' => '#c2410c', 'label' => 'Berjalan'],
                                'menunggu_verifikasi' => ['bg' => 'rgba(99,102,241,0.1)', 'text' => '#4338ca', 'label' => 'Menunggu Verifikasi'],
                                'selesai' => ['bg' => 'rgba(34,197,94,0.1)', 'text' => '#166534', 'label' => 'Selesai'],
                                'diarsipkan' => ['bg' => 'rgba(168,85,247,0.1)', 'text' => '#6b21a8', 'label' => 'Diarsipkan'],
                            ];
                            $config = $statusConfig[$doc->status] ?? ['bg' => 'rgba(100,116,139,0.1)', 'text' => '#475569', 'label' => $doc->status];
                        @endphp
                        <span style="display:inline-block;padding:0.375rem 0.75rem;background:{{ $config['bg'] }};color:{{ $config['text'] }};border-radius:20px;font-size:0.75rem;font-weight:600">
                            {{ $config['label'] }}
                        </span>
                    </td>
                    <td style="padding:1rem;color:var(--text-muted);font-size:0.875rem">{{ $doc->creator->name ?? '-' }}</td>
                    <td style="padding:1rem;text-align:right">
                        <a href="{{ route('admin.sidongan-data.show', $doc->id) }}"
                        title="Lihat Detail"
                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#3b82f6;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s;text-decoration:none;margin-right:0.25rem"
                        onmouseover="this.style.background='#eff6ff';this.style.transform='scale(1.1)'"
                        onmouseout="this.style.background='transparent';this.style.transform='scale(1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                        <button type="button" onclick="confirmDelete({{ $doc->id }}, '{{ addslashes($doc->subject) }}')"
                                title="Hapus Permanen"
                                style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#ef4444;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                onmouseover="this.style.background='#fef2f2';this.style.transform='scale(1.1)'"
                                onmouseout="this.style.background='transparent';this.style.transform='scale(1)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid rgba(0,0,0,0.06)">
        {{ $documents->links() }}
    </div>
    @endif
    @else
    <div style="padding:3rem 1rem;text-align:center;color:var(--text-muted)">
        <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Tidak ada data surat</h3>
        <p style="font-size:0.9rem;margin:0">Silakan gunakan filter untuk mencari data surat.</p>
    </div>
    @endif
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
                if (confirmed) {
                    this.submit();
                }
            });
        } else {
            if (confirm(message)) {
                this.submit();
            }
        }
    });
});

// Konfirmasi Delete Single
function confirmDelete(id, title) {
    const message = `Apakah Anda yakin ingin menghapus surat "<strong>${title}</strong>" secara permanen?<br><small style="color:#64748b">File, laporan, dan notifikasi terkait juga akan dihapus.</small>`;
    
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
        if (confirm(`Hapus surat "${title}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `/admin/sidongan-data/${id}`;
            form.submit();
        }
    }
}
</script>

@endsection