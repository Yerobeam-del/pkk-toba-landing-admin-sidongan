{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Lapor Kegiatan - SIDONGAN')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-lapor-kegiatan-index.css') }}">


<div class="sd-page u-px-6">
    {{-- Header --}}
    <div class="animate-slide-in u-mb-8">
        <h1 class="u-h2-slate">
            Lapor Kegiatan
        </h1>
        <p class="u-text-muted-lead">
            Laporkan kegiatan yang telah dilaksanakan
        </p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Perlu Dilaporkan',
            'value' => $perluDilaporkan ?? 0,
            'icon' => 'fa-tasks',
            'color' => 'blue'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Menunggu Verifikasi',
            'value' => $menungguVerifikasi ?? 0,
            'icon' => 'fa-clock',
            'color' => 'orange'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Disetujui',
            'value' => $disetujui ?? 0,
            'icon' => 'fa-check-circle',
            'color' => 'green'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Ditolak',
            'value' => $ditolak ?? 0,
            'icon' => 'fa-times-circle',
            'color' => 'red'
        ])
    </div>

    {{-- Filter Section --}}
    <div class="lk-filter-card animate-slide-in">
        <form id="filterForm" method="GET">
            <div class="lk-filter-grid">
                <div>
                    <label class="u-label-gray">Cari Laporan</label>
                    <div class="u-relative">
                        <i class="fas fa-search lk-search-icon"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari berdasarkan judul kegiatan..." class="lk-search-input">
                    </div>
                </div>
                
                <div>
                    <label class="u-label-gray">Status</label>
                    <div class="u-relative">
                        <select class="u-label-gray" name="status">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Perlu Dilaporkan</option>
                            <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="u-label-gray">Tampilkan</label>
                    <div class="u-relative">
                        <select class="u-label-gray" name="per_page">
                            @foreach([5, 10, 15, 25, 50] as $value)
                                <option value="{{ $value }}" {{ (request('per_page', 10) == $value) ? 'selected' : '' }}>
                                    {{ $value }} laporan
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- List Container --}}
    <div class="lk-list-container animate-slide-in">
        @if(isset($documents) && $documents->count() > 0)
            <div class="u-p-0">
                @foreach($documents as $doc)
                    @php
                        // Satu surat = SATU laporan: laporan apa pun (dari role tujuan
                        // mana pun) yang sudah ada harus terbaca di sini agar suratnya
                        // tidak tampil sebagai "Perlu Dilaporkan" untuk kedua kalinya.
                        $existingReport = $doc->activityReports->sortBy('created_at')->last() ?? null;
                        
                        $statusConfig = [
                            null => ['bg' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#075985', 'btn' => '#0ea5e9', 'label' => 'Perlu Dilaporkan', 'icon' => 'fa-pen'],
                            'draft' => ['bg' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#075985', 'btn' => '#0ea5e9', 'label' => 'Draft', 'icon' => 'fa-pen'],
                            'menunggu_verifikasi' => ['bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412', 'btn' => '#f97316', 'label' => 'Menunggu Verifikasi', 'icon' => 'fa-clock'],
                            'disetujui' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'btn' => '#22c55e', 'label' => 'Disetujui', 'icon' => 'fa-check-circle'],
                            'ditolak' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b', 'btn' => '#ef4444', 'label' => 'Ditolak', 'icon' => 'fa-times-circle'],
                        ];
                        
                        $theme = $statusConfig[$existingReport->status ?? null] ?? $statusConfig[null];
                    @endphp
                    
                    <div class="laporan-item animate-slide-in lk-item" 
                        style="--lk-bg: {{ $theme['bg'] }}; --lk-border: {{ $theme['btn'] }};">
                        
                        <div class="lk-list-head sd-list-head">
                            <div class="u-flex-1">
                                <div class="u-flex-1">
                                    <span class="lk-agenda-badge" style="color: {{ $theme['text'] }}; border: 1px solid {{ $theme['border'] }};">
                                        {{ $doc->agenda_number }}
                                    </span>
                                    <span class="lk-status-badge" style="background: {{ $theme['btn'] }};">
                                        <i class="fas {{ $theme['icon'] }} lk-status-icon"></i>
                                        {{ $theme['label'] }}
                                    </span>
                                </div>
                                
                                <h4 class="lk-doc-title">
                                    {{ $doc->subject ?? $doc->title }}
                                </h4>
                                
                                <div class="lk-meta-row">
                                    <span class="u-flex-center-gap-2">
                                        <div class="lk-meta-icon-box" style="background: {{ $theme['bg'] }};">
                                            <i class="fas fa-user lk-meta-icon" style="color: {{ $theme['btn'] }};"></i>
                                        </div>
                                        {{ $doc->sender }}
                                    </span>
                                    <span class="u-flex-center-gap-2">
                                        <div class="lk-meta-icon-box" style="background: {{ $theme['bg'] }};">
                                            <i class="fas fa-calendar lk-meta-icon" style="color: {{ $theme['btn'] }};"></i>
                                        </div>
                                        {{ $doc->created_at->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="lk-actions sd-list-actions">
                                @if($existingReport)
                                    @if($existingReport->status === 'ditolak')
                                        {{-- Surat ditolak: siapa pun dari role tujuan disposisi boleh
                                             membuat laporan baru (data lama otomatis dimuat di form).
                                             Revisi di tempat tetap tersedia sebagai opsi kedua. --}}
                                        <a href="{{ route('sidongan.lapor_kegiatan.create', ['document_id' => $doc->id]) }}" 
                                        class="btn-action lk-btn-primary">
                                            <i class="fas fa-plus"></i>
                                            <span>Buat Laporan</span>
                                        </a>
                                        <a href="{{ route('sidongan.lapor_kegiatan.edit', $existingReport->id) }}" 
                                        class="btn-action lk-btn-outline" style="color: #c2410c;">
                                            <i class="fas fa-edit"></i>
                                            <span>Revisi Laporan</span>
                                        </a>
                                    @else
                                        {{-- Tombol Lihat untuk status lainnya --}}
                                        <a href="{{ route('sidongan.lapor_kegiatan.show', $existingReport->id) }}" 
                                        class="btn-action lk-btn-outline" style="color: {{ $theme['text'] }}; border-color: {{ $theme['border'] }};">
                                            <i class="fas fa-eye"></i>
                                            <span>Lihat</span>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('sidongan.lapor_kegiatan.create', ['document_id' => $doc->id]) }}" 
                                    class="btn-action lk-btn-primary">
                                        <i class="fas fa-plus"></i>
                                        <span>Buat Laporan</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                        
                        @if($doc->disposisi_data)
                            @php
                                $dispo = is_string($doc->disposisi_data) ? json_decode($doc->disposisi_data, true) : $doc->disposisi_data;
                            @endphp
                            @if(isset($dispo['action']))
                            <div class="lk-dispo-box" style="background: {{ $theme['bg'] }}; border-left-color: {{ $theme['btn'] }};">
                                <div class="lk-dispo-row">
                                    <div class="lk-dispo-icon-box">
                                        <i class="fas fa-info-circle" style="color: {{ $theme['btn'] }}; font-size: 0.875rem;"></i>
                                    </div>
                                    <div class="u-flex-1">
                                        <p class="lk-dispo-label" style="color: {{ $theme['text'] }};">Instruksi Disposisi</p>
                                        <p class="lk-dispo-text">
                                            <strong>{{ $dispo['action'] }}</strong>
                                            @if(isset($dispo['comment']) && $dispo['comment'])
                                                - "{{ Str::limit($dispo['comment'], 100) }}"
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if(isset($documents) && method_exists($documents, 'hasPages') && $documents->hasPages())
                <div class="u-card-footer">
                    <div class="u-text-sm-muted-2">
                        Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> laporan
                    </div>
                    
                    <div class="sd-pagination u-a79">
                        @if($documents->onFirstPage())
                            <span class="u-a15">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $documents->previousPageUrl() }}" 
                            class="sd-page-btn sd-page-btn-sky">
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
                                <span class="sd-page-btn sd-page-btn-sky">
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
                            class="sd-page-btn sd-page-btn-sky">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="u-a15">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
            @elseif(isset($documents) && count($documents) > 0)
                <div class="u-card-footer">
                    <div class="u-text-sm-muted-2">
                        Menampilkan <strong>1</strong> - <strong>{{ count($documents) }}</strong> dari <strong>{{ count($documents) }}</strong> laporan
                    </div>
                    <div class="u-text-xs-muted">
                        <i class="fas fa-info-circle"></i> Semua laporan ditampilkan dalam satu halaman
                    </div>
                </div>
            @endif
        @else
            <div class="animate-slide-in u-a117">
                <div class="lk-empty-icon-box">
                    <i class="fas fa-inbox lk-empty-icon"></i>
                </div>
                <h4 class="u-h3-slate">Tidak Ada Laporan</h4>
                <p class="u-text-muted-lead">
                    Belum ada surat yang perlu dilaporkan atau sesuai dengan filter.
                </p>
            </div>
        @endif
    </div>
</div>

    <script src="{{ asset('assets/sidongan/js/sidongan-lapor-kegiatan-index.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
