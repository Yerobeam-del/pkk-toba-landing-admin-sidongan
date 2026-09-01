{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Koneksi Database SIEDA Gagal')
@section('page-title', 'Koneksi Database SIEDA Gagal')

@section('content')
<div class="sieda-header">
    <div>
        <a href="{{ route('admin.sieda-data.index') }}" class="btn btn-outline-secondary btn-sm" style="margin-bottom:0.5rem;display:inline-flex;align-items:center;gap:0.4rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali
        </a>
        <h1 style="margin:0">Manajemen Data SIEDA</h1>
    </div>
</div>

<div class="card" style="padding:2.5rem;text-align:center">
    <div style="width:64px;height:64px;background:rgba(239,68,68,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
    </div>

    <h2 style="margin:0 0 0.5rem 0;font-size:1.25rem;font-weight:700;color:var(--text-dark)">Database SIEDA Tidak Dapat Dihubungi</h2>
    <p style="margin:0 0 1.5rem 0;color:var(--text-muted);font-size:0.95rem;max-width:500px;margin-left:auto;margin-right:auto;line-height:1.6">
        Server database SIEDA sedang tidak tersedia atau koneksi terputus.
        Pastikan server database SIEDA sedang aktif dan dapat diakses dari server ini.
    </p>

    @if($message ?? null)
        <div style="background:#fef2f2;border:1px solid rgba(239,68,68,0.2);border-radius:8px;padding:1rem;margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto;text-align:left">
            <p style="margin:0;font-size:0.85rem;color:#991b1b;font-family:monospace;word-break:break-all">{{ $message }}</p>
        </div>
    @endif

    <div style="display:flex;gap:0.75rem;justify-content:center">
        <a href="{{ route('admin.sieda-data.index') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Coba Lagi
        </a>
    </div>
</div>
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
