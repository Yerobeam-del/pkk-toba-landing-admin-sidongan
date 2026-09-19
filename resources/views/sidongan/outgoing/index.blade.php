{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Surat Keluar - SIDONGAN')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-outgoing.css') }}">
<link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-documents-index.css') }}">
@endpush

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    $isKetua = $currentUser && $currentUser->isSidonganKetua();

    $statusConfig = [
        'draft' => ['class' => 'so-status-draft', 'label' => 'Draft', 'icon' => 'fa-pen'],
        'diajukan' => ['class' => 'so-status-diajukan', 'label' => 'Diajukan', 'icon' => 'fa-paper-plane'],
        'perlu_revisi' => ['class' => 'so-status-perlu_revisi', 'label' => 'Perlu Revisi', 'icon' => 'fa-rotate-left'],
        'disetujui' => ['class' => 'so-status-disetujui', 'label' => 'Disetujui', 'icon' => 'fa-check'],
        'dikirim' => ['class' => 'so-status-dikirim', 'label' => 'Dikirim', 'icon' => 'fa-envelope-open-text'],
        'diarsipkan' => ['class' => 'so-status-diarsipkan', 'label' => 'Diarsipkan', 'icon' => 'fa-archive'],
    ];
@endphp
<div class="sd-page u-px-6">
    {{-- Header Section --}}
    <div class="sd-index-header animate-slide-in">
        <div>
            <h1 class="u-h2-slate">Surat Keluar</h1>
            <p class="u-text-muted-lead">Surat tindak lanjut yang dibuat dari Surat Masuk</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Total Surat Keluar',
            'value' => $statTotal,
            'icon' => 'fa-file-signature',
            'color' => 'blue'
        ])

        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Menunggu Proses',
            'value' => $statMenunggu,
            'icon' => 'fa-hourglass-half',
            'color' => 'yellow'
        ])

        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Disetujui / Dikirim',
            'value' => $statDisetujui,
            'icon' => 'fa-check-circle',
            'color' => 'green'
        ])

        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Diarsipkan',
            'value' => $statDiarsipkan,
            'icon' => 'fa-archive',
            'color' => 'purple'
        ])
    </div>

    {{-- Filter Section --}}
    <div class="sd-filter-card animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.outgoing.index') }}" data-base-url="{{ route('sidongan.outgoing.index') }}">

            {{-- Row 1: Search, Per Page, Status --}}
            <div class="sd-filter-row-1">
                <div>
                    <label class="u-label-slate">Cari Surat Keluar</label>
                    <div class="u-relative">
                        <i class="fas fa-search sd-search-icon"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Ketik untuk mencari perihal, penerima, atau nomor..." class="sd-filter-input">
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Tampilkan</label>
                    <div class="u-relative">
                        <select class="sd-filter-select" name="per_page" id="perPageSelect">
                            @foreach($allowedPerPages as $value)
                                <option value="{{ $value }}" {{ $currentPerPage == $value ? 'selected' : '' }}>
                                    {{ $value }} surat
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="u-label-slate">Status</label>
                    <div class="u-relative">
                        <select class="sd-filter-select" name="status" id="statusSelect">
                            <option value="">Semua Status</option>
                            @foreach($statusConfig as $key => $cfg)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Row 2: Quick Filters, Date Filters, Reset --}}
            <div class="sd-filter-row-2">
                <div>
                    <label class="u-label-slate">Filter Cepat</label>
                    <div class="sd-quick-filters">
                        <button type="button" data-filter-status="draft" class="filter-btn sd-filter-btn-dynamic {{ request('status') == 'draft' ? 'sd-filter-active-menunggu' : 'sd-filter-inactive' }}">
                            <i class="fas fa-pen"></i>
                            Draft
                        </button>
                        <button type="button" data-filter-status="diajukan" class="filter-btn sd-filter-btn-dynamic {{ request('status') == 'diajukan' ? 'sd-filter-active-berjalan' : 'sd-filter-inactive' }}">
                            <i class="fas fa-paper-plane"></i>
                            Diajukan
                        </button>
                        <button type="button" data-filter-status="disetujui" class="filter-btn sd-filter-btn-dynamic {{ request('status') == 'disetujui' ? 'sd-filter-active-selesai' : 'sd-filter-inactive' }}">
                            <i class="fas fa-check-circle"></i>
                            Disetujui
                        </button>
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
        </form>
    </div>

    {{-- Table --}}
    @if($letters->count() > 0)
    <div class="animate-slide-in u-a78">
        <div class="sd-table-wrap">
            <table class="sd-doc-table">
                <thead>
                    <tr>
                        <th class="sd-th-static">No. Surat</th>
                        <th class="sd-th-static">Perihal</th>
                        <th class="sd-th-static">Penerima</th>
                        <th class="sd-th-static">Referensi</th>
                        <th class="sd-th-static">Status</th>
                        <th class="sd-th-actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($letters as $letter)
                    @php
                        $status = $statusConfig[$letter->status] ?? ['class' => 'so-status-draft', 'label' => $letter->status, 'icon' => 'fa-tag'];
                    @endphp
                    <tr class="sd-row">
                        <td class="sd-cell-text" data-label="No. Surat">
                            {{ $letter->outgoing_number ?: 'DRAFT' }}
                        </td>
                        <td data-label="Perihal" class="sd-cell-stack u-p-4">
                            <div class="sd-cell-subject">{{ Str::limit($letter->subject, 60) }}</div>
                            <div class="sd-cell-sender">
                                {{ optional($letter->letter_date)->locale('id')->translatedFormat('d F Y') ?? '-' }}
                                @if($letter->revision_note && $letter->status === 'perlu_revisi')
                                    &middot; <i class="fas fa-rotate-left"></i> Perlu revisi
                                @endif
                            </div>
                        </td>
                        <td class="sd-cell-text" data-label="Penerima">
                            {{ $letter->recipient }}
                        </td>
                        <td class="sd-cell-agenda" data-label="Referensi">
                            {{ $letter->incomingDocument->agenda_number ?? '-' }}
                        </td>
                        <td data-label="Status">
                            <span class="so-badge {{ $status['class'] }}">
                                <i class="fas {{ $status['icon'] }} u-text-xxs"></i>
                                {{ $status['label'] }}
                            </span>
                        </td>
                        <td class="sd-th-actions" data-label="Aksi">
                            <div class="so-actions-cell">
                                <a href="{{ route('sidongan.outgoing.word', $letter) }}" class="sd-icon-btn" title="Unduh Word (.docx) untuk revisi">
                                    <i class="fas fa-file-word u-text-sm"></i>
                                </a>
                                <a href="{{ route('sidongan.outgoing.show', $letter) }}" class="sd-icon-btn" title="Lihat Detail">
                                    <i class="fas fa-eye u-text-sm"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($letters->hasPages())
        <div class="so-pagination-footer">
            <div class="so-pagination-info">
                Menampilkan <strong>{{ $letters->firstItem() }}</strong> - <strong>{{ $letters->lastItem() }}</strong> dari <strong>{{ $letters->total() }}</strong> surat keluar
            </div>
            <div class="so-pagination">
                @if($letters->onFirstPage())
                    <span class="so-page-btn" style="opacity:.5"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $letters->previousPageUrl() }}" class="so-page-btn"><i class="fas fa-chevron-left"></i></a>
                @endif

                @foreach($letters->getUrlRange(1, $letters->lastPage()) as $page => $url)
                    @if($page == $letters->currentPage())
                        <span class="so-page-btn so-page-btn-active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="so-page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($letters->hasMorePages())
                    <a href="{{ $letters->nextPageUrl() }}" class="so-page-btn"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="so-page-btn" style="opacity:.5"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="sd-empty-state animate-slide-in">
        <div class="sd-empty-icon-box">
            <i class="fas fa-file-signature sd-empty-icon"></i>
        </div>
        <h3 class="sd-empty-title">{{ request('search') || request('status') ? 'Tidak Ada Hasil' : 'Belum Ada Surat Keluar' }}</h3>
        <p class="sd-empty-desc">
            @if(request('search') || request('status'))
                Tidak ada surat keluar yang cocok dengan filter. Coba ubah kata kunci atau reset filter.
            @else
                Surat Keluar dibuat sebagai tindak lanjut dari Surat Masuk.<br>Buka detail Surat Masuk, lalu klik "Buat Surat Keluar".
            @endif
        </p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/sidongan/js/documents-index.js') }}"></script>
@endpush
{{-- Dikembangkan oleh Institut Teknologi Del --}}
