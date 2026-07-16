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

<style>
    /* =========================================
       Styles untuk Kartu Pilihan (Colored Icons)
       ========================================= */
    .decision-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        border-radius: 0.75rem;
    }
    .decision-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.05);
        border-color: #cbd5e1;
    }
    
    /* Style saat dipilih: Setujui (Hijau) */
    .decision-card.selected-approve {
        border-color: #22c55e;
        background-color: #f0fdf4;
    }
    .decision-card.selected-approve .card-icon {
        background-color: #22c55e;
        color: white;
    }
    .decision-card.selected-approve .card-title { color: #15803d; }
    
    /* Style saat dipilih: Tolak (Merah) */
    .decision-card.selected-reject {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    .decision-card.selected-reject .card-icon {
        background-color: #ef4444;
        color: white;
    }
    .decision-card.selected-reject .card-title { color: #b91c1c; }

    .card-icon {
        width: 3.5rem; height: 3.5rem;
        background-color: #e2e8f0;
        color: #64748b;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.75rem;
        transition: all 0.3s;
        font-size: 1.5rem;
    }

    /* =========================================
       Styles untuk Gallery & Animasi Popup
       ========================================= */
    .thumb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        gap: 0.75rem;
    }
    .thumb-item {
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: transform 0.2s, border-color 0.2s;
    }
    .thumb-item:hover {
        transform: scale(1.05);
        border-color: #7c3aed;
    }
    .thumb-item:active {
        transform: scale(0.92);
    }
    .thumb-item img {
        width: 100%; height: 100%;
        object-fit: cover;
    }

    /* Gallery Modal CSS */
    .gallery-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(8px);
        z-index: 9999;
        align-items: center; justify-content: center;
        opacity: 0; transition: opacity 0.3s ease;
    }
    .gallery-overlay.active { display: flex; opacity: 1; }
    
    .gallery-container {
        position: relative;
        max-width: 85vw; max-height: 85vh;
        display: flex; flex-direction: column; align-items: center;
    }
    .gallery-image-wrapper {
        position: relative; overflow: hidden;
        border-radius: 0.75rem; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        max-width: 80vw; max-height: 70vh;
    }
    .gallery-image {
        display: block; max-width: 80vw; max-height: 70vh;
        object-fit: contain; transition: opacity 0.3s ease;
    }
    
    @keyframes zoomIn {
        0% { opacity: 0; transform: scale(0.8); }
        100% { opacity: 1; transform: scale(1); }
    }
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    @keyframes slideLeft { 0% { opacity: 0; transform: translateX(50px); } 100% { opacity: 1; transform: translateX(0); } }
    @keyframes slideRight { 0% { opacity: 0; transform: translateX(-50px); } 100% { opacity: 1; transform: translateX(0); } }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .gallery-image.zoom-in { animation: zoomIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .gallery-image.fade-in { animation: fadeIn 0.3s ease forwards; }
    .gallery-image.slide-left { animation: slideLeft 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
    .gallery-image.slide-right { animation: slideRight 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }

    .gallery-close {
        position: fixed; top: 20px; right: 20px;
        width: 44px; height: 44px;
        background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50%; color: white; font-size: 1.25rem;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s; z-index: 10000;
    }
    .gallery-close:hover { background: rgba(255,255,255,0.4); transform: rotate(90deg); }

    .gallery-nav {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 44px; height: 44px;
        background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);
        border-radius: 50%; color: white; font-size: 1.25rem;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: all 0.3s;
    }
    .gallery-nav:hover { background: rgba(255,255,255,0.4); transform: translateY(-50%) scale(1.1); }
    .gallery-nav.prev { left: -60px; }
    .gallery-nav.next { right: -60px; }

    .gallery-bottom-bar {
        display: flex; 
        align-items: center; 
        justify-content: space-between;
        width: 100%; 
        margin-top: 1.5rem;
        padding: 0 0.5rem;
        position: relative;
        z-index: 20;
    }

    .gallery-counter {
        color: rgba(255,255,255,0.9); font-size: 0.875rem; font-weight: 600;
        background: rgba(255,255,255,0.15); padding: 0.35rem 0.85rem; border-radius: 9999px;
    }
    .gallery-download-btn {
        display: inline-flex; align-items: center; gap: 0.5rem;
        padding: 0.5rem 1.25rem; background: rgba(59, 130, 246, 0.8);
        border: 1px solid rgba(59, 130, 246, 0.5); border-radius: 0.5rem;
        color: white; font-size: 0.85rem; font-weight: 600;
        cursor: pointer; transition: all 0.3s; text-decoration: none;
    }
    .gallery-download-btn:hover { background: rgba(59, 130, 246, 1); transform: translateY(-2px); }

    .animate-slide-in {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .verifikasi-container {
            padding: 0 1rem;
        }
        
        .content-grid {
            grid-template-columns: 1fr !important;
            gap: 1rem;
        }
        
        .verifikasi-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .verifikasi-header-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .verifikasi-header-actions a {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .gallery-nav.prev { left: 10px; }
        .gallery-nav.next { right: 10px; }
        .gallery-image { max-width: 95vw; max-height: 60vh; }
        .gallery-bottom-bar { flex-direction: column; gap: 0.5rem; }
    }
</style>

<div class="verifikasi-container" style="padding: 0 1.5rem;">
{{-- Header --}}
<div class="verifikasi-header animate-slide-in" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 4px 20px rgba(124, 58, 237, 0.2);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <i class="fas fa-clipboard-check" style="font-size: 1.5rem; color: white;"></i>
            </div>
            <div>
                <h1 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0;">Form Verifikasi</h1>
                <p style="font-size: 0.875rem; opacity: 0.95; margin: 0;">Tinjau detail laporan dan tentukan keputusan</p>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ $backUrl }}" 
               style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: rgba(255,255,255,0.25); color: white; text-decoration: none; border-radius: 0.5rem; font-weight: 600; transition: all 0.25s ease; backdrop-filter: blur(4px); border: 1px solid rgba(255, 255, 255, 0.3);" 
               onmouseover="this.style.background='rgba(255,255,255,0.35)'; this.style.transform='translateY(-2px)'" 
               onmouseout="this.style.background='rgba(255,255,255,0.25)'; this.style.transform='translateY(0)'">
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
            <div class="animate-slide-in" style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;">
                <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #f5f3ff, #ede9fe); border-bottom: 2px solid #ddd6fe;">
                    <h3 style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-file-alt" style="color: #7c3aed;"></i>
                        Detail Laporan Kegiatan
                    </h3>
                </div>
                <div style="padding: 1.5rem;">
                    {{-- Basic Info --}}
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 1rem 0;">{{ $report->kegiatan_nama }}</h4>
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <span style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 1.5rem; height: 1.5rem; background: #f5f3ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-calendar" style="color: #7c3aed; font-size: 0.7rem;"></i>
                                </div>
                                <span style="font-size: 0.875rem; color: #64748b;">
                                    {{ $report->kegiatan_tanggal->locale('id')->translatedFormat('d F Y') }}
                                </span>
                            </span>
                            @if($report->lokasi)
                            <span style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 1.5rem; height: 1.5rem; background: #f5f3ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-map-marker-alt" style="color: #7c3aed; font-size: 0.7rem;"></i>
                                </div>
                                <span style="font-size: 0.875rem; color: #64748b;">
                                    {{ $report->lokasi }}
                                </span>
                            </span>
                            @endif
                            <span style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 1.5rem; height: 1.5rem; background: #f5f3ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="color: #7c3aed; font-size: 0.7rem;"></i>
                                </div>
                                <span style="font-size: 0.875rem; color: #64748b;">
                                    {{ $report->creator->name ?? 'Sekretaris PKK' }}
                                </span>
                            </span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom: 1.5rem;">
                        <span style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Deskripsi Kegiatan</span>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;">
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
                            <i class="fas fa-images" style="margin-right: 0.35rem;"></i>
                            Dokumentasi Kegiatan ({{ count($fotosArray) }} foto)
                        </span>
                        <div class="thumb-grid">
                            @foreach($fotosArray as $index => $foto)
                            <div class="thumb-item" onclick="openGallery({{ $index }})">
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
                <div style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;">
                    <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #fffbeb, #fef3c7); border-bottom: 2px solid #fcd34d;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; color: #92400e; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-clipboard-check" style="color: #d97706;"></i>
                            Keputusan Verifikasi
                        </h3>
                    </div>
                    <div style="padding: 1.5rem;">
                        
                        {{-- Status Choice (Colored Icons) --}}
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                                Status Keputusan <span style="color: #ef4444;">*</span>
                            </label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                
                                {{-- Option: Approve (Green) --}}
                                <label class="decision-card selected-approve" id="option-approve" onclick="selectOption('disetujui')">
                                    <input type="radio" name="status" value="disetujui" required style="display: none;" checked>
                                    <div class="card-content" style="position: relative; padding: 1.5rem 1rem; text-align: center;">
                                        <div class="card-icon">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <p class="card-title" style="font-weight: 700; margin: 0; font-size: 1rem; transition: color 0.3s;">Setujui</p>
                                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0;">Laporan valid & sesuai</p>
                                    </div>
                                </label>

                                {{-- Option: Reject (Red) --}}
                                <label class="decision-card" id="option-reject" onclick="selectOption('ditolak')">
                                    <input type="radio" name="status" value="ditolak" required style="display: none;">
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
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                                Catatan Verifikasi
                            </label>
                            <textarea name="catatan_verifikasi" rows="4" placeholder="Berikan catatan atau alasan jika ditolak (Opsional)..."
                                      style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; resize: vertical; box-sizing: border-box; font-family: inherit;" 
                                      onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124,58,237,0.1)'" 
                                      onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">{{ old('catatan_verifikasi') }}</textarea>
                        </div>

                        {{-- Submit Buttons --}}
                        <div style="display: flex; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                            <a href="{{ $backUrl }}" 
                               style="flex: 1; padding: 0.75rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 0.875rem; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;" 
                               onmouseover="this.style.background='#f1f5f9'" 
                               onmouseout="this.style.background='white'">
                                Batal
                            </a>
                            <button type="submit" 
                                    style="flex: 2; padding: 0.75rem; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; border-radius: 0.5rem; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 8px rgba(124,58,237,0.2); font-size: 0.875rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" 
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(124,58,237,0.3)'" 
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(124,58,237,0.2)'">
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
<div id="galleryOverlay" class="gallery-overlay" onclick="closeGallery(event)">
    <button class="gallery-close" onclick="closeGallery()">
        <i class="fas fa-times"></i>
    </button>
    
    <div class="gallery-container" onclick="event.stopPropagation()">
        <div class="gallery-image-wrapper">
            <img id="galleryImage" class="gallery-image" src="" alt="Dokumentasi">
        </div>
        
        <button class="gallery-nav prev" onclick="navigateGallery(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="gallery-nav next" onclick="navigateGallery(1)">
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

<script>
    // ===============================
    // Logic untuk Kartu Status (Warna)
    // ===============================
    function selectOption(value) {
        document.getElementById('option-approve').classList.remove('selected-approve');
        document.getElementById('option-reject').classList.remove('selected-reject');
        
        if (value === 'disetujui') {
            document.getElementById('option-approve').classList.add('selected-approve');
        } else {
            document.getElementById('option-reject').classList.add('selected-reject');
        }
    }

    // ===============================
    // Logic untuk Gallery Popup
    // ===============================
    const galleryFotos = @json($fotosArray ?? []);
    let currentIndex = 0;
    let isAnimating = false;
    
    function openGallery(index) {
        currentIndex = index;
        updateGalleryImage('zoom-in'); 
        updateGalleryUI(); 
        document.getElementById('galleryOverlay').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeGallery(event) {
        if (event && event.target !== document.getElementById('galleryOverlay')) return;
        const overlay = document.getElementById('galleryOverlay');
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.classList.remove('active');
            overlay.style.opacity = '';
        }, 300);
        document.body.style.overflow = '';
    }
    
    function navigateGallery(direction) {
        if (isAnimating || galleryFotos.length <= 1) return;
        isAnimating = true;
        
        const animClass = direction > 0 ? 'slide-left' : 'slide-right';
        const nextIndex = (currentIndex + direction + galleryFotos.length) % galleryFotos.length;
        
        const img = document.getElementById('galleryImage');
        img.style.opacity = '0';
        img.style.transform = direction > 0 ? 'translateX(-40px) scale(0.95)' : 'translateX(40px) scale(0.95)';
        
        setTimeout(() => {
            currentIndex = nextIndex;
            img.src = '{{ asset("storage") }}/' + galleryFotos[currentIndex];
            img.className = 'gallery-image ' + animClass;
            
            setTimeout(() => {
                img.style.opacity = '1';
                img.style.transform = 'translateX(0) scale(1)';
            }, 50);
            
            updateGalleryUI();
            setTimeout(() => { isAnimating = false; img.className = 'gallery-image'; }, 400);
        }, 200);
    }
    
    function updateGalleryImage(animClass = 'fade-in') {
        const img = document.getElementById('galleryImage');
        img.src = '{{ asset("storage") }}/' + galleryFotos[currentIndex];
        img.className = 'gallery-image ' + animClass;
        updateGalleryUI();
    }
    
    function updateGalleryUI() {
        document.getElementById('galleryCounter').textContent = `${currentIndex + 1} / ${galleryFotos.length}`;
        document.getElementById('galleryDownload').href = '{{ asset("storage") }}/' + galleryFotos[currentIndex];
        
        const prevBtn = document.querySelector('.gallery-nav.prev');
        const nextBtn = document.querySelector('.gallery-nav.next');
        if (galleryFotos.length <= 1) {
            prevBtn.style.display = 'none'; nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex'; nextBtn.style.display = 'flex';
        }
        
        const container = document.getElementById('galleryThumbnails');
        container.innerHTML = '';
        galleryFotos.forEach((foto, index) => {
            const thumb = document.createElement('div');
            thumb.style.cssText = `width: 40px; height: 40px; border-radius: 0.25rem; overflow: hidden; border: 2px solid ${index === currentIndex ? '#fff' : 'rgba(255,255,255,0.3)'}; cursor: pointer; opacity: ${index === currentIndex ? '1' : '0.5'}; flex-shrink: 0;`;
            thumb.innerHTML = `<img src="{{ asset('storage') }}/${foto}" style="width:100%; height:100%; object-fit:cover;">`;
            thumb.onclick = () => {
                if (index !== currentIndex) navigateGallery(index - currentIndex);
            };
            container.appendChild(thumb);
        });
    }
    
    document.addEventListener('keydown', (e) => {
        const overlay = document.getElementById('galleryOverlay');
        if (!overlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowLeft') navigateGallery(-1);
        if (e.key === 'ArrowRight') navigateGallery(1);
    });
</script>
@endsection