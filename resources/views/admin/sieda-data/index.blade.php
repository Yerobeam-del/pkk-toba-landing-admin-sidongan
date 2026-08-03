@extends('admin.layouts.app')
@section('title', 'Manajemen Data SIEDA')
@section('page-title', 'Manajemen Data SIEDA')

@section('content')
<style>
    .sieda-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .sieda-header h1 { font-size: 1.5rem; font-weight: 800; color: var(--text-dark); margin: 0 0 0.25rem 0; }
    .sieda-header p { color: var(--text-muted); margin: 0; font-size: 0.9rem; }
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card { background: #fff; border: 1px solid rgba(0,0,0,0.06); border-radius: 12px; padding: 1.25rem; }
    .btn-delete-perm { background: linear-gradient(135deg,#ef4444,#b91c1c); color: #fff; border: none; }
    .status-aktif { background: #d1fae5; color: #065f46; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .status-terhapus { background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th { background: #f8fafc; text-align: left; padding: 0.75rem; font-weight: 600; color: #334155; border-bottom: 2px solid #e2e8f0; }
    td { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; }
    tr:hover td { background: #fafbfc; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; border-radius: 6px; }
    .card { border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .badge-warning-lg { background: #fef3c7; color: #92400e; border-left: 3px solid #f59e0b; }
</style>

{{-- Header --}}
<div class="sieda-header">
    <div>
        <h1>Manajemen Data SIEDA</h1>
        <p>Kelola data aplikasi SIEDA — lihat, restore, atau hapus permanen</p>
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
<div class="stat-grid" style="margin-bottom: 2rem">
    <div class="stat-card" style="background: linear-gradient(135deg,#3b82f6,#2563eb); color:white; padding:1.25rem">
        <p style="margin:0 0 0.25rem 0; opacity:0.9; font-size:0.85rem">Total Record Keseluruhan</p>
        <p style="margin:0; font-size:2rem; font-weight:800">{{ number_format($totalKeseluruhan) }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg,#22c55e,#16a34a); color:white; padding:1.25rem">
        <p style="margin:0 0 0.25rem 0; opacity:0.9; font-size:0.85rem">Data Aktif di SIEDA</p>
        <p style="margin:0; font-size:2rem; font-weight:800">{{ number_format($totalKeseluruhan - $totalTerhapus) }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg,#f59e0b,#d97706); color:white; padding:1.25rem">
        <p style="margin:0 0 0.25rem 0; opacity:0.9; font-size:0.85rem">Data Terhapus (Recycle Bin)</p>
        <p style="margin:0; font-size:2rem; font-weight:800">{{ number_format($totalTerhapus) }}</p>
    </div>
</div>

{{-- Daftar Modul --}}
<div class="card" style="padding: 1.5rem">
    <h2 style="margin:0 0 1rem 0; font-size:1.1rem; font-weight:700; color:var(--text-dark)">Pilih Modul untuk Dikelola</h2>

    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Modul</th>
                <th style="text-align:center">Total Data</th>
                <th style="text-align:center">Aktif</th>
                <th style="text-align:center">Recycle Bin</th>
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
                    <td style="text-align:center">
                        <span class="status-aktif">{{ number_format($stat['aktif']) }}</span>
                    </td>
                    <td style="text-align:center">
                        <span class="status-terhapus">{{ number_format($stat['terhapus']) }}</span>
                    </td>
                    <td style="text-align:right">
                        <a href="{{ route('admin.sieda-data.module', $stat['slug']) }}?status=aktif"
                           class="btn btn-sm btn-outline-primary">
                            Kelola Data Aktif
                        </a>
                        @if ($stat['terhapus'] > 0)
                            <a href="{{ route('admin.sieda-data.module', $stat['slug']) }}?status=terhapus"
                               class="btn btn-sm btn-outline-warning ms-2">
                                Lihat Recycle Bin ({{ number_format($stat['terhapus']) }})
                            </a>
                        @else
                            <span class="text-muted ms-2" style="font-size:0.85rem">Kosong</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Quick Guide --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:1rem; margin-top:2rem">
    <div class="card" style="padding:1.25rem; border-left:4px solid #3b82f6">
        <h3 style="margin:0 0 0.5rem 0; color:#1e40af; font-size:0.95rem">👁️ Lihat Data Aktif</h3>
        <p style="margin:0; color:#64748b; font-size:0.85rem">Tampilkan data yang masih aktif di SIEDA (belum dihapus).</p>
    </div>
    <div class="card" style="padding:1.25rem; border-left:4px solid #f59e0b">
        <h3 style="margin:0 0 0.5rem 0; color:#92400e; font-size:0.95rem">🗑️ Recycle Bin</h3>
        <p style="margin:0; color:#64748b; font-size:0.85rem">Data yang sudah dihapus di SIEDA (soft-delete). Bisa Restore atau Hapus Permanen.</p>
    </div>
    <div class="card" style="padding:1.25rem; border-left:4px solid #ef4444">
        <h3 style="margin:0 0 0.5rem 0; color:#b91c1c; font-size:0.95rem">⚠️ Hapus Permanen</h3>
        <p style="margin:0; color:#64748b; font-size:0.85rem">Hanya di Recycle Bin. Tidak bisa dikembalikan setelah dihapus.</p>
    </div>
    <div class="card" style="padding:1.25rem; border-left:4px solid #22c55e">
        <h3 style="margin:0 0 0.5rem 0; color:#166534; font-size:0.95rem">↩️ Restore</h3>
        <p style="margin:0; color:#64748b; font-size:0.85rem">Pulihkan data dari Recycle Bin ke kondisi aktif kembali.</p>
    </div>
</div>

@endsection
