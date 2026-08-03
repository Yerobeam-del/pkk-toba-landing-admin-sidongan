@extends('admin.layouts.app')
@section('title', 'Detail — ' . $config['label'])
@section('page-title', 'Detail — ' . $config['label'])

@section('content')
<style>
    .sieda-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .badge-aktif { background: #d1fae5; color: #065f46; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .badge-terhapus { background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .detail-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .detail-table tr { border-bottom: 1px solid #e2e8f0; }
    .detail-table tr:last-child { border-bottom: none; }
    .detail-table th { text-align: left; padding: 0.75rem 1rem 0.75rem 0; color: #64748b; font-weight: 600; width: 220px; vertical-align: top; padding-top: 1rem; }
    .detail-table td { padding: 0.75rem 0.5rem; color: #1e293b; vertical-align: top; padding-top: 1rem; }
    .card { border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .section-heading { font-size: 1rem; font-weight: 700; margin: 1.5rem 0 0.75rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid #e2e8f0; color: #334155; }
    .section-heading:first-of-type { margin-top: 0; }
    .data-nilai-null { color: #94a3b8; font-style: italic; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
    .btn-warning-outline { border: 1px solid #f59e0b; color: #92400e; background: transparent; }
    .btn-warning-outline:hover { background: #fef3c7; }
    .btn-success-outline { border: 1px solid #22c55e; color: #166534; background: transparent; }
    .btn-success-outline:hover { background: #f0fdf4; }
</style>

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
        <span class="{{ $item->active ? 'badge-aktif' : 'badge-terhapus' }}">
            {{ $item->active ? '✓ Aktif' : '✗ Terhapus (Recycle Bin)' }}
        </span>
    </div>
</div>

{{-- Card status --}}
<div class="card" style="padding: 1.5rem; margin-bottom: 1.5rem">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem">

        <div>
            <p style="margin:0 0 0.25rem 0; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8">ID Record</p>
            <p style="margin:0; font-size:1.25rem; font-weight:800; font-family:monospace; color:#1e293b">
                {{ $item->{$config['id_field']} }}
            </p>
            <p style="margin:0.25rem 0 0 0; font-size:0.8rem; color:#94a3b8">
                Terakhir diubah: {{ $item->updated_at ?? '-' }}
            </p>
        </div>

        <div style="display:flex; gap:0.5rem; align-items:center">
            @if ($item->active == 0)
                <form method="POST" action="{{ route('admin.sieda-data.restore', [$module, $item->{$config['id_field']}]) }}"
                      onsubmit="return confirm('Pulihkan record ini ke kondisi aktif?')">
                    @csrf
                    <button type="submit" class="btn btn-success-outline btn-sm" style="border-radius:8px; padding:0.5rem 0.875rem">
                        ↩️ Restore
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('admin.sieda-data.force-delete', [$module, $item->{$config['id_field']}]) }}"
                  onsubmit="return confirm('⚠️ HAPUS PERMANEN: Data akan hilang dari database dan TIDAK bisa dikembalikan. Lanjutkan?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background:linear-gradient(135deg,#ef4444,#b91c1c); color:white; border:none; border-radius:8px; padding:0.5rem 0.875rem">
                    🗑️ Hapus Permanen
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
                    <td style="color: #94a3b8; font-style: italic">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
