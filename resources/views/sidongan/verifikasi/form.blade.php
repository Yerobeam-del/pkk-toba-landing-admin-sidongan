{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Form Verifikasi Laporan - SIDONGAN')

@section('content')
@php
    // ✅ Ambil URL kembali dari session
    $backUrl = session('verifikasi_form_back_url', route('sidongan.lapor_kegiatan.show', $report->id));
    
    // Validasi URL - pastikan bukan halaman form verifikasi
    if (str_contains($backUrl, '/verifikasi/form') || 
        str_contains($backUrl, '/verifikasi-print')) {
        $backUrl = route('sidongan.lapor_kegiatan.show', $report->id);
    }
@endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-verifikasi-form.css') }}">


<div class="verifikasi-container sd-page u-px-6">
{{-- Header --}}
<div class="verifikasi-header animate-slide-in" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2);">
    <div class="sd-page-header u-a89">
        <div class="u-flex-center-gap-3">
            <div class="u-icon-badge-sm">
                <i class="fas fa-clipboard-check u-a90"></i>
            </div>
            <div>
                <h1 class="u-h3">Form Verifikasi</h1>
                <p class="u-subtitle-flat">Tinjau detail laporan dan tentukan keputusan</p>
            </div>
        </div>
        <div class="sd-header-actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ $backUrl }}" class="sd-btn-back"
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: rgba(255,255,255,0.25); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: all 0.25s ease; backdrop-filter: blur(4px); border: 1px solid rgba(255, 255, 255, 0.3);">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>
</div>

    <form action="{{ route('sidongan.verifikasi.store', $report->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="content-grid" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 1.5rem; align-items: start;">
            
            {{-- LEFT: Full Report Detail --}}
            <div class="animate-slide-in u-card-white">
                <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-bottom: 2px solid #ddd6fe;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-file-alt" style="color: #7c3aed;"></i>
                        Detail Laporan Kegiatan
                    </h3>
                </div>
                <div class="u-p-6">
                    {{-- Basic Info --}}
                    <div class="u-mb-6">
                        <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem 0;">{{ $report->kegiatan_nama }}</h4>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <span class="u-flex-center-gap-2">
                                <div class="u-a91">
                                    <i class="fas fa-calendar u-a92"></i>
                                </div>
                                <span class="u-text-sm-muted-2">
                                    {{ $report->kegiatan_tanggal->locale('id')->translatedFormat('d F Y') }}
                                </span>
                            </span>
                            @if($report->lokasi)
                            <span class="u-flex-center-gap-2">
                                <div class="u-a91">
                                    <i class="fas fa-map-marker-alt u-a92"></i>
                                </div>
                                <span class="u-text-sm-muted-2">
                                    {{ $report->lokasi }}
                                </span>
                            </span>
                            @endif
                            <span class="u-flex-center-gap-2">
                                <div class="u-a91">
                                    <i class="fas fa-user u-a92"></i>
                                </div>
                                <span class="u-text-sm-muted-2">
                                    {{ $report->creator->name ?? 'Sekretaris PKK' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="u-mb-6">
                        <span style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Deskripsi Kegiatan</span>
                        <div class="u-box-slate">
                            <p style="font-size: 0.9rem; color: #334155; margin: 0; line-height: 1.7;">
                                {!! nl2br(e($report->deskripsi)) !!}
                            </p>
                        </div>
                    </div>

                    {{-- Photos with Gallery Popup --}}
                    @php
                        $fotosArray = is_string($report->fotos) ? json_decode($report->fotos, true) : $report->fotos;
                        $fotosArray = is_array($fotosArray) ? $fotosArray : [];
                    @endphp
                    @if(count($fotosArray) > 0)
                    <div>
                        <span style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.75rem;">
                            <i class="fas fa-images u-mr-1"></i>
                            Dokumentasi Kegiatan ({{ count($fotosArray) }} foto)
                        </span>
                        <div class="thumb-grid">
                            @foreach($fotosArray as $index => $foto)
                            <div class="thumb-item" data-index="{{ $index }}">
                                <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi {{ $index + 1 }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Verification Form (Sticky) --}}
            <div class="animate-slide-in" style="position: sticky; top: 1rem;">
                <div class="u-card-white">
                    <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #fffbeb, #fef3c7); border-bottom: 2px solid #fcd34d;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #92400e; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clipboard-check u-a81"></i>
                            Keputusan Verifikasi
                        </h3>
                    </div>
                    <div class="u-p-6">
                        
                        {{-- Status Choice (Colored Icons) --}}
                        <div class="u-mb-6">
                            <label class="u-section-title-dark">
                                Status Keputusan <span class="u-text-danger">*</span>
                            </label>
                            <div class="u-grid-2-gap-4">
                                
                                {{-- Option: Approve (Green) --}}
                                <label class="decision-card selected-approve" id="option-approve" data-value="disetujui">
                                    <input class="u-hidden" type="radio" name="status" value="disetujui" required checked>
                                    <div class="card-content" style="position: relative; padding: 1.5rem 1rem; text-align: center;">
                                        <div class="card-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <p class="card-title" style="font-weight: 700; margin: 0; font-size: 1rem; transition: color 0.3s;">Setujui</p>
                                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0;">Laporan valid & sesuai</p>
                                    </div>
                                </label>

                                {{-- Option: Reject (Red) --}}
                                <label class="decision-card" id="option-reject" data-value="ditolak">
                                    <input class="u-hidden" type="radio" name="status" value="ditolak" required>
                                    <div class="card-content" style="position: relative; padding: 1.5rem 1rem; text-align: center;">
                                        <div class="card-icon">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <p class="card-title" style="font-weight: 700; margin: 0; font-size: 1rem; transition: color 0.3s;">Tolak / Revisi</p>
                                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0;">Perlu perbaikan data</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="u-mb-6">
                            <label class="u-section-title-dark">
                                Catatan Verifikasi
                            </label>
                            <textarea name="catatan_verifikasi" rows="4" placeholder="Berikan catatan atau alasan jika ditolak (Opsional)..."
                                      style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; resize: vertical; box-sizing: border-box; font-family: inherit;">{{ old('catatan_verifikasi') }}</textarea>
                        </div>

                        {{-- Submit Buttons --}}
                        <div style="display: flex; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                            <a href="{{ $backUrl }}" 
                               style="flex: 1; padding: 0.75rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                                Batal
                            </a>
                            <button type="submit" 
                                    style="flex: 2; padding: 0.75rem; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(124,58,237,0.2); font-size: 0.875rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                                <i class="fas fa-save"></i>
                                <span>Simpan Keputusan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODAL GALLERY POPUP --}}
<div id="galleryOverlay" class="gallery-overlay" data-fotos='{{ json_encode($fotosArray ?? []) }}' data-storage="{{ asset('storage') }}">
    <button class="gallery-close" >
        <i class="fas fa-times"></i>
    </button>
    
    <div class="gallery-container" >
        <div class="gallery-image-wrapper">
            <img id="galleryImage" class="gallery-image" src="" alt="Dokumentasi">
        </div>
        
        <button class="gallery-nav prev" >
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="gallery-nav next" >
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div class="gallery-bottom-bar">
            <span id="galleryCounter" class="gallery-counter">1 / 1</span>
            <a id="galleryDownload" class="gallery-download-btn" href="" download>
                <i class="fas fa-download"></i>
                <span>Unduh Foto</span>
            </a>
        </div>
        
        <div id="galleryThumbnails" style="display: flex; gap: 0.5rem; margin-top: 0.5rem; justify-content: center; max-width: 80vw;"></div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/sidongan/js/verifikasi-form.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
