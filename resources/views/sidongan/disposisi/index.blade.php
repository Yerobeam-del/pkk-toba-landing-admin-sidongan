{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Disposisi Surat - SIDONGAN')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-disposisi-index.css') }}">


<div class="sd-page u-px-6">
    {{-- Header --}}
    <div class="animate-slide-in u-mb-8">
        <h1 class="u-h2-slate">
            Disposisi Surat
        </h1>
        <p class="u-text-muted-lead">
            Kelola disposisi surat untuk diteruskan ke pelaksana
        </p>
    </div>

    {{-- Stats Cards --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        {{-- Menunggu Disposisi - WARNA ORANGE --}}
        <div class="stats-card animate-slide-in" 
             style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-hourglass-half u-text-2xl"></i>
                </div>
                <div class="u-flex-1">
                    <p class="u-a7">
                        Menunggu Disposisi
                    </p>
                    <p class="u-a8">
                        {{ $documents->total() ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Sudah Didisposisi - WARNA HIJAU --}}
        <div class="stats-card animate-slide-in" 
             style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-check-circle u-text-2xl"></i>
                </div>
                <div class="u-flex-1">
                    <p class="u-a7">
                        Sudah Didisposisi
                    </p>
                    <p class="u-a8">
                        {{ \App\Models\Document::where('status', 'berjalan')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Total Surat Bulan Ini - WARNA BIRU --}}
        <div class="stats-card animate-slide-in" 
             style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-calendar-check u-text-2xl"></i>
                </div>
                <div class="u-flex-1">
                    <p class="u-a7">
                        Surat Bulan Ini
                    </p>
                    <p class="u-a8">
                        {{ \App\Models\Document::whereMonth('created_at', now()->month)->count() }}
                    </p>
                    <p style="font-size: 0.8rem; opacity: 0.9; margin: 0.25rem 0 0 0; font-weight: 500;">
                        {{ now()->locale('id')->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" class="animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.disposisi') }}">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
                <div>
                    <label class="u-label-gray">
                        Cari Surat
                    </label>
                    <div class="u-relative">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.875rem;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                            placeholder="Cari berdasarkan judul, nomor, atau pengirim..." 
                            style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; transition: all 0.2s;">
                    </div>
                </div>
                
                <div>
                    <label class="u-label-gray">
                        Urutkan
                    </label>
                    <div class="u-relative">
                        <select class="u-a49" name="sort">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="agenda" {{ request('sort') == 'agenda' ? 'selected' : '' }}>No. Agenda</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="u-label-gray">
                        Tampilkan
                    </label>
                    <div class="u-relative">
                        <select class="u-a49" name="per_page" id="perPageSelect">
                            @foreach([5, 10, 15, 25, 50] as $value)
                                <option value="{{ $value }}" {{ (request('per_page', 10) == $value) ? 'selected' : '' }}>
                                    {{ $value }} surat
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Daftar Surat Disposisi --}}
    <div class="animate-slide-in u-a78">        
        @if(isset($documents) && $documents->count() > 0)
            <div class="u-p-0">
                @foreach($documents as $index => $doc)
                    {{-- Item Disposisi --}}
                    <div class="disposisi-item animate-slide-in" 
                        style="padding: 1.5rem 1.75rem; border-bottom: {{ $loop->last ? 'none' : '1px solid #f3f4f6' }}; 
                                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                border-left: 3px solid transparent;
                                position: relative;
                                overflow: hidden;">
                        
                        {{-- Baris kepala kartu: aturan mobile bersama ada di kelas sd-list-head --}}
                        <div class="sd-list-head" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div class="u-flex-1">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.8rem; font-family: monospace; background: white; color: #ea580c; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700; border: 1px solid #fed7aa; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        {{ $doc->agenda_number }}
                                    </span>
                                    <span style="font-size: 0.75rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-weight: 600; background: #fef3c7; color: #92400e; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        Menunggu Disposisi
                                    </span>
                                </div>
                                
                                <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 0.75rem 0; line-height: 1.4;">
                                    {{ $doc->subject ?? $doc->title }}
                                </h4>
                                
                                <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; color: #64748b; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                    <span class="u-flex-center-gap-2">
                                        <div style="width: 1.5rem; height: 1.5rem; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="color: #f97316; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $doc->sender }}
                                    </span>
                                    <span class="u-flex-center-gap-2">
                                        <div style="width: 1.5rem; height: 1.5rem; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-calendar" style="color: #f97316; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $doc->document_date->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Kolom tombol kartu: aturan mobile bersama ada di kelas sd-list-actions --}}
                            <div class="sd-list-actions" style="display: flex; gap: 0.5rem; flex-shrink: 0; flex-direction: column;">
                                <a href="{{ route('sidongan.disposisi.form', $doc->id) }}?from={{ urlencode(route('sidongan.disposisi')) }}"
                                    class="btn-action"
                                    style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #f97316, #ea580c); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(249, 115, 22, 0.2);">
                                        <i class="fas fa-share"></i>
                                        <span>Disposisi</span>
                                </a>
                                <a href="{{ route('sidongan.documents.show', $doc) }}?from={{ urlencode(route('sidongan.disposisi')) }}" 
                                    class="sd-btn-detail">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                </a>
                            </div>
                        </div>
                        
                        @if($doc->suggestion)
                        <div style="background: #fff7ed; border-left: 3px solid #f97316; border-radius: 0.5rem; padding: 0.875rem 1rem;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <div style="width: 2rem; height: 2rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <i class="fas fa-comment-alt" style="color: #f97316; font-size: 0.875rem;"></i>
                                </div>
                                <div class="u-flex-1">
                                    <p style="font-size: 0.8rem; color: #92400e; margin: 0 0 0.375rem 0; font-weight: 700;">Saran Sekretaris</p>
                                    <p style="font-size: 0.875rem; color: #64748b; margin: 0; line-height: 1.5; font-style: italic;">
                                        "{{ Str::limit($doc->suggestion, 100) }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination Modern --}}
            @if($documents->hasPages())
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="u-text-sm-muted-2">
                    Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
                </div>
                
                {{-- Tombol nomor halaman: aturan mobile bersama ada di kelas sd-pagination --}}
                <div class="sd-pagination u-a79">
                    @if($documents->onFirstPage())
                        <span class="u-a15">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" 
                        class="sd-page-btn sd-page-btn-orange">
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
                            <span class="sd-page-btn sd-page-btn-orange">
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
                        class="sd-page-btn sd-page-btn-orange">
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
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="u-text-sm-muted-2">
                    Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
                </div>
                <div class="u-text-xs-muted">
                    <i class="fas fa-info-circle"></i> Semua surat ditampilkan dalam satu halaman
                </div>
            </div>
            @endif
        @else
            {{-- Empty State --}}
            <div style="text-align: center; padding: 3rem 1.5rem;" class="animate-slide-in">
                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); animation: float 3s ease-in-out infinite;">
                    <i class="fas fa-check-double" style="color: #10b981; font-size: 3rem;"></i>
                </div>
                <h4 class="u-h3-slate">Semua Surat Sudah Didisposisi</h4>
                <p class="u-text-muted-lead">
                    Tidak ada surat yang menunggu disposisi saat ini.
                </p>
                <a href="{{ route('sidongan.documents.index') }}" 
                   class="btn-action"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; text-decoration: none; border-radius: 0.625rem; font-weight: 600; margin-top: 1.5rem; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar Surat</span>
                </a>
            </div>
        @endif
    </div>
</div>

    <script src="{{ asset('assets/sidongan/js/sidongan-disposisi-index.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
