{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Daftar Surat - SIDONGAN')

@section('content')    @php
        $currentUser = auth()->guard('sidongan')->user();
        
        // Helper function untuk sort icon
        $sortIconFn = function($field, $currentSort, $currentDirection) {
            if ($currentSort !== $field) {
                return '<i class="fas fa-sort sd-sort-icon sd-sort-icon-inactive"></i>';
            }
            return $currentDirection === 'asc' 
                ? '<i class="fas fa-sort-up sd-sort-icon sd-sort-icon-active"></i>'
                : '<i class="fas fa-sort-down sd-sort-icon sd-sort-icon-active"></i>';
        };
        
        // Helper function untuk sort URL
        $sortUrlFn = function($field, $currentSort, $currentDirection) {
            $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
            $params = array_merge(request()->all(), ['sort' => $field, 'direction' => $newDirection]);
            return route('sidongan.documents.index', $params);
        };
    @endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-documents-index.css') }}">


<div class="sd-page u-px-6">
    {{-- Header Section --}}
    <div class="sd-index-header animate-slide-in">
        <div>
            <h1 class="u-h2-slate">Daftar Surat</h1>
            <p class="u-text-muted-lead">Kelola semua dokumen surat masuk dan keluar</p>
        </div>
        @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
        <a href="{{ route('sidongan.documents.create') }}" class="sd-btn-create btn-action">
            <i class="fas fa-plus"></i>
            <span>Buat Surat Baru</span>
        </a>
        @endif
    </div>

    {{-- Stats Cards --}}

    
    <div class="stats-grid">
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Total Surat',
            'value' => $totalDocuments ?? 0,
            'icon' => 'fa-envelope',
            'color' => 'blue'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Selesai',
            'value' => $statSelesai ?? 0,
            'icon' => 'fa-check-circle',
            'color' => 'green'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Berjalan',
            'value' => $statBerjalan ?? 0,
            'icon' => 'fa-spinner fa-spin',
            'color' => 'orange'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Menunggu Disposisi',
            'value' => $statMenungguDisposisi ?? 0,
            'icon' => 'fa-clock',
            'color' => 'yellow'
        ])
    </div>

    {{-- Filter Section --}}
    <div class="sd-filter-card animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.documents.index') }}" data-base-url="{{ route('sidongan.documents.index') }}">
            
            {{-- Row 1: Search, Per Page, Status --}}
            <div class="sd-filter-row-1">
                <div>
                    <label class="u-label-slate">Cari Dokumen</label>
                    <div class="u-relative">
                        <i class="fas fa-search sd-search-icon"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Ketik untuk mencari berdasarkan judul atau nomor..." class="sd-filter-input">
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
            <div class="sd-filter-row-2">
                
                <div>
                    <label class="u-label-slate">Filter Cepat</label>
                    <div class="sd-quick-filters">
                        <button type="button" data-filter-status="menunggu_disposisi" class="filter-btn sd-filter-btn-dynamic {{ request('status') == 'menunggu_disposisi' ? 'sd-filter-active-menunggu' : 'sd-filter-inactive' }}">
                            <i class="fas fa-hourglass-half"></i>
                            Menunggu Disposisi
                        </button>
                        <button type="button" data-filter-status="berjalan" class="filter-btn sd-filter-btn-dynamic {{ request('status') == 'berjalan' ? 'sd-filter-active-berjalan' : 'sd-filter-inactive' }}">
                            <i class="fas fa-spinner"></i>
                            Berjalan
                        </button>
                        <button type="button" data-filter-status="selesai" class="filter-btn sd-filter-btn-dynamic {{ request('status') == 'selesai' ? 'sd-filter-active-selesai' : 'sd-filter-inactive' }}">
                            <i class="fas fa-check-circle"></i>
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
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="sd-filter-date">
                </div>

                <div>
                    <label class="u-label-slate">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="sd-filter-date">
                </div>

                <div class="sd-filter-reset-wrap">
                    <button type="button" data-action="reset-filters" class="sd-btn-reset-filter">
                        <i class="fas fa-undo u-mr-1"></i>
                        Reset
                    </button>
                </div>
            </div>

            @if($currentSort)
            <div class="sd-sort-indicator">
                <div class="sd-sort-info">
                    <i class="fas fa-sort-amount-down"></i>
                    <span class="sd-sort-info strong">Diurutkan:</span>
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
                <button type="button" data-action="reset-sorting" class="sd-btn-clear-sort">
                    <i class="fas fa-times" style="margin-right: 0.25rem;"></i>
                    Hapus Urutan
                </button>
            </div>
            @endif
        </form>
    </div>

    {{-- Bulk Action Bar --}}
    <div id="bulkActionBar" class="sd-bulk-bar sd-bulk-hidden">
        <div class="sd-bulk-bar-inner">
            <div class="sd-bulk-bar-info">
                <i class="fas fa-check-circle"></i>
                <span id="bulkSelectedCount">0</span> surat dipilih
            </div>
            <div class="sd-bulk-bar-actions">
                @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
                <button type="button" data-action="bulk-archive" class="sd-bulk-btn sd-bulk-btn-archive" title="Pindahkan surat yang dipilih ke Arsip">
                    <i class="fas fa-archive"></i>
                    Arsipkan
                </button>
                <button type="button" data-action="bulk-delete" class="sd-bulk-btn sd-bulk-btn-delete" title="Hapus surat yang dipilih secara permanen">
                    <i class="fas fa-trash-alt"></i>
                    Hapus
                </button>
                @endif
                <button type="button" data-action="bulk-cancel" class="sd-bulk-btn sd-bulk-btn-cancel" title="Batalkan pemilihan">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="animate-slide-in u-a78">
        @if(isset($documents) && $documents->count() > 0)
        <div class="sd-table-wrap">
            <table class="sd-doc-table" id="documentsTable">
                <thead>
                    <tr>
                        <th class="sd-th-checkbox">
                            <label class="sd-checkbox-label">
                                <input type="checkbox" id="selectAll" class="sd-checkbox-input">
                                <span class="sd-checkbox-box"></span>
                            </label>
                        </th>
                        <th
                            data-sort-url="{{ $sortUrlFn('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                NO {!! $sortIconFn('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain" 
                            data-sort-url="{{ $sortUrlFn('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                NO. AGENDA {!! $sortIconFn('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain"
                            data-sort-url="{{ $sortUrlFn('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                PERIHAL {!! $sortIconFn('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain"
                            data-sort-url="{{ $sortUrlFn('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                NO. SURAT {!! $sortIconFn('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="u-th-plain"
                            data-sort-url="{{ $sortUrlFn('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                TANGGAL {!! $sortIconFn('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="sd-th-static">DISPOSISI</th>
                        <th class="u-th-plain"
                            data-sort-url="{{ $sortUrlFn('status', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                            <span class="u-a14">
                                STATUS {!! $sortIconFn('status', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                            </span>
                        </th>
                        <th class="sd-th-static" style="min-width: 220px;">AKSI TERAKHIR</th>
                        <th class="sd-th-actions">AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($documents as $index => $doc)
                    <tr class="sd-row">
                        <td class="sd-td-checkbox">
                            <label class="sd-checkbox-label">
                                <input type="checkbox" name="doc_ids[]" value="{{ $doc->id }}" class="sd-checkbox-input doc-checkbox">
                                <span class="sd-checkbox-box"></span>
                            </label>
                        </td>
                        <td data-label="No" class="sd-cell-no">
                            {{ $documents->firstItem() + $index }}
                        </td>
                        <td data-label="No. Agenda" class="sd-cell-agenda">
                            {{ $doc->agenda_number ?? '-' }}
                        </td>
                        
                        <td data-label="Perihal" class="sd-cell-stack u-p-4">
                            <div class="sd-cell-subject">{{ Str::limit($doc->subject ?? $doc->title, 60) }}</div>
                            @if($doc->sender)
                            <div class="sd-cell-sender">{{ $doc->sender }}</div>
                            @endif
                        </td>
                        
                        <td class="sd-cell-text" data-label="No. Surat">
                            {{ $doc->document_number ?? '-' }}
                        </td>
                        
                        <td class="sd-cell-text" data-label="Tanggal">
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
                                <span class="sd-dispo-chip">
                                    {{ $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                                </span>
                            @endforeach
                            @else
                                <span class="sd-dispo-none">Belum</span>
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
                                <span class="sd-badge sd-badge-menunggu">
                                    <i class="fas fa-hourglass-half u-text-xs"></i>
                                    Menunggu Disposisi
                                </span>

                            @elseif($doc->status === 'berjalan')
                                <div class="sd-status-col">
                                    <span class="sd-badge sd-badge-berjalan sd-badge-fit">
                                        <i class="fas fa-spinner fa-spin u-text-xs"></i>
                                        Berjalan
                                    </span>
                                    
                                    @if($hasRejected)
                                        @foreach($rejectedReports as $rejected)
                                            <div class="sd-report-card sd-report-rejected">
                                                <div class="sd-report-head">
                                                    <span class="sd-report-tag sd-report-tag-red">
                                                        <i class="fas fa-times-circle u-text-xxs"></i>
                                                        Laporan Ditolak
                                                    </span>
                                                </div>
                                                <div class="sd-report-user-red">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $rejected->creator->name ?? 'Unknown' }}
                                                </div>
                                                @if($rejected->catatan_verifikasi)
                                                    <div class="sd-report-note-red">
                                                        "{{ Str::limit($rejected->catatan_verifikasi, 60) }}"
                                                    </div>
                                                @endif
                                                <div class="sd-report-time-sm">
                                                    {{ $rejected->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if($hasApproved)
                                        @foreach($approvedReports as $approved)
                                            <div class="sd-report-card sd-report-approved">
                                                <div class="sd-report-head">
                                                    <span class="sd-report-tag sd-report-tag-green">
                                                        <i class="fas fa-check-circle u-text-xxs"></i>
                                                        Laporan Disetujui
                                                    </span>
                                                </div>
                                                <div class="sd-report-user-green">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $approved->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div class="sd-report-time-sm">
                                                    {{ $approved->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if($hasPending)
                                        @foreach($pendingReports as $pending)
                                            <div class="sd-report-card sd-report-pending">
                                                <div class="sd-report-head">
                                                    <span class="sd-report-tag sd-report-tag-blue">
                                                        <i class="fas fa-clock u-text-xxs"></i>
                                                        Menunggu Verifikasi
                                                    </span>
                                                </div>
                                                <div class="sd-report-user-blue">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $pending->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div class="sd-report-time-sm">
                                                    {{ $pending->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                    
                                    @if(!$hasReport)
                                        <div class="sd-report-card sd-report-none">
                                            <div class="sd-no-report">
                                                <i class="fas fa-file-circle-xmark u-text-xs"></i>
                                                Belum ada laporan
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @elseif($doc->status === 'menunggu_verifikasi')
                                <div class="sd-status-col-sm">
                                    <span class="sd-badge sd-badge-verifikasi sd-badge-fit">
                                        <i class="fas fa-clock u-text-xs"></i>
                                        Menunggu Verifikasi
                                    </span>
                                    @if($allReported)
                                        <div class="sd-verify-note">
                                            <div class="sd-verify-note-text">
                                                <i class="fas fa-clock u-text-xs"></i>
                                                Laporan sedang menunggu verifikasi ketua
                                            </div>
                                        </div>
                                    @endif
                                </div>

                            @elseif($doc->status === 'selesai')
                                <div class="sd-status-col-sm">
                                    <span class="sd-badge sd-badge-selesai sd-badge-fit">
                                        <i class="fas fa-check-circle u-text-xs"></i>
                                        Selesai
                                    </span>
                                    @if($hasApproved)
                                        @foreach($approvedReports as $approved)
                                            <div class="sd-done-note">
                                                <div class="sd-done-note-text">
                                                    <i class="fas fa-user u-text-xs"></i>
                                                    {{ $approved->creator->name ?? 'Unknown' }}
                                                </div>
                                                <div class="sd-report-time-xs">
                                                    {{ $approved->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            @elseif($doc->status === 'diarsipkan')
                                <span class="sd-badge sd-badge-arsip">
                                    <i class="fas fa-archive u-text-xs"></i>
                                    Diarsipkan
                                </span>

                            @else
                                <span class="sd-badge sd-badge-default">
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
                     class="sd-action-avatar">
            @else
                <div class="sd-action-avatar-placeholder">
                    {{ strtoupper(substr($latestAction['user']->name, 0, 1)) }}
                </div>
            @endif
            
            <div class="u-flex-1-min">
                <span class="sd-action-badge" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }};">
                    <i class="fas {{ $color['icon'] }} u-text-xxs"></i>
                    {{ $latestAction['action'] }}
                </span>
                <div class="sd-action-user">
                    {{ $latestAction['user']->name }}
                </div>
                <div class="sd-action-time">
                    {{ $latestAction['time']->locale('id')->translatedFormat('d F Y, H.i') }}
                </div>
            </div>
        </div>
    @else
        <span class="sd-no-action">-</span>
    @endif
</td>
                        
                        <td data-label="Aksi" class="sd-cell-stack sd-action-cell">
                            <div class="sd-actions sd-actions-row">
                                {{-- Lihat Detail (semua role) --}}
                                <a href="{{ route('sidongan.documents.show', $doc) }}"
                                class="sd-icon-btn sd-icon-view"
                                title="Lihat Detail">
                                    <i class="fas fa-eye u-text-sm"></i>
                                </a>

                                {{-- Disposisi (Ketua PKK, surat menunggu disposisi) --}}
                                @if($currentUser && $currentUser->hasSidonganRole('ketua') && $doc->status === 'menunggu_disposisi')
                                    <a href="{{ route('sidongan.disposisi') }}?doc_id={{ $doc->id }}"
                                    class="sd-icon-btn sd-icon-disposisi"
                                    title="Disposisi Surat"
                                    style="background: #fff7ed; color: #ea580c;">
                                        <i class="fas fa-share-alt u-text-sm"></i>
                                    </a>
                                @endif

                                {{-- Edit & Hapus (Sekretaris, surat menunggu disposisi) --}}
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

                                {{-- Arsipkan (Sekretaris, jika memenuhi syarat) --}}
                                @php
                                    $canArchive = false;
                                    $dispoData = is_string($doc->disposisi_data) 
                                        ? json_decode($doc->disposisi_data, true) 
                                        : $doc->disposisi_data;

                                    $isArsipDisposition = false;
                                    if (isset($dispoData['action'])) {
                                        $actionLower = strtolower($dispoData['action']);
                                        if (strpos($actionLower, 'arsip') !== false) {
                                            $isArsipDisposition = true;
                                        }
                                    }

                                    if ($isArsipDisposition && $doc->status === 'berjalan') {
                                        $canArchive = true;
                                    } elseif ($doc->status === 'selesai') {
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
        <div class="sd-pagination-footer">
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
        <div class="sd-pagination-footer-alt">
            <div class="u-text-sm-muted-2">
                Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
            </div>
            <div class="u-text-xs-muted">
                <i class="fas fa-info-circle"></i> Semua surat ditampilkan dalam satu halaman
            </div>
        </div>
        @endif
        @else
            <div class="sd-empty-state">
                <div class="sd-empty-icon-box">
                    <i class="fas fa-inbox sd-empty-icon"></i>
                </div>
                <h3 class="sd-empty-title">Belum Ada Dokumen</h3>
                <p class="sd-empty-desc">Belum ada dokumen surat yang ditemukan.</p>
                @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
                <a href="{{ route('sidongan.documents.create') }}" class="btn-action sd-empty-btn">
                    <i class="fas fa-plus"></i>
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
