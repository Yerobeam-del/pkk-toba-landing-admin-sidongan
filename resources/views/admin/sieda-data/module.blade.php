{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Manajemen Data SIEDA — ' . $config['label'])
@section('page-title', 'Manajemen Data SIEDA — ' . $config['label'])

@section('content')
{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem">
    <div>
        <a href="{{ route('admin.sieda-data.index') }}" class="btn btn-outline-secondary btn-sm" style="margin-bottom:0.5rem;display:inline-flex;align-items:center;gap:0.4rem">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali ke Daftar Modul
        </a>
        <h1 style="margin:0 0 0.25rem 0; font-size:1.5rem; font-weight:800; color:var(--text-dark); letter-spacing:-0.5px">{{ $config['label'] }}</h1>
        <p style="color:var(--text-muted); margin:0; font-size:0.9rem">Kelola data {{ strtolower($config['label']) }} dari aplikasi SIEDA</p>
    </div>
    <div style="text-align:right; font-size:0.9rem; color:var(--text-muted)">
        <div><strong style="color:var(--text-dark)">{{ number_format($totalCount) }}</strong> total record</div>
        <div class="u-badge-green"><strong>{{ number_format($totalAktif) }}</strong> aktif</div>
    </div>
</div>

{{-- Search & Tampilkan --}}
<div class="u-header-row-wrap">
    {{-- Search --}}
    <div class="u-flex-1-min-200">
        <form method="GET" action="{{ route('admin.sieda-data.module', $module) }}">
            <input type="hidden" name="per_page" value="{{ $perPage }}">
            <div class="u-relative">
                <svg class="u-position-left" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input class="u-input-icon-left" type="text" name="search" value="{{ $search }}" placeholder="Cari {{ strtolower($config['id_label']) }} atau nama...">
                @if ($search)
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);text-decoration:none" title="Hapus pencarian">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Per Page --}}
    <div class="u-flex-center-gap-2-shrink">
        <form class="u-flex-center-gap-2" method="GET" action="{{ route('admin.sieda-data.module', $module) }}">
            <input type="hidden" name="search" value="{{ $search }}">
            <label class="u-a3">Tampilkan:</label>
            <div class="u-relative">
                <select class="u-select-mini" name="per_page">
                    @foreach ([25, 50, 100] as $pp)
                        <option value="{{ $pp }}" {{ $perPage == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                    @endforeach
                </select>
                <svg class="u-select-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Data (partial standar + pagination otomatis) --}}
<div class="card" style="overflow:hidden">
    <div class="u-p-0">
        @php
            $columns = [];

            // Kolom ID utama (style code)
            $columns[] = [
                'key' => $config['id_field'],
                'label' => $config['id_label'],
                'type' => 'callback',
                'callback' => function ($item) use ($config) {
                    return '<code class="u-code-chip">' . e($item->{$config['id_field']}) . '</code>';
                },
            ];

            if ($module === 'warga') {
                $columns[] = [
                    'key' => 'nama', 'label' => 'Nama', 'type' => 'callback',
                    'callback' => fn($item) => '<strong>' . e($item->nama ?? '-') . '</strong>',
                ];
                $columns[] = [
                    'key' => 'jenis_kelamin', 'label' => 'Jenis Kelamin', 'type' => 'callback',
                    'callback' => function ($item) {
                        if ($item->jenis_kelamin === 'L') return 'Laki-laki';
                        if ($item->jenis_kelamin === 'P') return 'Perempuan';
                        return e($item->jenis_kelamin ?? '-');
                    },
                ];
                $columns[] = [
                    'key' => 'tempat_lahir', 'label' => 'TTL', 'type' => 'callback',
                    'callback' => fn($item) => e($item->tempat_lahir ?? '-') . '<br><small class="u-muted-plain">' . e($item->tanggal_lahir ?? '-') . '</small>',
                ];
            } elseif ($module === 'keluarga') {
                $columns[] = [
                    'key' => 'kepalaKeluarga.nama', 'label' => 'Kepala Keluarga', 'type' => 'callback',
                    'callback' => fn($item) => '<strong>' . e($item->kepalaKeluarga?->nama ?? '-') . '</strong><br><small class="u-muted-plain">' . e($item->id_kepala_keluarga ?? '-') . '</small>',
                ];
                $columns[] = [
                    'key' => 'kelompokDasawisma.nama', 'label' => 'Dasawisma', 'type' => 'callback',
                    'callback' => fn($item) => e($item->kelompokDasawisma?->nama ?? '-'),
                ];
                $columns[] = [
                    'key' => 'config_year', 'label' => 'Tahun Config', 'type' => 'callback',
                    'callback' => fn($item) => e($item->config_year ?? '-'),
                ];
            } elseif ($module === 'anggota-keluarga') {
                $columns[] = [
                    'key' => 'nik', 'label' => 'NIK', 'type' => 'callback',
                    'callback' => fn($item) => '<code class="u-code-chip">' . e($item->nik) . '</code>',
                ];
                $columns[] = [
                    'key' => 'no_kk', 'label' => 'No. KK', 'type' => 'callback',
                    'callback' => fn($item) => '<code class="u-code-chip">' . e($item->no_kk) . '</code>',
                ];
            } elseif ($module === 'kelompok-dasawisma') {
                $columns[] = [
                    'key' => 'nama', 'label' => 'Nama Kelompok', 'type' => 'callback',
                    'callback' => fn($item) => '<strong>' . e($item->nama ?? '-') . '</strong>',
                ];
                $columns[] = [
                    'key' => 'dusun.nama', 'label' => 'Dusun', 'type' => 'callback',
                    'callback' => fn($item) => e($item->dusun?->nama ?? '-'),
                ];
                $columns[] = [
                    'key' => 'kader', 'label' => 'Kader', 'type' => 'callback',
                    'callback' => fn($item) => e($item->kader ?? '-'),
                ];
                $columns[] = [
                    'key' => 'config_year', 'label' => 'Tahun Config', 'type' => 'callback',
                    'callback' => fn($item) => e($item->config_year ?? '-'),
                ];
            } elseif ($module === 'catatan-ibu-anak') {
                $columns[] = [
                    'key' => 'id_warga_ibu', 'label' => 'Ibu (NIK)', 'type' => 'callback',
                    'callback' => fn($item) => '<code class="u-code-chip">' . e($item->id_warga_ibu ?? '-') . '</code>',
                ];
                $columns[] = [
                    'key' => 'status_ibu', 'label' => 'Status Ibu', 'type' => 'callback',
                    'callback' => function ($item) {
                        $status = $item->status_ibu;
                        if ($status === 'hamil') {
                            return '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(59,130,246,0.1);color:#1e40af">Hamil</span>';
                        }
                        if ($status === 'melahirkan') {
                            return '<span class="u-badge-green">Melahirkan</span>';
                        }
                        if ($status === 'nifas') {
                            return '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(245,158,11,0.1);color:#92400e">Nifas</span>';
                        }
                        return e($status ?? '-');
                    },
                ];
                $columns[] = [
                    'key' => 'tanggal_melahirkan', 'label' => 'Tgl Melahirkan', 'type' => 'callback',
                    'callback' => fn($item) => e($item->tanggal_melahirkan ?? '-'),
                ];
                $columns[] = [
                    'key' => 'config_year', 'label' => 'Tahun Config', 'type' => 'callback',
                    'callback' => fn($item) => e($item->config_year ?? '-'),
                ];
            }

            // Kolom status (aktif / terhapus di SIEDA)
            $columns[] = [
                'key' => 'active',
                'label' => 'Status',
                'type' => 'callback',
                'callback' => function ($item) {
                    if ((int) $item->active === 1) {
                        return '<span class="u-badge-green"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Aktif</span>';
                    }
                    return '<span class="u-a67"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Terhapus</span>';
                },
            ];

            // Label record untuk dialog konfirmasi hapus
            $rowLabel = function ($item) use ($module, $config) {
                if ($module === 'warga') return $item->nama ?? $item->{$config['id_field']};
                if ($module === 'keluarga') return $item->kepalaKeluarga?->nama ?? $item->{$config['id_field']};
                if ($module === 'kelompok-dasawisma') return $item->nama ?? $item->{$config['id_field']};
                if ($module === 'anggota-keluarga') return $item->nik ?? $item->{$config['id_field']};
                return $item->{$config['id_field']};
            };
        @endphp

        @include('admin.partials.table', [
            'data' => $items,
            'columns' => $columns,
            'emptyMessage' => 'Belum ada data pada modul ini. Data ditambahkan melalui aplikasi SIEDA.',
            'emptyIcon' => 'database',
            'actions' => [],
            'rowActions' => function ($item) use ($module, $config, $rowLabel) {
                $id = $item->{$config['id_field']};
                $name = $rowLabel($item);
                $styleBtn = 'width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer';
                $styleBtnOver = 'this.style.background=\'#eff6ff\';this.style.color=\'#2563eb\'';
                $styleBtnOut = 'this.style.background=\'transparent\';this.style.color=\'#94a3b8\'';

                $html = '<a href="' . route('admin.sieda-data.show', [$module, $id]) . '" title="Lihat Detail" style="' . $styleBtn . '">'
                    . '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>';

                $html .= '<button type="button" data-delete-item="' . $id . '" data-delete-title="' . addslashes($name) . '" title="Hapus Permanen" style="' . $styleBtn . ';border:none">'
                    . '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg></button>';

                $html .= '<form id="delete-form-' . $id . '" action="' . route('admin.sieda-data.force-delete', [$module, $id]) . '" method="POST" class="d-none">'
                    . csrf_field() . '<input type="hidden" name="_method" value="DELETE"></form>';

                return $html;
            }
        ])
    </div>
</div>

{{-- Hapus Semua Data (danger zone) --}}
<div class="card" style="margin-top:1.5rem; border:1px solid rgba(239,68,68,0.2); border-left:4px solid #dc2626; background:#fef2f2; padding:1.25rem 1.5rem; border-radius:12px">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap">
        <div style="display:flex; align-items:flex-start; gap:0.75rem; flex:1; min-width:260px">
            <div style="width:44px;height:44px;background:rgba(239,68,68,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
            </div>
            <div>
                <strong style="color:#991b1b">Hapus Semua Data</strong>
                <p style="margin:0.25rem 0 0 0; color:#7f1d1d; font-size:0.85rem">
                    Menghapus <strong>{{ number_format($totalCount) }} record</strong> {{ strtolower($config['label']) }}
                    @if (!empty($config['cascade_label']))
                        beserta data terkait ({{ $config['cascade_label'] }})
                    @endif
                    di database SIEDA secara permanen. Tindakan ini tidak bisa dibatalkan!
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.sieda-data.delete-all', $module) }}" class="delete-all-form"
              data-title="Hapus Semua Data {{ $config['label'] }}"
              data-message="Seluruh {{ number_format($totalCount) }} data <strong>{{ $config['label'] }}</strong>@if (!empty($config['cascade_label'])) beserta data terkait ({{ $config['cascade_label'] }})@endif di database SIEDA akan dihapus permanen dan TIDAK bisa dikembalikan. Lanjutkan?">
            @csrf
            <input type="hidden" name="confirm" value="1">
            <button type="submit" style="background:linear-gradient(135deg,#ef4444,#b91c1c);color:#fff;border:none;border-radius:8px;padding:0.6rem 1.25rem;display:inline-flex;align-items:center;gap:0.5rem;font-weight:600;font-size:0.875rem;cursor:pointer;transition:all 0.2s">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                </svg>
                Hapus Semua Data
            </button>
        </form>
    </div>
</div>

    <script src="{{ asset('assets/admin/js/admin-sieda-data-module.js') }}"></script>


@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
