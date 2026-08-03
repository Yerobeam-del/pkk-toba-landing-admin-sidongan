@extends('admin.layouts.app')
@section('title', 'Manajemen Data SIEDA — ' . $config['label'])
@section('page-title', 'Manajemen Data SIEDA — ' . $config['label'])

@section('content')
<style>
    .sieda-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem; }
    .btn-delete-perm { background: linear-gradient(135deg,#ef4444,#b91c1c); color: #fff; border: none; }
    .status-aktif { background: #d1fae5; color: #065f46; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    .status-terhapus { background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; }
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th { background: #f8fafc; text-align: left; padding: 0.75rem; font-weight: 600; color: #334155; border-bottom: 2px solid #e2e8f0; }
    td { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; }
    tr:hover td { background: #fafbfc; }
    .card { border-radius: 12px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .data-table-responsive { overflow-x: auto; }
    .data-table-responsive table { min-width: 700px; }
    .pagination { display: flex; gap: 0.25rem; justify-content: flex-end; margin-top: 1rem; }
    .page-link { padding: 0.375rem 0.75rem; border: 1px solid #dee2e6; border-radius: 6px; color: #334155; text-decoration: none; font-size: 0.9rem; }
    .page-link:hover { background: #e2e8f0; }
    .page-item.active .page-link { background: var(--primary); color: white; border-color: var(--primary); }
    .page-item.disabled .page-link { opacity: 0.4; pointer-events: none; }
    .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.8rem; }
</style>

{{-- Header --}}
<div class="sieda-header" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
    <div>
        <a href="{{ route('admin.sieda-data.index') }}" class="btn btn-outline-secondary btn-sm" style="margin-bottom:0.5rem;display:inline-flex;align-items:center;gap:0.4rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali ke Daftar Modul
        </a>
        <h1 style="margin:0 0 0.25rem 0">{{ $config['label'] }}</h1>
        <p style="color:#64748b; margin:0; font-size:0.9rem">Kelola data {{ strtolower($config['label']) }} dari aplikasi SIEDA</p>
    </div>
    <div style="text-align:right; font-size:0.9rem; color:#64748b">
        <div><strong>{{ number_format($totalCount) }}</strong> total record</div>
        <div style="color:#991b1b"><strong>{{ number_format($softDeletedCount) }}</strong> di recycle bin</div>
    </div>
</div>

{{-- Filter Status --}}
<div class="card" style="padding:1.25rem; margin-bottom:1.5rem">
    <form method="GET" style="display:flex; gap:1rem; flex-wrap:wrap; align-items:center">
        <div style="display:flex; gap:0.5rem; align-items:center">
            <label style="font-weight:600; font-size:0.9rem">Status:</label>
            <div style="display:flex; gap:0.5rem; border:1px solid #dee2e6; border-radius:8px; padding:2px; background:#f8fafc">
                <a href="?status=aktif{{ $search ? '&search=' . urlencode($search) : '' }}"
                   class="btn btn-sm {{ $filterStatus === 'aktif' ? 'btn-primary' : 'btn-outline-secondary' }}"
                   style="border-radius:6px; padding:0.35rem 0.85rem">
                    Aktif
                </a>
                <a href="?status=terhapus{{ $search ? '&search=' . urlencode($search) : '' }}"
                   class="btn btn-sm {{ $filterStatus === 'terhapus' ? 'btn-warning' : 'btn-outline-secondary' }}"
                   style="border-radius:6px; padding:0.35rem 0.85rem">
                    Recycle Bin ({{ number_format($softDeletedCount) }})
                </a>
            </div>
        </div>

        {{-- Search --}}
        <div style="display:flex; gap:0.5rem; flex:1; min-width:200px; max-width:400px">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari {{ strtolower($config['id_label']) }}, nama, NIK..."
                   class="form-control" style="border-radius:8px; border:1px solid #dee2e6; padding:0.5rem 0.75rem; font-size:0.875rem">
            <button type="submit" class="btn btn-primary" style="border-radius:8px; padding:0.5rem 1rem">Cari</button>
            @if ($search)
                <a href="?status={{ $filterStatus }}" class="btn btn-outline-secondary" style="border-radius:8px; padding:0.5rem 0.75rem">Reset</a>
            @endif
        </div>

        {{-- Per Page --}}
        <div style="display:flex; gap:0.5rem; align-items:center">
            <label style="font-weight:600; font-size:0.9rem; color:#64748b">Tampilkan:</label>
            <select name="per_page" onchange="this.form.submit()" class="form-select" style="border-radius:8px; border:1px solid #dee2e6; padding:0.5rem 0.75rem; font-size:0.875rem; width:auto">
                @foreach ([25, 50, 100] as $pp)
                    <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card" style="overflow:hidden">
    @if ($items->isEmpty())
        <div style="padding: 3rem 2rem; text-align: center; color: var(--text-muted)">
            <svg style="width: 48px; height: 48px; margin: 0 auto 12px; opacity: 0.4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 12a9 9 0 1 0-18 0 9 9 0 0 0 18 0Z"/><path d="M12 7v4l2 2"/>
            </svg>
            <p style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.25rem">Tidak ada data ditemukan</p>
            <p style="margin: 0; font-size: 0.9rem">
                @if ($search)
                    Coba ubah kata kunci pencarian Anda.
                @else
                    @if ($filterStatus === 'terhapus')
                        Recycle Bin kosong. Semua data masih aktif.
                    @else
                        Belum ada data di modul ini. Silakan tambahkan di aplikasi SIEDA.
                    @endif
                @endif
            </p>
        </div>
    @else
        <div class="data-table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>{{ $config['id_label'] }}</th>
                        @if ($module === 'warga')
                            <th>Nama</th><th>Jenis Kelamin</th><th>TTL</th>
                        @elseif ($module === 'keluarga')
                            <th>Kepala Keluarga</th><th>Dasawisma</th><th>Tahun Config</th>
                        @elseif ($module === 'anggota-keluarga')
                            <th>NIK</th><th>No. KK</th>
                        @elseif ($module === 'kelompok-dasawisma')
                            <th>Nama Kelompok</th><th>Dusun</th><th>Kader</th>
                        @elseif ($module === 'catatan-ibu-anak')
                            <th>IBU (NIK)</th><th>Status Ibu</th><th>Tgl Melahirkan</th><th>Tahun Config</th>
                        @endif
                        <th style="width:220px; text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td>{{ $items->firstItem() + $index }}</td>
                            <td>
                                <code style="font-family:monospace; background:#f1f5f9; padding:2px 6px; border-radius:4px; font-size:0.85rem">
                                    {{ $item->{$config['id_field']} }}
                                </code>
                            </td>
                            @if ($module === 'warga')
                                <td><strong>{{ $item->nama ?? '-' }}</strong></td>
                                <td>{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : ($item->jenis_kelamin === 'P' ? 'Perempuan' : $item->jenis_kelamin) }}</td>
                                <td>{{ optional($item)->tempat_lahir }}<br><small style="color:#64748b">{{ optional($item)->tanggal_lahir }}</small></td>
                            @elseif ($module === 'keluarga')
                                <td>{{ optional($item->kepalaKeluarga)->nama ?? '-' }} <br><small style="color:#64748b">{{ $item->id_kepala_keluarga }}</small></td>
                                <td>{{ optional($item->kelompokDasawisma)->nama ?? '-' }}</td>
                                <td>{{ $item->config_year ?? '-' }}</td>
                            @elseif ($module === 'anggota-keluarga')
                                <td><code style="font-family:monospace; font-size:0.85rem">{{ $item->nik }}</code></td>
                                <td><code style="font-family:monospace; font-size:0.85rem">{{ $item->no_kk }}</code></td>
                            @elseif ($module === 'kelompok-dasawisma')
                                <td><strong>{{ $item->nama ?? '-' }}</strong></td>
                                <td>{{ optional($item->dusun)->nama ?? '-' }}</td>
                                <td>{{ $item->kader ?? '-' }}</td>
                            @elseif ($module === 'catatan-ibu-anak')
                                <td><code style="font-family:monospace; font-size:0.85rem">{{ $item->id_warga_ibu }}</code></td>
                                <td>
                                    @if ($item->status_ibu === 'hamil')
                                        <span class="status-aktif" style="background:#dbeafe; color:#1e40af">Hamil</span>
                                    @elseif ($item->status_ibu === 'melahirkan')
                                        <span class="status-aktif" style="background:#d1fae5; color:#065f46">Melahirkan</span>
                                    @elseif ($item->status_ibu === 'nifas')
                                        <span class="status-aktif" style="background:#fef3c7; color:#92400e">Nifas</span>
                                    @else
                                        {{ $item->status_ibu ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $item->tanggal_melahirkan ?? '-' }}</td>
                                <td>{{ $item->config_year ?? '-' }}</td>
                            @endif
                            <td style="width:220px; text-align:right">
                                {{-- Lihat detail --}}
                                <a href="{{ route('admin.sieda-data.show', [$module, $item->{$config['id_field']}]) }}"
                                   class="btn btn-outline-primary btn-sm" title="Lihat Detail"
                                   style="white-space:nowrap">
                                    Detail
                                </a>

                                @if ($item->active == 0)
                                    {{-- Restore dari recycle bin --}}
                                    <form method="POST" style="display:inline"
                                          action="{{ route('admin.sieda-data.restore', [$module, $item->{$config['id_field']}]) }}"
                                          onsubmit="return confirm('Pulihkan record ini ke kondisi aktif?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm" title="Pulihkan" style="white-space:nowrap">
                                            ↩️ Restore
                                        </button>
                                    </form>
                                @else
                                    {{-- Hapus (soft-delete via SIEDA atau hard-delete via Admin) --}}
                                    <form method="POST" style="display:inline"
                                          action="{{ route('admin.sieda-data.force-delete', [$module, $item->{$config['id_field']}]) }}"
                                          onsubmit="return confirm('⚠️ Hapus permanen? Data akan hilang dari database dan TIDAK bisa dikembalikan. Lanjutkan?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete-perm btn-sm" title="Hapus Permanen" style="white-space:nowrap">
                                            🗑️ Hapus Permanen
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($items->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9">
                {{ $items->appends(['status' => $filterStatus, 'search' => $search, 'per_page' => $perPage])->links('pagination::bootstrap-4') }}
            </div>
        @endif
    @endif
</div>

{{-- Perhatian --}}
@if ($filterStatus === 'aktif')
    <div class="card" style="border-left:3px solid #f59e0b; background:#fffbeb; padding:1rem 1.25rem; margin-top:1rem; border-radius:8px">
        <div style="display:flex; align-items:center; gap:0.5rem">
            <svg style="flex-shrink:0; color:#f59e0b" width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/>
            </svg>
            <span style="font-size:0.85rem; color:#78350f">
                Record yang dihapus tidak hilang — hanya <code>active=0</code> (disembunyikan dari aplikasi SIEDA). Gunakan tombol <strong>Hapus Permanen</strong> di Recycle Bin jika benar-benar perlu.
            </span>
        </div>
    </div>
@endif

@endsection
