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
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
        {{-- Perlu Dilaporkan - BIRU --}}
        <div class="stats-card animate-slide-in" 
            style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-tasks u-text-2xl"></i>
                </div>
                <div class="u-flex-1"><p class="u-a7">
                         Perlu Dilaporkan
                     </p>
                    <p class="u-a8">
                        {{ $perluDilaporkan ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Menunggu Verifikasi - ORANGE --}}
        <div class="stats-card animate-slide-in" 
            style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-clock u-text-2xl"></i>
                </div>
                <div class="u-flex-1"><p class="u-a7">
                         Menunggu Verifikasi
                     </p>
                    <p class="u-a8">
                        {{ $menungguVerifikasi ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Disetujui - HIJAU --}}
        <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);" class="stats-card animate-slide-in" 
           >
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-check-circle u-text-2xl"></i>
                </div>
                <div class="u-flex-1"><p class="u-a7">
                         Disetujui
                     </p>
                    <p class="u-a8">
                        {{ $disetujui ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Ditolak - MERAH --}}
        <div class="stats-card animate-slide-in" 
            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
            <div class="u-deco-circle-tr"></div>
            <div class="u-deco-circle-bl"></div>
            
            <div class="u-flex-center-gap-4-rel">
                <div class="u-icon-badge-lg">
                    <i class="fas fa-times-circle u-text-2xl"></i>
                </div>
                <div class="u-flex-1"><p class="u-a7">
                         Ditolak
                     </p>
                    <p class="u-a8">
                        {{ $ditolak ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="animate-slide-in">
        <form id="filterForm" method="GET">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
                <div>
                    <label class="u-label-gray">Cari Laporan</label>
                    <div class="u-relative">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.875rem; pointer-events: none;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari berdasarkan judul kegiatan..." style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; transition: all 0.2s;">
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
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; overflow: hidden;" class="animate-slide-in">
        @if(isset($documents) && $documents->count() > 0)
            <div class="u-p-0">
                @foreach($documents as $doc)
                    @php
                        // Satu surat = SATU laporan: laporan apa pun (dari role tujuan
                        // mana pun) yang sudah ada harus terbaca di sini agar suratnya
                        // tidak tampil sebagai "Perlu Dilaporkan" untuk kedua kalinya.
                        $existingReport = \App\Models\ActivityReport::where('document_id', $doc->id)
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        $statusConfig = [
                            null => ['bg' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#075985', 'btn' => '#0ea5e9', 'label' => 'Perlu Dilaporkan', 'icon' => 'fa-pen'],
                            'draft' => ['bg' => '#f0f9ff', 'border' => '#bae6fd', 'text' => '#075985', 'btn' => '#0ea5e9', 'label' => 'Draft', 'icon' => 'fa-pen'],
                            'menunggu_verifikasi' => ['bg' => '#fff7ed', 'border' => '#fed7aa', 'text' => '#9a3412', 'btn' => '#f97316', 'label' => 'Menunggu Verifikasi', 'icon' => 'fa-clock'],
                            'disetujui' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'btn' => '#22c55e', 'label' => 'Disetujui', 'icon' => 'fa-check-circle'],
                            'ditolak' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b', 'btn' => '#ef4444', 'label' => 'Ditolak', 'icon' => 'fa-times-circle'],
                        ];
                        
                        $theme = $statusConfig[$existingReport->status ?? null] ?? $statusConfig[null];
                    @endphp
                    
                    <div class="laporan-item animate-slide-in" 
                        style="padding: 1.5rem 1.75rem; border-bottom: {{ $loop->last ? 'none' : '1px solid #f3f4f6' }}; 
                                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                border-left: 3px solid transparent;
                                position: relative;
                                overflow: hidden;
                                --lk-bg: {{ $theme['bg'] }};
                                --lk-border: {{ $theme['btn'] }};">
                        
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;" class="sd-list-head">
                            <div class="u-flex-1">
                                <div class="u-flex-1">
                                    <span style="font-size: 0.8rem; font-family: monospace; background: white; color: {{ $theme['text'] }}; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700; border: 1px solid {{ $theme['border'] }}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        {{ $doc->agenda_number }}
                                    </span>
                                    <span style="font-size: 0.75rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-weight: 600; background: {{ $theme['btn'] }}; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                        <i class="fas {{ $theme['icon'] }}" style="margin-right: 0.35rem; font-size: 0.65rem;"></i>
                                        {{ $theme['label'] }}
                                    </span>
                                </div>
                                
                                <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 0.75rem 0; line-height: 1.4;">
                                    {{ $doc->subject ?? $doc->title }}
                                </h4>
                                
                                <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; color: #64748b; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                    <span class="u-flex-center-gap-2">
                                        <div style="width: 1.5rem; height: 1.5rem; background: {{ $theme['bg'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="color: {{ $theme['btn'] }}; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $doc->sender }}
                                    </span>
                                    <span class="u-flex-center-gap-2">
                                        <div style="width: 1.5rem; height: 1.5rem; background: {{ $theme['bg'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-calendar" style="color: {{ $theme['btn'] }}; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $doc->created_at->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-shrink: 0; flex-direction: column;" class="sd-list-actions">
                                @if($existingReport)
                                    @if($existingReport->status === 'ditolak')
                                        {{-- Surat ditolak: siapa pun dari role tujuan disposisi boleh
                                             membuat laporan baru (data lama otomatis dimuat di form).
                                             Revisi di tempat tetap tersedia sebagai opsi kedua. --}}
                                        <a href="{{ route('sidongan.lapor_kegiatan.create', ['document_id' => $doc->id]) }}" 
                                        class="btn-action"
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);">
                                            <i class="fas fa-plus"></i>
                                            <span>Buat Laporan</span>
                                        </a>
                                        <a href="{{ route('sidongan.lapor_kegiatan.edit', $existingReport->id) }}" 
                                        class="btn-action"
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: white; color: #c2410c; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: 1px solid #fed7aa; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <i class="fas fa-edit"></i>
                                            <span>Revisi Laporan</span>
                                        </a>
                                    @else
                                        {{-- Tombol Lihat untuk status lainnya --}}
                                        <a href="{{ route('sidongan.lapor_kegiatan.show', $existingReport->id) }}" 
                                        class="btn-action"
                                        style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: white; color: {{ $theme['text'] }}; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: 1px solid {{ $theme['border'] }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                            <i class="fas fa-eye"></i>
                                            <span>Lihat</span>
                                        </a>
                                    @endif
                                @else
                                    <a href="{{ route('sidongan.lapor_kegiatan.create', ['document_id' => $doc->id]) }}" 
                                    class="btn-action"
                                    style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #0ea5e9, #0284c7); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);">
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
                            <div style="background: {{ $theme['bg'] }}; border-left: 3px solid {{ $theme['btn'] }}; border-radius: 0.5rem; padding: 0.875rem 1rem;">
                                <div style="display: flex; align-items: start; gap: 0.75rem;">
                                    <div style="width: 2rem; height: 2rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                        <i class="fas fa-info-circle" style="color: {{ $theme['btn'] }}; font-size: 0.875rem;"></i>
                                    </div>
                                    <div class="u-flex-1">
                                        <p style="font-size: 0.8rem; color: {{ $theme['text'] }}; margin: 0 0 0.375rem 0; font-weight: 700;">Instruksi Disposisi</p>
                                        <p style="font-size: 0.875rem; color: #64748b; margin: 0; line-height: 1.5;">
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
                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #f0f9ff, #e0f2fe); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15); animation: float 3s ease-in-out infinite;">
                    <i class="fas fa-inbox" style="color: #0ea5e9; font-size: 3rem;"></i>
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
