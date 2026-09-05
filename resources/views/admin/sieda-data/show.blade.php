{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Detail — ' . $config['label'])
@section('page-title', 'Detail — ' . $config['label'])

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-sieda-data-show.css') }}">


{{-- Header --}}
<div class="sieda-header">
    <div>
        <a href="{{ route('admin.sieda-data.module', $module) }}" class="btn btn-outline-secondary btn-sm" style="margin-bottom:0.5rem;display:inline-flex;align-items:center;gap:0.4rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kelola {{ $config['label'] }}
        </a>
        <h1 style="margin:0">{{ $config['label'] }}</h1>
        <p style="color:#64748b; margin:0; font-size:0.9rem">Detail record #{{ $item->{$config['id_field']} }} dari aplikasi SIEDA</p>
    </div>
    <div>
        @if ($item->active)
            <span class="u-badge-green">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Aktif
            </span>
        @else
            <span class="u-a67">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Terhapus
            </span>
        @endif
    </div>
</div>

{{-- Card status --}}
<div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem">

        <div>
            <p style="margin:0 0 0.25rem 0; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted)">ID Record</p>
            <p class="u-text-dark" style="margin:0; font-size:1.25rem; font-weight:800; font-family:monospace;">
                {{ $item->{$config['id_field']} }}
            </p>
            <p style="margin:0.25rem 0 0 0; font-size:0.8rem; color:var(--text-muted)">
                Terakhir diubah: {{ $item->updated_at ?? '-' }}
            </p>
        </div>

        <div style="display:flex; gap:0.5rem; align-items:center">
            <form method="POST" action="{{ route('admin.sieda-data.force-delete', [$module, $item->{$config['id_field']}]) }}" id="forceDeleteForm">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" aria-label="Hapus permanen data ini" style="background:linear-gradient(135deg,#ef4444,#b91c1c); color:white; border:none; border-radius:8px; padding:0.5rem 0.875rem; display:inline-flex; align-items:center; gap:0.5rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                    </svg>
                    Hapus Permanen
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Detail data --}}
<div class="card" style="padding: 1.5rem">
    <h2 style="margin:0 0 0 0; font-size:1.1rem; font-weight:700; color:#334155">Isi Data Lengkap</h2>

    <table class="detail-table">
        <tbody>
            @forelse ($item->getAttributes() as $field => $value)
                @continue(in_array($field, ['created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by']))
                <tr>
                    <th>{{ str_replace('_', ' ', Str::title($field)) }}</th>
                    <td>
                        @if (is_null($value) || $value === '' || $value === 'null')
                            <span class="data-nilai-null">—</span>
                        @elseif (is_bool($value))
                            {{ $value ? 'Ya' : 'Tidak' }}
                        @elseif (in_array($field, ['created_at', 'updated_at', 'tanggal_lahir', 'tanggal_melahirkan', 'tanggal_meninggal', 'tanggal_nifas_selesai', 'tanggal_hamil']) && !empty($value))
                            {{ \Carbon\Carbon::parse($value)->format('d F Y, H:i') }}
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td style="color:var(--text-muted); font-style:italic">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
