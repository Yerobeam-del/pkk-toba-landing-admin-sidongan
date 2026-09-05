{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Manajemen Desa')
@section('page-title', 'Manajemen Desa')

@section('content')

{{-- Header Section --}}
<div class="desa-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div class="u-flex-1-min">
        <h1 class="u-page-title-tight">Manajemen Desa</h1>
        <p class="u-muted">Kabupaten Toba • Angka penduduk &amp; KK otomatis dari database SIEDA</p>
    </div>
    
    <div class="desa-header-actions" style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
        
        {{-- Custom Select dengan SVG Icon --}}
        <div style="position:relative;width:220px;min-width:200px;display:flex;align-items:center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                style="position:absolute;left:0.85rem;color:var(--text-muted);pointer-events:none;z-index:1">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            
            <select id="filterKecamatan" data-base-url="{{ route('admin.desa.create') }}" class="form-control" style="width:100%;padding:0.6rem 2.5rem 0.6rem 2.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.75rem center;background-size:18px;cursor:pointer">
                <option value="">Filter Kecamatan</option>
            </select>
        </div>
        
        {{-- Tombol Tambah Desa --}}
        <a id="btnTambahDesa" href="{{ route('admin.desa.create') }}" class="btn btn-primary" style="white-space:nowrap;display:inline-flex;align-items:center;gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Desa
        </a>
    </div>
</div>

{{-- Loading State --}}
<div id="loading-state" style="text-align:center;padding:3rem;color:var(--text-muted)">
    <div style="width:48px;height:48px;border:4px solid rgba(20,184,166,0.1);border-top-color:var(--primary);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 1rem"></div>
    <div style="font-size:1rem;font-weight:600;margin-bottom:0.5rem">Memuat data...</div>
    <div style="font-size:0.85rem">Mengambil data desa per kecamatan</div>
</div>

{{-- Error State --}}
<div id="error-state" style="display:none;text-align:center;padding:3rem">
    <div style="width:64px;height:64px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <h3 style="color:var(--danger);margin-bottom:0.5rem;font-size:1.1rem">Gagal Memuat Data</h3>
    <p id="error-message" style="color:var(--text-muted);margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto"></p>
    <button data-action="reload-page" class="btn btn-primary u-inline-flex-center-gap-2">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        Refresh Halaman
    </button>
</div>

{{-- Empty State --}}
<div id="empty-state" style="display:none;text-align:center;padding:3rem">
    <div class="u-a55">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    </div>
    <h3 style="margin-bottom:0.5rem;font-size:1.1rem;font-weight:700;color:var(--text-dark)">Belum Ada Data Desa</h3>
    <p style="color:var(--text-muted);margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto;font-size:0.9rem">
        Belum ada desa yang diinput. Silakan tambah desa pertama Anda untuk mulai mengelola data.
    </p>
    <a href="{{ route('admin.desa.create') }}" class="btn btn-primary u-inline-flex-center-gap-2">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Desa Pertama
    </a>
</div>

{{-- Content State --}}
<div class="u-hidden" id="content-state">
    <div id="no-data-message" style="display:none;text-align:center;padding:2rem;background:#f8fafc;border-radius:10px;margin-bottom:1.5rem;color:var(--text-muted)">
        <p>Tidak ada kecamatan dengan data desa. Silakan tambah desa untuk memulai.</p>
    </div>
    
    <div class="card u-a11">
        <div id="accordion-container"></div>
    </div>
</div>

    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-desa-index.css') }}">


@push('scripts')
    <script src="{{ asset('assets/admin/js/desa-index.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
