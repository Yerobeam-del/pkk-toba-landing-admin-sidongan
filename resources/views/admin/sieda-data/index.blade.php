@extends('admin.layouts.app')
@section('title', 'Manajemen Data SIEDA')
@section('page-title', 'Manajemen Data SIEDA')

@section('content')
<style>
    .sieda-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .sieda-header h1 { font-size: 1.5rem; font-weight: 800; color: var(--text-dark); margin: 0 0 0.25rem 0; letter-spacing: -0.5px; }
    .sieda-header p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th { background: #f8fafc; text-align: left; padding: 0.75rem; font-weight: 600; color: #334155; border-bottom: 2px solid #e2e8f0; }
    td { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; }
    tr:hover td { background: #fafbfc; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px; }
    .card { border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
</style>

{{-- Header --}}
<div class="sieda-header">
    <div>
        <h1>Manajemen Data SIEDA</h1>
        <p>Kelola data aplikasi SIEDA — lihat atau hapus permanen</p>
    </div>
</div>

{{-- Perhatian --}}
<div class="card" style="border-left:4px solid #f59e0b; padding:1.25rem; margin-bottom:1.5rem; background:#fffbeb; border-radius:8px">
    <div style="display:flex; align-items:flex-start; gap:0.75rem">
        <svg style="flex-shrink:0; color:#f59e0b; margin-top:2px" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
        <div>
            <strong style="color:#92400e">Akses Super Admin Saja</strong>
            <p style="margin:0.25rem 0 0 0; color:#78350f; font-size:0.9rem">
                Fitur ini melakukan <strong>HARD DELETE permanen</strong> terhadap data SIEDA.
                Berbeda dengan soft-delete di SIEDA yang hanya menandai <code>active=0</code>,
                operasi di sini menghapus record dari database secara permanen dan <strong>tidak bisa dikembalikan</strong>.
            </p>
        </div>
    </div>
</div>

{{-- Statistik Ringkas --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem; margin-bottom:2rem">
    <div class="card" style="background:linear-gradient(135deg,#3b82f6,#2563eb); color:#fff; padding:1.25rem; border:none">
        <div style="display:flex; align-items:flex-start; gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                </svg>
            </div>
            <div style="flex:1">
                <p style="margin:0 0 0.25rem 0; opacity:0.9; font-size:0.85rem">Total Record Keseluruhan</p>
                <p style="margin:0; font-size:1.85rem; font-weight:800; line-height:1.1">{{ number_format($totalKeseluruhan) }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="background:linear-gradient(135deg,#22c55e,#16a34a); color:#fff; padding:1.25rem; border:none">
        <div style="display:flex; align-items:flex-start; gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div style="flex:1">
                <p style="margin:0 0 0.25rem 0; opacity:0.9; font-size:0.85rem">Data Aktif di SIEDA</p>
                <p style="margin:0; font-size:1.85rem; font-weight:800; line-height:1.1">{{ number_format($totalAktif) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Daftar Modul --}}
<div class="card" style="padding: 1.5rem">
    <h2 style="margin:0 0 1rem 0; font-size:1.1rem; font-weight:700; color:var(--text-dark)">Pilih Modul untuk Dikelola</h2>

    <table>
        <thead>
            <tr>
                <th>Modul</th>
                <th style="text-align:center">Total Data</th>
                <th style="text-align:right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($stats as $stat)
                <tr>
                    <td>
                        <strong>{{ $stat['label'] }}</strong>
                    </td>
                    <td style="text-align:center">{{ number_format($stat['total']) }}</td>
                    <td style="text-align:right">
                        <a href="{{ route('admin.sieda-data.module', $stat['slug']) }}"
                           class="btn btn-sm btn-outline-primary"
                           style="display:inline-flex; align-items:center; gap:0.4rem; white-space:nowrap">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            Kelola Data
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Quick Guide --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; margin-top:2rem">
    <div class="card" style="padding:1.25rem; border-left:4px solid #3b82f6">
        <h3 style="margin:0 0 0.5rem 0; color:#1e40af; font-size:0.95rem; display:flex; align-items:center; gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
            </svg>
            Lihat Data
        </h3>
        <p style="margin:0; color:#64748b; font-size:0.85rem">Tampilkan seluruh data SIEDA per modul dengan pencarian dan pagination.</p>
    </div>
    <div class="card" style="padding:1.25rem; border-left:4px solid #ef4444">
        <h3 style="margin:0 0 0.5rem 0; color:#b91c1c; font-size:0.95rem; display:flex; align-items:center; gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
            Hapus Data
        </h3>
        <p style="margin:0; color:#64748b; font-size:0.85rem">Hapus permanen satu record yang tidak diperlukan dari database SIEDA.</p>
    </div>
    <div class="card" style="padding:1.25rem; border-left:4px solid #dc2626; background:#fef2f2">
        <h3 style="margin:0 0 0.5rem 0; color:#991b1b; font-size:0.95rem; display:flex; align-items:center; gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Hapus Semua Data
        </h3>
        <p style="margin:0; color:#7f1d1d; font-size:0.85rem">Kosongkan seluruh data pada satu modul dari database SIEDA. Tidak bisa dikembalikan.</p>
    </div>
</div>

@endsection
