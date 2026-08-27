{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Arsip Surat - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    
    $arsipSortIconFn = function($field, $currentSort, $currentDirection) {
        if ($currentSort !== $field) {
            return '<i class="fas fa-sort" style="color: rgba(255,255,255,0.6); margin-left: 0.5rem;"></i>';
        }
        return $currentDirection === 'asc' 
            ? '<i class="fas fa-sort-up" style="color: white; margin-left: 0.5rem;"></i>'
            : '<i class="fas fa-sort-down" style="color: white; margin-left: 0.5rem;"></i>';
    };
    
    $arsipSortUrlFn = function($field, $currentSort, $currentDirection) {
        $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
        $params = array_merge(request()->all(), ['sort' => $field, 'direction' => $newDirection]);
        return route('sidongan.arsip', $params);
    };
    
    $currentSort = request('sort');
    $currentDirection = request('direction', 'desc');
@endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-arsip-index.css') }}">


<div class="sd-page u-px-6">
    {{-- Header Section --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="animate-slide-in">
        <div>
            <h1 class="u-h2-slate">Arsip Surat</h1>
            <p class="u-text-muted-lead">Lihat dan unduh dokumen yang telah selesai diproses</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    @php
        $filterYear = request('year');
        $filterMonth = request('filter_month');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');
        
        // Query dasar untuk stats
        $arsipQuery = \App\Models\Document::where('status', 'diarsipkan');
        
        // Filter tahun
        if ($filterYear) {
            $arsipQuery->whereYear('created_at', $filterYear);
        }
        
        // Filter bulan
        if ($filterMonth) {
            $arsipQuery->whereMonth('created_at', $filterMonth);
        }
        
        // Filter tanggal dari
        if ($dateFrom) {
            $arsipQuery->whereDate('created_at', '>=', $dateFrom);
        }
        
        // Filter tanggal sampai
        if ($dateTo) {
            $arsipQuery->whereDate('created_at', '<=', $dateTo);
        }
        
        $totalArsip = (clone $arsipQuery)->count();
        
        // Format nama bulan
        $monthNames = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
        // Tentukan teks untuk stats card
        $statsPeriodText = '';
        if ($dateFrom && $dateTo) {
            // Format tanggal Indonesia
            $dateFromObj = \Carbon\Carbon::parse($dateFrom);
            $dateToObj = \Carbon\Carbon::parse($dateTo);
            $statsPeriodText = $dateFromObj->locale('id')->translatedFormat('d F') . ' - ' . $dateToObj->locale('id')->translatedFormat('d F Y');
        } else {
            $displayMonth = $filterMonth ?? now()->month;
            $displayYear = $filterYear ?? now()->year;
            $monthStr = is_numeric($displayMonth) ? str_pad($displayMonth, 2, '0', STR_PAD_LEFT) : $displayMonth;
            $monthName = $monthNames[$monthStr] ?? now()->locale('id')->translatedFormat('F');
            $statsPeriodText = $monthName . ' ' . $displayYear;
        }
        
        // Arsip Bulan Ini (mengikuti filter)
        $arsipBulanIniQuery = \App\Models\Document::where('status', 'diarsipkan');
        if ($filterMonth) {
            $arsipBulanIniQuery->whereMonth('created_at', $filterMonth);
        } else {
            $arsipBulanIniQuery->whereMonth('created_at', now()->month);
        }
        if ($filterYear) {
            $arsipBulanIniQuery->whereYear('created_at', $filterYear);
        } else {
            $arsipBulanIniQuery->whereYear('created_at', now()->year);
        }
        if ($dateFrom) {
            $arsipBulanIniQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $arsipBulanIniQuery->whereDate('created_at', '<=', $dateTo);
        }
        $arsipBulanIni = $arsipBulanIniQuery->count();
        
        // Arsip Tahun Ini (mengikuti filter)
        $arsipTahunIniQuery = \App\Models\Document::where('status', 'diarsipkan');
        if ($filterYear) {
            $arsipTahunIniQuery->whereYear('created_at', $filterYear);
        } else {
            $arsipTahunIniQuery->whereYear('created_at', now()->year);
        }
        if ($dateFrom) {
            $arsipTahunIniQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $arsipTahunIniQuery->whereDate('created_at', '<=', $dateTo);
        }
        $arsipTahunIni = $arsipTahunIniQuery->count();
    @endphp

    <div class="stats-grid">
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Total Arsip',
            'value' => $totalArsip ?? 0,
            'icon' => 'fa-archive',
            'color' => 'purple'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Arsip Bulan Ini',
            'value' => $arsipBulanIni ?? 0,
            'icon' => 'fa-calendar-check',
            'color' => 'green'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Arsip Tahun Ini',
            'value' => $arsipTahunIni ?? 0,
            'icon' => 'fa-calendar-alt',
            'color' => 'orange'
        ])
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 1.5rem;" class="animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.arsip') }}" data-base-url="{{ route('sidongan.arsip') }}">
            
            {{-- Row 1: Search, Tampilkan, Tahun --}}
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                <div>
                    <label class="u-label-slate">Cari Arsip</label>
                    <div class="u-relative">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari berdasarkan judul atau nomor..." style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;">
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Tampilkan</label>
                    <div class="u-relative">
                        <select class="u-a77" name="per_page" id="perPageSelect">
                            @foreach([10, 25, 50, 100] as $value)
                                <option value="{{ $value }}" {{ (request('per_page', 10) == $value) ? 'selected' : '' }}>
                                    {{ $value }} arsip
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down u-select-chevron-right"></i>
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Tahun</label>
                    <div class="u-relative">
                        <select class="u-a77" name="year" id="yearSelect">
                            <option value="">Semua Tahun</option>
                            @for($year = date('Y'); $year >= date('Y')-5; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        <i class="fas fa-chevron-down u-select-chevron-right"></i>
                    </div>
                </div>
            </div>

            {{-- Row 2: Bulan, Tanggal, Reset --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                <div>
                    <label class="u-label-slate">Bulan</label>
                    <div class="u-relative">
                        <select class="u-a77" name="filter_month">
                            <option value="">Semua Bulan</option>
                            @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                <option value="{{ $num }}" {{ request('filter_month') == $num ? 'selected' : '' }}>{{ $name }}</option>
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
            <div style="margin-top: 1rem; padding: 0.75rem 1rem; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 0.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #6d28d9;">
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
                                'created_at' => 'Tanggal Dibuat',
                            ];
                            $sortLabel = $sortLabels[$currentSort] ?? $currentSort;
                            $directionLabel = $currentDirection === 'asc' ? 'Terlama/Ascending' : 'Terbaru/Descending';
                        @endphp
                        {{ $sortLabel }} ({{ $directionLabel }})
                    </span>
                </div>
                <button type="button" data-action="reset-sorting" style="padding: 0.35rem 0.75rem; background: #8b5cf6; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-times" style="margin-right: 0.25rem;"></i>
                    Hapus Urutan
                </button>
            </div>
            @endif
        </form>
    </div>

    {{-- Tabel Arsip --}}
    <div class="animate-slide-in u-a78">
        @if(isset($documents) && $documents->count() > 0)
        <div class="sd-table-wrap" style="overflow-x: auto;">
<table style="width: 100%; border-collapse: collapse;">
    <thead style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
        <tr>
            <th style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; width: 60px; cursor: pointer; white-space: nowrap;"
                data-sort-url="{{ $arsipSortUrlFn('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                <span class="u-a14">
                    NO {!! $arsipSortIconFn('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th class="u-th-plain" 
                data-sort-url="{{ $arsipSortUrlFn('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                <span class="u-a14">
                    NO. AGENDA {!! $arsipSortIconFn('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th class="u-th-plain"
                data-sort-url="{{ $arsipSortUrlFn('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                <span class="u-a14">
                    PERIHAL {!! $arsipSortIconFn('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th class="u-th-plain"
                data-sort-url="{{ $arsipSortUrlFn('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                <span class="u-a14">
                    NO. SURAT {!! $arsipSortIconFn('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th class="u-th-plain"
                data-sort-url="{{ $arsipSortUrlFn('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                <span class="u-a14">
                    TANGGAL {!! $arsipSortIconFn('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th class="u-th-plain"
                data-sort-url="{{ $arsipSortUrlFn('disposisi_count', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}">
                <span class="u-a14">
                    DISPOSISI {!! $arsipSortIconFn('disposisi_count', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; white-space: nowrap;">AKSI</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $index => $doc)
        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
            <td data-label="No" style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.875rem;">
                {{ $documents->firstItem() + $index }}
            </td>
            <td data-label="No. Agenda" style="padding: 1rem; font-weight: 600; color: #8b5cf6; font-family: monospace; font-size: 0.875rem;">
                {{ $doc->agenda_number ?? '-' }}
            </td>
            
            <td data-label="Perihal" class="sd-cell-stack" style="padding: 1rem;">
                <div style="font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">{{ Str::limit($doc->subject ?? $doc->title, 60) }}</div>
                @if($doc->sender)
                <div style="font-size: 0.75rem; color: #64748b;">{{ $doc->sender }}</div>
                @endif
            </td>
            
            <td data-label="No. Surat" style="padding: 1rem; color: #475569; font-size: 0.875rem;">
                {{ $doc->document_number ?? '-' }}
            </td>
            
            <td data-label="Tanggal" style="padding: 1rem; color: #475569; font-size: 0.875rem;">
                {{ $doc->document_date ? $doc->document_date->locale('id')->translatedFormat('d F Y') : '-' }}
            </td>
            
            <td data-label="Disposisi" class="sd-cell-chips" style="padding: 1rem;">
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
                        <span style="display: inline-block; padding: 0.25rem 0.5rem; background: #f3e8ff; color: #7c3aed; border-radius: 0.25rem; font-size: 0.75rem; margin: 0.125rem;">
                            {{ $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                        </span>
                    @endforeach
                @else
                    <span style="color: #94a3b8; font-size: 0.75rem;">-</span>
                @endif
            </td>
            
            <td data-label="Aksi" class="sd-cell-stack" style="padding: 1rem; white-space: nowrap;">
                <div class="sd-actions" style="display: flex; gap: 0.5rem; justify-content: center;">
                    <a href="{{ route('sidongan.documents.show', $doc) }}"
                    class="sd-icon-btn"
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #dbeafe; color: #2563eb; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s;"
                    title="Lihat Detail">
                        <i class="fas fa-eye u-text-sm"></i>
                    </a>
                    
                    <a href="{{ route('sidongan.documents.disposisi-print', $doc) }}"
                    target="_blank"
                    class="sd-icon-btn"
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #d1fae5; color: #059669; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s;"
                    title="Cetak Lembar Disposisi">
                        <i class="fas fa-print u-text-sm"></i>
                    </a>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
        </div>

        {{-- Pagination --}}
        @if($documents->hasPages())
        <div style="padding: 1.25rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div class="u-text-sm-muted-2">
                Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> arsip
            </div>
            
            <div class="sd-pagination u-a79">
                @if($documents->onFirstPage())
                    <span class="u-a15">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #8b5cf6; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;">
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
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;">
                        1
                    </a>
                    @if($startPage > 2)
                        <span class="u-a16">...</span>
                    @endif
                @endif
                
                @for($i = $startPage; $i <= $endPage; $i++)
                    @if($i == $currentPage)
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #8b5cf6; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $documents->url($i) }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;">
                            {{ $i }}
                        </a>
                    @endif
                @endfor
                
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span class="u-a16">...</span>
                    @endif
                    <a href="{{ $documents->url($lastPage) }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;">
                        {{ $lastPage }}
                    </a>
                @endif
                
                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #8b5cf6; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;">
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
                Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> arsip
            </div>
            <div class="u-text-xs-muted">
                <i class="fas fa-info-circle"></i> Semua arsip ditampilkan dalam satu halaman
            </div>
        </div>
        @endif
        @else
        <div style="padding: 4rem 2rem; text-align: center;" class="animate-slide-in">
            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #f3e8ff, #e9d5ff); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15); animation: float 3s ease-in-out infinite;">
                <i class="fas fa-archive" style="color: #8b5cf6; font-size: 3rem;"></i>
            </div>
            <h3 class="u-h3-slate">Tidak Ada Arsip</h3>
            <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6; max-width: 400px; margin-left: auto; margin-right: auto;">
                Dokumen yang telah selesai diproses akan muncul di sini.
            </p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/sidongan/js/arsip-index.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
