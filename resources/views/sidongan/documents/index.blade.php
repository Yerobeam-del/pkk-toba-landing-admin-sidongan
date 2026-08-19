{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Daftar Surat - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    
    // Helper function untuk sort icon
    function sortIcon($field, $currentSort, $currentDirection) {
        if ($currentSort !== $field) {
            return '<i class="fas fa-sort" style="color: rgba(255,255,255,0.6); margin-left: 0.5rem;"></i>';
        }
        return $currentDirection === 'asc' 
            ? '<i style="color: white; margin-left: 0.5rem;" class="fas fa-sort-up"></i>'
            : '<i style="color: white; margin-left: 0.5rem;" class="fas fa-sort-down"></i>';
    }
    
    // Helper function untuk sort URL
    function sortUrl($field, $currentSort, $currentDirection) {
        $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
        $params = array_merge(request()->all(), ['sort' => $field, 'direction' => $newDirection]);
        return route('sidongan.documents.index', $params);
    }
@endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-documents-index.css') }}">


<div class="sd-page u-px-6">
    {{-- Header Section --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="animate-slide-in">
        <div>
            <h1 class="u-h2-slate">Daftar Surat</h1>
            <p class="u-text-muted-lead">Kelola semua dokumen surat masuk dan keluar</p>
        </div>
        @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
        <a href="{{ route('sidongan.documents.create') }}" class="btn-action" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0891b2, #06b6d4); color: white; text-decoration: none; border-radius: 0.75rem; font-weight: 600; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.25); font-size: 0.95rem;">
            <i class="fas fa-plus" style="font-size: 1rem;"></i>
            <span>Buat Surat Baru</span>
        </a>
        @endif
    </div>

    {{-- Stats Cards --}}
    @php
        $statsQuery = \App\Models\Document::query();
        if ($currentUser && $currentUser->hasSidonganRole('sekretaris')) {
            $statsQuery->where('created_by', $currentUser->id);
        }
        $statSelesai = (clone $statsQuery)->where('status', 'selesai')->count();
        $statBerjalan = (clone $statsQuery)->where('status', 'berjalan')->count();
        $statMenunggu = (clone $statsQuery)->whereIn('status', ['menunggu_disposisi', 'menunggu_verifikasi'])->count();
    @endphp
    
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        {{-- Total Surat --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-md">
                    <i class="fas fa-envelope u-text-1xl"></i>
                </div>
                <div>
                    <p class="u-subtitle-sm">Total Surat</p>
                    <p class="u-subtitle-sm">{{ $totalDocuments ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        {{-- Selesai --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-md">
                    <i class="fas fa-check-circle u-text-1xl"></i>
                </div>
                <div>
                    <p class="u-subtitle-sm">Selesai</p>
                    <p class="u-subtitle-sm">{{ $statSelesai ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        {{-- Berjalan --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-md">
                    <i class="fas fa-spinner fa-spin u-text-1xl"></i>
                </div>
                <div>
                    <p class="u-subtitle-sm">Berjalan</p>
                    <p class="u-subtitle-sm">{{ $statBerjalan ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        {{-- Menunggu Disposisi --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #eab308, #ca8a04); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(234, 179, 8, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-md">
                    <i class="fas fa-clock u-text-1xl"></i>
                </div>
                <div>
                    <p class="u-subtitle-sm">Menunggu Disposisi</p>
                    <p class="u-subtitle-sm">{{ $statMenungguDisposisi ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 1.5rem;" class="animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.documents.index') }}" data-base-url="{{ route('sidongan.documents.index') }}">
            
            {{-- Row 1: Search, Per Page, Status --}}
            <div style="display: grid; grid-template-columns: 2fr 0.5fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                <div>
                    <label class="u-label-slate">Cari Dokumen</label>
                    <div class="u-relative">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Ketik untuk mencari berdasarkan judul atau nomor..." style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Tampilkan</label>
                    <div class="u-relative">
                        <select class="u-label-slate" name="per_page" id="perPageSelect">
                            @foreach($allowedPerPages as $value)
                                <option value="{{ $value }}" {{ ($currentPerPage ?? 10) == $value ? 'selected' : '' }}>
                                    {{ $value }} surat
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down u-select-chevron-right"></i>
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Status</label>
                    <div class="u-relative">
                        <select class="u-label-slate" name="status" id="statusSelect">
                            <option value="">Semua Status</option>
                            <option value="menunggu_disposisi" {{ request('status') == 'menunggu_disposisi' ? 'selected' : '' }}>Menunggu Disposisi</option>
                            <option value="berjalan" {{ request('status') == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                            <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="diarsipkan" {{ request('status') == 'diarsipkan' ? 'selected' : '' }}>Diarsipkan</option>
                        </select>
                        <i class="fas fa-chevron-down u-select-chevron-right"></i>
                    </div>
                </div>
            </div>

            {{-- Row 2: Quick Filters, Date Filters, Reset --}}
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                
                <div>
                    <label class="u-label-slate">Filter Cepat</label>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" data-filter-status="menunggu_disposisi" class="filter-btn" style="padding: 0.5rem 0.75rem; background: {{ request('status') == 'menunggu_disposisi' ? '#fef3c7' : '#f1f5f9' }}; color: {{ request('status') == 'menunggu_disposisi' ? '#92400e' : '#475569' }}; border: 1px solid {{ request('status') == 'menunggu_disposisi' ? '#fde68a' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                            <i style="margin-right: 0.35rem; font-size: 0.7rem;" class="fas fa-hourglass-half"></i>
                            Menunggu Disposisi
                        </button>
                        <button type="button" data-filter-status="berjalan" class="filter-btn" style="padding: 0.5rem 0.75rem; background: {{ request('status') == 'berjalan' ? '#ffedd5' : '#f1f5f9' }}; color: {{ request('status') == 'berjalan' ? '#c2410c' : '#475569' }}; border: 1px solid {{ request('status') == 'berjalan' ? '#fed7aa' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                            <i style="margin-right: 0.35rem; font-size: 0.7rem;" class="fas fa-spinner"></i>
                            Berjalan
                        </button>
                        <button type="button" data-filter-status="selesai" class="filter-btn" style="padding: 0.5rem 0.75rem; background: {{ request('status') == 'selesai' ? '#d1fae5' : '#f1f5f9' }}; color: {{ request('status') == 'selesai' ? '#065f46' : '#475569' }}; border: 1px solid {{ request('status') == 'selesai' ? '#a7f3d0' : '#e2e8f0' }}; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                            <i style="margin-right: 0.35rem; font-size: 0.7rem;" class="fas fa-check-circle"></i>
                            Selesai
                        </button>
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Bulan</label>
                    <div class="u-relative">
                        <select class="u-label-slate" name="filter_month">
                            <option value="">Semua Bulan</option>
                            @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                <option value="{{ $num }}" {{ request('filter_month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down u-select-chevron-right"></i>
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Tahun</label>
                    <div class="u-relative">
                        <select class="u-label-slate" name="filter_year">
                            <option value="">Semua Tahun</option>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ request('filter_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down u-select-chevron-right"></i>
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                </div>

                <div>
                    <label class="u-label-slate">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white;">
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: end;">
                    <button type="button" data-action="reset-filters" style="padding: 0.625rem 1rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;">
                        <i class="fas fa-undo u-mr-1"></i>
                        Reset
                    </button>
                </div>
            </div>

            @if($currentSort)
            <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #1e40af;">
                    <i class="fas fa-sort-amount-down"></i>
                    <span style="font-weight: 600;">Diurutkan:</span>
                    <span>
                        @php
                            $sortLabels = [
                                'id' => 'No. Urut',
                                'agenda_number' => 'No. Agenda',
                                'subject' => 'Perihal',
                                'document_number' => 'No. Surat',
                                'document_date' => 'Tanggal',
                                'status' => 'Status',
                                'created_at' => 'Tanggal Dibuat',
                            ];
                            $sortLabel = $sortLabels[$currentSort] ?? $currentSort;
                            $directionLabel = $currentDirection === 'asc' ? 'Terlama/Ascending' : 'Terbaru/Descending';
                        @endphp
                        {{ $sortLabel }} ({{ $directionLabel }})
                    </span>
                </div>
                <button type="button" data-action="reset-sorting" style="padding: 0.35rem 0.75rem; background: #3b82f6; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    <i style="margin-right: 0.25rem;" class="fas fa-times"></i>
                    Hapus Urutan
                </button>
            </div>
            @endif
        </form>
    </div>

    {{-- Documents Table --}}
    <div class="animate-slide-in u-a78">
        @if(isset($documents) && $documents->count() > 0)
        <div class="sd-table-wrap" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: linear-gradient(135deg, #0891b2, #14b8a6); color: white;">
                    <tr>
                        <th style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; width: 60px; cursor: pointer; white-space: nowrap;"
                            data-sort-url="{{ sortUrl('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                NO {!! sortIcon('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain" 
                            data-sort-url="{{ sortUrl('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                NO. AGENDA {!! sortIcon('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain"
                            data-sort-url="{{ sortUrl('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                PERIHAL {!! sortIcon('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain"
                            data-sort-url="{{ sortUrl('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                NO. SURAT {!! sortIcon('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain"
                            data-sort-url="{{ sortUrl('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                TANGGAL {!! sortIcon('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; white-space: nowrap;">DISPOSISI</th>
                        <th class="u-th-plain"
                            data-sort-url="{{ sortUrl('status', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                STATUS {!! sortIcon('status', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; min-width: 220px; white-space: nowrap;">AKSI TERAKHIR</th>
                        <th style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; white-space: nowrap;">AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($documents as $index => $doc)
                    <tr class="sd-row">
                        <td data-label="No" style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.875rem;">
                            {{ $documents->firstItem() + $index }}
                        </td>
                        <td data-label="No. Agenda" style="padding: 1rem; font-weight: 600; color: #3b82f6; font-family: monospace; font-size: 0.875rem;">
                            {{ $doc->agenda_number ?? '-' }}
                        </td>
                        
                        <td data-label="Perihal" class="sd-cell-stack u-p-4">
                            <div style="font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">{{ Str::limit($doc->subject ?? $doc->title, 60) }}</div>
                            @if($doc->sender)
                            <div style="font-size: 0.75rem; color: #64748b;">{{ $doc->sender }}</div>
                            @endif
                        </td>
                        
                        <td style="padding: 1rem; color: #475569; font-size: 0.875rem;" data-label="No. Surat">
                            {{ $doc->document_number ?? '-' }}
                        </td>
                        
                        <td style="padding: 1rem; color: #475569; font-size: 0.875rem;" data-label="Tanggal">
                            {{ $doc->document_date ? $doc->document_date->locale('id')->translatedFormat('d F Y') : '-' }}
                        </td>
                        
                        <td data-label="Disposisi" class="sd-cell-chips u-p-4">
                            @php
                                $disposisiData = is_string($doc->disposisi_data) ? json_decode($doc->disposisi_data, true) : $doc->disposisi_data;
                            @endphp
                            @if($disposisiData && isset($disposisiData['target_roles']))
                            @php
                                $rolesMap = [
                                    'sekretaris' => 'Sekretaris PKK',
                                    'bendahara' => 'Bendahara PKK',
                                    'staf_ahli_1' => 'Staf Ahli I',
                                    'staf_ahli_2' => 'Staf Ahli II',
                                    'pengurus_1' => 'Ketua Pengurus I',
                                    'pengurus_2' => 'Ketua Pengurus II',
                                    'pengurus_3' => 'Ketua Pengurus III',
                                    'pengurus_4' => 'Ketua Pengurus IV',
                                ];
                            @endphp
                            @foreach($disposisiData['target_roles'] as $role)
                                <span style="display: inline-block; padding: 0.25rem 0.5rem; background: #dbeafe; color: #1e40af; border-radius: 0.25rem; font-size: 0.75rem; margin: 0.125rem;">
                                    {{ $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                                </span>
                            @endforeach
                            @else
                                <span style="color: #94a3b8; font-size: 0.75rem;">Belum</span>
                            @endif
                        </td>
                        
                        <td data-label="Status" class="sd-cell-stack u-p-4">
                            @php
                                $reports = $doc->activityReports ?? collect();
                                $hasReport = $reports->count() > 0;
                                
                                $rejectedReports = $reports->where('status', 'ditolak');
                                $hasRejected = $rejectedReports->count() > 0;
                                
                                $approvedReports = $reports->where('status', 'disetujui');
                                $hasApproved = $approvedReports->count() > 0;
                                
                                $pendingReports = $reports->where('status', 'menunggu_verifikasi');
                                $hasPending = $pendingReports->count() > 0;
                                
                                $allReported = false;
                                $dispoData = is_string($doc->disposisi_data) 
                                    ? json_decode($doc->disposisi_data, true) 
                                    : $doc->disposisi_data;
                                
                                if ($dispoData && isset($dispoData['target_roles'])) {
                                    // Satu surat = SATU laporan: surat dianggap sudah
                                    // dilaporkan bila ada laporan apa pun.
                                    $allReported = $reports->count() > 0;
                                }
                            @endphp

                            @if($doc->status === 'menunggu_disposisi')
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.75rem; background: #fef3c7; color: #92400e; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-hourglass-half u-text-xs"></i>
                                    Menunggu Disposisi
                                </span>

                            @elseif($doc->status === 'berjalan')
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.75rem; background: #ffedd5; color: #9a3412; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; width: fit-content;">
                                        <i class="fas fa-spinner fa-spin u-text-xs"></i>
                                        Berjalan
                                    </span>
                                    
                                    @if($hasRejected)
                                        @foreach($rejectedReports as $rejected)
                                            <div style="padding: 0.5rem 0.75rem; background: #fef2f2; border-left: 3px solid #ef4444; border-radius: 0.25rem; font-size: 0.75rem;">
                                                <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.5rem; background: #fee2e2; color: #991b1b; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 600;">
                                                        <i class="fas fa-times-circle u-text-xxs"></i>
                                                        Laporan Ditolak
                                                    </span>
                                                </div>
                                                <div style="color: #991b1b; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $rejected->creator->name ?? 'Unknown' }}
                                                </div>
                                                @if($rejected->catatan_verifikasi)
                                                    <div style="color: #7f1d1d; font-style: italic; font-size: 0.7rem; line-height: 1.4; margin-top: 0.25rem;">
                                                        "{{ Str::limit($rejected->catatan_verifikasi, 60) }}"
                                                    </div>
                                                @endif
                                                <div style="color: #94a3b8; font-size: 0.65rem; margin-top: 0.2rem;">
                                                    {{ $rejected->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if($hasApproved)
                                        @foreach($approvedReports as $approved)
                                            <div style="padding: 0.5rem 0.75rem; background: #f0fdf4; border-left: 3px solid #22c55e; border-radius: 0.25rem; font-size: 0.75rem;">
                                                <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.5rem; background: #d1fae5; color: #065f46; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 600;">
                                                        <i class="fas fa-check-circle u-text-xxs"></i>
                                                        Laporan Disetujui
                                                    </span>
                                                </div>
                                                <div style="color: #065f46; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $approved->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div style="color: #94a3b8; font-size: 0.65rem; margin-top: 0.2rem;">
                                                    {{ $approved->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if($hasPending)
                                        @foreach($pendingReports as $pending)
                                            <div style="padding: 0.5rem 0.75rem; background: #eff6ff; border-left: 3px solid #3b82f6; border-radius: 0.25rem; font-size: 0.75rem;">
                                                <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem;">
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.5rem; background: #dbeafe; color: #1e40af; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 600;">
                                                        <i class="fas fa-clock u-text-xxs"></i>
                                                        Menunggu Verifikasi
                                                    </span>
                                                </div>
                                                <div style="color: #1e40af; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $pending->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div style="color: #94a3b8; font-size: 0.65rem; margin-top: 0.2rem;">
                                                    {{ $pending->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if(!$hasReport)
                                        <div style="padding: 0.5rem 0.75rem; background: #f8fafc; border-left: 3px solid #cbd5e1; border-radius: 0.25rem; font-size: 0.75rem;">
                                            <div style="color: #64748b; display: flex; align-items: center; gap: 0.35rem;">
                                                <i class="fas fa-file-circle-xmark u-text-xs"></i>
                                                Belum ada laporan
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @elseif($doc->status === 'menunggu_verifikasi')
                                <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.75rem; background: #dbeafe; color: #1e40af; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; width: fit-content;">
                                        <i class="fas fa-clock u-text-xs"></i>
                                        Menunggu Verifikasi
                                    </span>
                                    @if($allReported)
                                        <div style="padding: 0.5rem 0.75rem; background: #eff6ff; border-left: 3px solid #3b82f6; border-radius: 0.25rem; font-size: 0.75rem;">
                                            <div style="color: #1e40af; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                                                <i class="fas fa-clock u-text-xs"></i>
                                                Laporan sedang menunggu verifikasi ketua
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @elseif($doc->status === 'selesai')
                                <div style="display: flex; flex-direction: column; gap: 0.35rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.75rem; background: #d1fae5; color: #065f46; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; width: fit-content;">
                                        <i class="fas fa-check-circle u-text-xs"></i>
                                        Selesai
                                    </span>
                                    @if($hasApproved)
                                        @foreach($approvedReports as $approved)
                                            <div style="padding: 0.5rem 0.75rem; background: #f0fdf4; border-left: 3px solid #22c55e; border-radius: 0.25rem; font-size: 0.75rem;">
                                                <div style="color: #065f46; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $approved->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div style="color: #94a3b8; font-size: 0.65rem; margin-top: 0.15rem;">
                                                    {{ $approved->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            @elseif($doc->status === 'diarsipkan')
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.375rem 0.75rem; background: #f3e8ff; color: #7c3aed; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="fas fa-archive u-text-xs"></i>
                                    Diarsipkan
                                </span>

                            @else
                                <span style="display: inline-block; padding: 0.375rem 0.75rem; background: #f1f5f9; color: #475569; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">
                                    {{ ucfirst(str_replace('_', ' ', $doc->status)) }}
                                </span>
                            @endif
                        </td>
                        
<td data-label="Aksi Terakhir" class="sd-cell-stack u-p-4">
    @php
        $actions = [];
        
        $actions[] = [
            'action' => 'Dokumen Dibuat',
            'time' => \Carbon\Carbon::parse($doc->created_at),
            'user' => $doc->creator,
            'type' => 'info',
        ];
        
        if ($doc->disposisi_data) {
            $dispoData = is_string($doc->disposisi_data) 
                ? json_decode($doc->disposisi_data, true) 
                : $doc->disposisi_data;
            
            if (isset($dispoData['disposed_by'])) {
                $disposedBy = \App\Models\User::find($dispoData['disposed_by']);
                if ($disposedBy) {
                    $actions[] = [
                        'action' => 'Disposisi',
                        'time' => isset($dispoData['disposed_at']) 
                            ? \Carbon\Carbon::parse($dispoData['disposed_at'])
                            : \Carbon\Carbon::parse($doc->updated_at),
                        'user' => $disposedBy,
                        'type' => 'warning',
                    ];
                }
            }
        }
        
        $allReports = $doc->activityReports()->with('creator')->get();
        foreach ($allReports as $rpt) {
            $actions[] = [
                'action' => 'Buat Laporan',
                'time' => \Carbon\Carbon::parse($rpt->created_at),
                'user' => $rpt->creator,
                'type' => 'primary',
            ];
            
            if ($rpt->verified_at && $rpt->verified_by) {
                $verifier = \App\Models\User::find($rpt->verified_by);
                if ($verifier) {
                    $verifLabel = $rpt->status === 'disetujui' ? 'Laporan Disetujui' : 'Laporan Ditolak';
                    $actions[] = [
                        'action' => $verifLabel,
                        'time' => \Carbon\Carbon::parse($rpt->verified_at),
                        'user' => $verifier,
                        'type' => $rpt->status === 'disetujui' ? 'success' : 'danger',
                    ];
                }
            }
        }
        
        usort($actions, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });
        
        $latestAction = $actions[0] ?? null;
        
        $badgeColors = [
            'success' => ['bg' => '#d1fae5', 'text' => '#065f46', 'icon' => 'fa-check-circle'],
            'danger' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'icon' => 'fa-times-circle'],
            'primary' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'icon' => 'fa-file-alt'],
            'warning' => ['bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'fa-share-alt'],
            'info' => ['bg' => '#e0f2fe', 'text' => '#075985', 'icon' => 'fa-plus'],
        ];
    @endphp

    @if($latestAction && $latestAction['user'])
        @php $color = $badgeColors[$latestAction['type']] ?? $badgeColors['info']; @endphp
        <div class="u-flex-center-gap-3">
            @if($latestAction['user']->avatar)
                <img src="{{ asset('storage/' . $latestAction['user']->avatar) }}" 
                     alt="{{ $latestAction['user']->name }}"
                     style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            @else
                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.75rem; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr($latestAction['user']->name, 0, 1)) }}
                </div>
            @endif
            
            <div class="u-flex-1-min">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.5rem; background: {{ $color['bg'] }}; color: {{ $color['text'] }}; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 600; margin-bottom: 0.25rem;">
                    <i class="fas {{ $color['icon'] }} u-text-xxs"></i>
                    {{ $latestAction['action'] }}
                </span>
                <div style="font-weight: 600; color: #0f172a; font-size: 0.875rem;">
                    {{ $latestAction['user']->name }}
                </div>
                <div style="font-size: 0.7rem; color: #64748b;">
                    {{ $latestAction['time']->locale('id')->translatedFormat('d F Y, H.i') }}
                </div>
            </div>
        </div>
    @else
        <span style="color: #94a3b8; font-size: 0.85rem;">-</span>
    @endif
</td>
                        
                        <td data-label="Aksi" class="sd-cell-stack" style="padding: 1rem; white-space: nowrap;">
                            <div class="sd-actions" style="display: flex; gap: 0.5rem; justify-content: center;">
                                <a href="{{ route('sidongan.documents.show', $doc) }}"
                                class="sd-icon-btn sd-icon-view"
                                
                                title="Lihat Detail">
                                    <i class="fas fa-eye u-text-sm"></i>
                                </a>
                                
                                @if($currentUser && $currentUser->hasSidonganRole('sekretaris') && $doc->status === 'menunggu_disposisi')
                                    <a href="{{ route('sidongan.documents.edit', $doc) }}"
                                    class="sd-icon-btn sd-icon-view sd-icon-edit"
                                    
                                    title="Edit Surat">
                                        <i class="fas fa-edit u-text-sm"></i>
                                    </a>
                                    
                                    <button type="button"
                                            class="sd-icon-btn sd-icon-view sd-icon-delete"
                                            data-delete-id="{{ $doc->id }}" data-delete-title="{{ addslashes($doc->subject ?? $doc->title) }}"
                                            
                                            title="Hapus Surat">
                                        <i class="fas fa-trash u-text-sm"></i>
                                    </button>
                                @endif

                    			@php
                                    $canArchive = false;
                                    $dispoData = is_string($doc->disposisi_data) 
                                        ? json_decode($doc->disposisi_data, true) 
                                        : $doc->disposisi_data;

                                    // Jika tindak lanjut disposisi adalah "Di Arsipkan"
                                    $isArsipDisposition = false;
                                    if (isset($dispoData['action'])) {
                                        $actionLower = strtolower($dispoData['action']);
                                        if (strpos($actionLower, 'arsip') !== false) {
                                            $isArsipDisposition = true;
                                        }
                                    }

                                    if ($isArsipDisposition && $doc->status === 'berjalan') {
                                        // Langsung bisa arsip jika disposisi "Di Arsipkan" dan status berjalan
                                        $canArchive = true;
                                    } elseif ($doc->status === 'selesai') {
                                        // Status selesai berarti laporan terakhir sudah
                                        // disetujui ketua (satu surat = satu laporan).
                                        $latestReport = $doc->activityReports()
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                        $canArchive = $latestReport && $latestReport->status === 'disetujui';
                                    }
                                @endphp

                                @if($currentUser && $currentUser->hasSidonganRole('sekretaris') && $canArchive)
                                    <button type="button"
                                            class="sd-icon-btn sd-icon-view sd-icon-archive"
                                            data-archive-id="{{ $doc->id }}" data-archive-title="{{ addslashes($doc->subject ?? $doc->title) }}"
                                            
                                            title="Arsipkan Surat">
                                        <i class="fas fa-archive u-text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
        <div style="padding: 1.25rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="u-text-sm-muted-2">
                Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
            </div>
            
            <div class="sd-pagination u-a79">
                @if($documents->onFirstPage())
                    <span class="u-a15">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" 
                    class="sd-page-btn sd-page-btn-blue">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endif
                
                @php
                    $currentPage = $documents->currentPage();
                    $lastPage = $documents->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                    
                    if ($endPage - $startPage < 4) {
                        if ($startPage == 1) $endPage = min(5, $lastPage);
                        if ($endPage == $lastPage) $startPage = max(1, $lastPage - 4);
                    }
                @endphp
                
                @if($startPage > 1)
                    <a href="{{ $documents->url(1) }}" 
                    class="sd-page-btn sd-page-btn-white">
                        1
                    </a>
                    @if($startPage > 2)
                        <span class="u-a16">...</span>
                    @endif
                @endif
                
                @for($i = $startPage; $i <= $endPage; $i++)
                    @if($i == $currentPage)
                        <span class="sd-page-btn sd-page-btn-blue">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $documents->url($i) }}" 
                        class="sd-page-btn sd-page-btn-white">
                            {{ $i }}
                        </a>
                    @endif
                @endfor
                
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="u-a16">...</span>
                    @endif
                    <a href="{{ $documents->url($lastPage) }}" 
                    class="sd-page-btn sd-page-btn-white">
                        {{ $lastPage }}
                    </a>
                @endif
                
                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" 
                    class="sd-page-btn sd-page-btn-blue">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span class="u-a15">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        @else
        <div style="padding: 1.25rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #f8fafc;">
            <div class="u-text-sm-muted-2">
                Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
            </div>
            <div class="u-text-xs-muted">
                <i class="fas fa-info-circle"></i> Semua surat ditampilkan dalam satu halaman
            </div>
        </div>
        @endif
        @else
            <div style="padding: 4rem 2rem; text-align: center;">
                <div style="width: 96px; height: 96px; background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; animation: float 3s ease-in-out infinite;">
                    <i class="fas fa-inbox" style="color: #0891b2; font-size: 3rem;"></i>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">Belum Ada Dokumen</h3>
                <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 1.5rem 0; max-width: 400px; margin-left: auto; margin-right: auto;">Belum ada dokumen surat yang ditemukan.</p>
                @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
                <a href="{{ route('sidongan.documents.create') }}" class="btn-action" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0891b2, #06b6d4); color: white; text-decoration: none; border-radius: 0.75rem; font-weight: 600; box-shadow: 0 4px 12px rgba(8, 145, 178, 0.25); font-size: 0.95rem;">
                    <i class="fas fa-plus" style="font-size: 1rem;"></i>
                    <span>Buat Surat Pertama</span>
                </a>
                @endif
            </div>
        @endif
    </div>
</div>

<form class="u-hidden" id="deleteForm" method="POST" data-base-url="{{ url('/sidongan/documents') }}">
    @csrf
    @method('DELETE')
</form>

<form class="u-hidden" id="archiveForm" method="POST" data-base-url="{{ url('/sidongan/documents') }}">
    @csrf
    @method('PATCH')
</form>

@push('scripts')
    <script src="{{ asset('assets/sidongan/js/documents-index.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
