@extends('sidongan.layouts.app')
@section('title', 'Arsip Surat - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    
    function sortIcon($field, $currentSort, $currentDirection) {
        if ($currentSort !== $field) {
            return '<i class="fas fa-sort" style="color: rgba(255,255,255,0.6); margin-left: 0.5rem;"></i>';
        }
        return $currentDirection === 'asc' 
            ? '<i class="fas fa-sort-up" style="color: white; margin-left: 0.5rem;"></i>'
            : '<i class="fas fa-sort-down" style="color: white; margin-left: 0.5rem;"></i>';
    }
    
    function sortUrl($field, $currentSort, $currentDirection) {
        $newDirection = ($currentSort === $field && $currentDirection === 'asc') ? 'desc' : 'asc';
        $params = array_merge(request()->all(), ['sort' => $field, 'direction' => $newDirection]);
        return route('sidongan.arsip', $params);
    }
    
    $currentSort = request('sort');
    $currentDirection = request('direction', 'desc');
@endphp

<style>
    .arsip-row {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 3px solid transparent;
        position: relative;
    }
    .arsip-row::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #8b5cf6, #7c3aed);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .arsip-row:hover {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%) !important;
        border-left-color: #8b5cf6;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
    }
    .arsip-row:hover::before {
        opacity: 1;
    }
    .stats-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-slide-in {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    .btn-action {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    .filter-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>

<div style="padding: 0 1.5rem;">
    {{-- Header Section --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="animate-slide-in">
        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">Arsip Surat</h1>
            <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">Lihat dan unduh dokumen yang telah selesai diproses</p>
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

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        {{-- Total Arsip - UNGU --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4rem; height: 4rem; background: rgba(255,255,255,0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-archive" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <p style="font-size: 0.875rem; opacity: 0.95; margin: 0 0 0.25rem 0; font-weight: 500;">Total Arsip</p>
                    <p style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">{{ $totalArsip ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        {{-- Arsip Bulan Ini - HIJAU --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #22c55e, #16a34a); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4rem; height: 4rem; background: rgba(255,255,255,0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-calendar-check" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <p style="font-size: 0.875rem; opacity: 0.95; margin: 0 0 0.25rem 0; font-weight: 500;">Arsip Bulan Ini</p>
                    <p style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">{{ $arsipBulanIni ?? 0 }}</p>
                    <p style="font-size: 0.75rem; opacity: 0.9; margin: 0.25rem 0 0 0;">{{ $statsPeriodText }}</p>
                </div>
            </div>
        </div>
        
        {{-- Arsip Tahun Ini - ORANGE --}}
        <div class="stats-card animate-slide-in" style="background: linear-gradient(135deg, #f97316, #ea580c); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4rem; height: 4rem; background: rgba(255,255,255,0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-calendar-alt" style="font-size: 1.75rem;"></i>
                </div>
                <div>
                    <p style="font-size: 0.875rem; opacity: 0.95; margin: 0 0 0.25rem 0; font-weight: 500;">Arsip Tahun Ini</p>
                    <p style="font-size: 2.5rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">{{ $arsipTahunIni ?? 0 }}</p>
                    <p style="font-size: 0.75rem; opacity: 0.9; margin: 0.25rem 0 0 0;">{{ $filterYear ?? now()->format('Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; padding: 1.25rem; margin-bottom: 1.5rem;" class="animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.arsip') }}">
            
            {{-- Row 1: Search, Tampilkan, Tahun --}}
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Cari Arsip</label>
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari berdasarkan judul atau nomor..." style="width: 100%; padding: 0.625rem 1rem 0.625rem 2.5rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s;" onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.1)'" onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Tampilkan</label>
                    <div style="position: relative;">
                        <select name="per_page" id="perPageSelect" style="width: 100%; padding: 0.625rem 2.5rem 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; cursor: pointer; appearance: none;" onchange="document.getElementById('filterForm').submit()">
                            @foreach([10, 25, 50, 100] as $value)
                                <option value="{{ $value }}" {{ (request('per_page', 10) == $value) ? 'selected' : '' }}>
                                    {{ $value }} arsip
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.75rem;"></i>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Tahun</label>
                    <div style="position: relative;">
                        <select name="year" id="yearSelect" style="width: 100%; padding: 0.625rem 2.5rem 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; cursor: pointer; appearance: none;" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua Tahun</option>
                            @for($year = date('Y'); $year >= date('Y')-5; $year--)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.75rem;"></i>
                    </div>
                </div>
            </div>

            {{-- Row 2: Bulan, Tanggal, Reset --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 1rem; align-items: end; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Bulan</label>
                    <div style="position: relative;">
                        <select name="filter_month" style="width: 100%; padding: 0.625rem 2.5rem 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white; cursor: pointer; appearance: none;" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Semua Bulan</option>
                            @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $num => $name)
                                <option value="{{ $num }}" {{ request('filter_month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.75rem;"></i>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white;" onchange="document.getElementById('filterForm').submit()">
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 0.625rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; background: white;" onchange="document.getElementById('filterForm').submit()">
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: end;">
                    <button type="button" onclick="resetFilters()" style="padding: 0.625rem 1rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s; white-space: nowrap;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        <i class="fas fa-undo" style="margin-right: 0.35rem;"></i>
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
                <button type="button" onclick="resetSorting()" style="padding: 0.35rem 0.75rem; background: #8b5cf6; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">
                    <i class="fas fa-times" style="margin-right: 0.25rem;"></i>
                    Hapus Urutan
                </button>
            </div>
            @endif
        </form>
    </div>

    {{-- Tabel Arsip --}}
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden;" class="animate-slide-in">
        @if(isset($documents) && $documents->count() > 0)
        <div style="overflow-x: auto;">
<table style="width: 100%; border-collapse: collapse;">
    <thead style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
        <tr>
            <th style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; width: 60px; cursor: pointer; white-space: nowrap;"
                onclick="window.location.href='{{ sortUrl('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}'">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    NO {!! sortIcon('id', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer; white-space: nowrap;" 
                onclick="window.location.href='{{ sortUrl('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}'">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    NO. AGENDA {!! sortIcon('agenda_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer; white-space: nowrap;"
                onclick="window.location.href='{{ sortUrl('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}'">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    PERIHAL {!! sortIcon('subject', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer; white-space: nowrap;"
                onclick="window.location.href='{{ sortUrl('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}'">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    NO. SURAT {!! sortIcon('document_number', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer; white-space: nowrap;"
                onclick="window.location.href='{{ sortUrl('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}'">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    TANGGAL {!! sortIcon('document_date', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer; white-space: nowrap;"
                onclick="window.location.href='{{ sortUrl('disposisi_count', $currentSort ?? 'created_at', $currentDirection ?? 'desc') }}'">
                <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
                    DISPOSISI {!! sortIcon('disposisi_count', $currentSort ?? 'created_at', $currentDirection ?? 'desc') !!}
                </span>
            </th>
            <th style="padding: 1rem; text-align: center; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; white-space: nowrap;">AKSI</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $index => $doc)
        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
            <td style="padding: 1rem; text-align: center; font-weight: 600; color: #64748b; font-size: 0.875rem;">
                {{ $documents->firstItem() + $index }}
            </td>
            <td style="padding: 1rem; font-weight: 600; color: #8b5cf6; font-family: monospace; font-size: 0.875rem;">
                {{ $doc->agenda_number ?? '-' }}
            </td>
            
            <td style="padding: 1rem;">
                <div style="font-weight: 600; color: #0f172a; margin-bottom: 0.25rem;">{{ Str::limit($doc->subject ?? $doc->title, 60) }}</div>
                @if($doc->sender)
                <div style="font-size: 0.75rem; color: #64748b;">{{ $doc->sender }}</div>
                @endif
            </td>
            
            <td style="padding: 1rem; color: #475569; font-size: 0.875rem;">
                {{ $doc->document_number ?? '-' }}
            </td>
            
            <td style="padding: 1rem; color: #475569; font-size: 0.875rem;">
                {{ $doc->document_date ? $doc->document_date->locale('id')->translatedFormat('d F Y') : '-' }}
            </td>
            
            <td style="padding: 1rem;">
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
            
            <td style="padding: 1rem; white-space: nowrap;">
                <div style="display: flex; gap: 0.5rem; justify-content: center;">
                    <a href="{{ route('sidongan.documents.show', $doc) }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #dbeafe; color: #2563eb; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#bfdbfe'" 
                    onmouseout="this.style.background='#dbeafe'"
                    title="Lihat Detail">
                        <i class="fas fa-eye" style="font-size: 0.875rem;"></i>
                    </a>
                    
                    <a href="{{ route('sidongan.documents.disposisi-print', $doc) }}" 
                    target="_blank"
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem; background: #d1fae5; color: #059669; border-radius: 0.375rem; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#a7f3d0'" 
                    onmouseout="this.style.background='#d1fae5'"
                    title="Cetak Lembar Disposisi">
                        <i class="fas fa-print" style="font-size: 0.875rem;"></i>
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
            <div style="font-size: 0.875rem; color: #64748b;">
                Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> arsip
            </div>
            
            <div style="display: flex; gap: 0.35rem; align-items: center;">
                @if($documents->onFirstPage())
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f1f5f9; color: #94a3b8; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #8b5cf6; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">
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
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                        1
                    </a>
                    @if($startPage > 2)
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; color: #94a3b8; font-size: 0.875rem;">...</span>
                    @endif
                @endif
                
                @for($i = $startPage; $i <= $endPage; $i++)
                    @if($i == $currentPage)
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #8b5cf6; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">
                            {{ $i }}
                        </span>
                    @else
                        <a href="{{ $documents->url($i) }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                            {{ $i }}
                        </a>
                    @endif
                @endfor
                
                @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; color: #94a3b8; font-size: 0.875rem;">...</span>
                    @endif
                    <a href="{{ $documents->url($lastPage) }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                        {{ $lastPage }}
                    </a>
                @endif
                
                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" 
                    style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #8b5cf6; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                    onmouseover="this.style.background='#7c3aed'" onmouseout="this.style.background='#8b5cf6'">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f1f5f9; color: #94a3b8; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                @endif
            </div>
        </div>
        @else
        <div style="padding: 1.25rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; background: #f8fafc;">
            <div style="font-size: 0.875rem; color: #64748b;">
                Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> arsip
            </div>
            <div style="font-size: 0.875rem; color: #94a3b8;">
                <i class="fas fa-info-circle"></i> Semua arsip ditampilkan dalam satu halaman
            </div>
        </div>
        @endif
        @else
        <div style="padding: 4rem 2rem; text-align: center;" class="animate-slide-in">
            <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #f3e8ff, #e9d5ff); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15); animation: float 3s ease-in-out infinite;">
                <i class="fas fa-archive" style="color: #8b5cf6; font-size: 3rem;"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">Tidak Ada Arsip</h3>
            <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6; max-width: 400px; margin-left: auto; margin-right: auto;">
                Dokumen yang telah selesai diproses akan muncul di sini.
            </p>
        </div>
        @endif
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    searchInput?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });

    function applyQuickFilter(type) {
        const form = document.getElementById('filterForm');
        
        if (type === 'this_month') {
            const monthSelect = form.querySelector('[name="filter_month"]');
            const currentMonth = new Date().getMonth() + 1;
            const monthStr = currentMonth.toString().padStart(2, '0');
            if (monthSelect) monthSelect.value = monthStr;
        } else if (type === 'this_year') {
            const yearSelect = form.querySelector('[name="year"]');
            const currentYear = new Date().getFullYear();
            if (yearSelect) yearSelect.value = currentYear;
        } else if (type === 'recent') {
            const dateFrom = form.querySelector('[name="date_from"]');
            const dateTo = form.querySelector('[name="date_to"]');
            const today = new Date();
            const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
            
            if (dateFrom) dateFrom.value = lastMonth.toISOString().slice(0, 10);
            if (dateTo) dateTo.value = today.toISOString().slice(0, 10);
        }
        
        form.submit();
    }

    function resetFilters() {
        window.location.href = '{{ route("sidongan.arsip") }}';
    }

    function resetSorting() {
        const url = new URL(window.location.href);
        url.searchParams.delete('sort');
        url.searchParams.delete('direction');
        window.location.href = url.toString();
    }
</script>
@endsection