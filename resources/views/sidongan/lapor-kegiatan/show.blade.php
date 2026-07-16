@extends('sidongan.layouts.app')
@section('title', 'Detail Laporan Kegiatan - SIDONGAN')

@section('content')
@php
    // Ambil URL kembali dari session
    $backUrl = session('detail_laporan_back_url', route('sidongan.lapor_kegiatan.index'));
    
    // Validasi URL - pastikan bukan halaman create/edit/verifikasi/form
    if (str_contains($backUrl, '/create') || 
        str_contains($backUrl, '/edit') || 
        str_contains($backUrl, '/verifikasi/form')) {  // ← Hanya tolak form verifikasi
        $backUrl = route('sidongan.lapor_kegiatan.index');
    }
    
    $currentUser = auth()->guard('sidongan')->user();
    $isKetua = $currentUser && $currentUser->hasSidonganRole('ketua');
    $canVerify = $isKetua && $report->status === 'menunggu_verifikasi';
@endphp

<link rel="stylesheet" href="{{ asset('assets/sidongan/css/detail-laporan.css') }}">

<div class="dl-container">
    {{-- Header --}}
    <div class="dl-header">
        <div class="dl-header-top">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-clipboard-list" style="font-size: 1.5rem; color: white;"></i>
                </div>
                <div>
                    <h1>Detail Laporan Kegiatan</h1>
                    <p>Informasi lengkap laporan kegiatan</p>
                </div>
            </div>
            
            <div class="dl-header-actions">
                @if($canVerify)
                <a href="{{ route('sidongan.verifikasi.form', $report->id) }}" class="dl-btn dl-btn-verify">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Verifikasi Laporan</span>
                </a>
                @endif
                
                <a href="{{ $backUrl }}" class="dl-btn dl-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="dl-grid">
        
        {{-- KOLOM KIRI: Informasi Kegiatan --}}
        <div class="dl-card">
            <div class="dl-card-header dl-card-header-info">
                <h2>
                    <i class="fas fa-clipboard-list"></i>
                    Informasi Kegiatan
                </h2>
            </div>
            <div class="dl-card-body">
                <div class="dl-info-item">
                    <span class="dl-info-label">Nama Kegiatan</span>
                    <p class="dl-info-value" style="font-size: 1.05rem; font-weight: 600;">{{ $report->kegiatan_nama }}</p>
                </div>
                
                <div class="dl-info-grid">
                    <div>
                        <span class="dl-info-label">Tanggal Kegiatan</span>
                        <p class="dl-info-value" style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 1.5rem; height: 1.5rem; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar" style="color: #0891b2; font-size: 0.7rem;"></i>
                            </div>
                            @if($report->kegiatan_tanggal)
                                {{ $report->kegiatan_tanggal->locale('id')->translatedFormat('d F Y') }}
                            @else
                                <span class="dl-info-value-muted">-</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="dl-info-label">Waktu Kegiatan</span>
                        @if($report->start_time && $report->end_time)
                            <p class="dl-info-value" style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="width: 1.5rem; height: 1.5rem; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-clock" style="color: #0891b2; font-size: 0.7rem;"></i>
                                </div>
                                {{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}
                            </p>
                        @else
                            <p class="dl-info-value-muted">Waktu tidak tersedia</p>
                        @endif
                    </div>
                </div>
                
                {{-- Lokasi Kegiatan --}}
                <div class="dl-info-item">
                    <span class="dl-info-label" style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 1.5rem; height: 1.5rem; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marker-alt" style="color: #ef4444; font-size: 0.7rem;"></i>
                        </div>
                        Lokasi Kegiatan
                    </span>
                    @php
                        $lokasiParts = [];
                        if (!empty($report->kelurahan)) $lokasiParts[] = $report->kelurahan;
                        if (!empty($report->kecamatan)) $lokasiParts[] = $report->kecamatan;
                        if (!empty($report->kabupaten)) $lokasiParts[] = $report->kabupaten;
                        if (!empty($report->provinsi)) $lokasiParts[] = $report->provinsi;
                        $lokasiLengkap = implode(', ', $lokasiParts);
                    @endphp
                    
                    @if($lokasiLengkap || !empty($report->alamat_lengkap))
                        <div class="dl-lokasi-box">
                            @if($lokasiLengkap)
                                <p class="dl-lokasi-hierarki">{{ $lokasiLengkap }}</p>
                            @endif
                            
                            @if(!empty($report->alamat_lengkap))
                                <p class="dl-lokasi-alamat">
                                    <i class="fas fa-location-arrow"></i>
                                    {{ $report->alamat_lengkap }}
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="dl-lokasi-kosong">
                            <i class="fas fa-info-circle"></i>
                            Informasi lokasi belum diisi untuk laporan ini
                        </div>
                    @endif
                </div>
                
                <div class="dl-info-item">
                    <span class="dl-info-label">Deskripsi Kegiatan</span>
                    <div class="dl-deskripsi-box">{!! nl2br(e($report->deskripsi)) !!}</div>
                </div>
                
                {{-- Dokumentasi Foto --}}
                @php
                    $fotosArray = is_string($report->fotos) ? json_decode($report->fotos, true) : $report->fotos;
                    $fotosArray = is_array($fotosArray) ? $fotosArray : [];
                @endphp
                @if(count($fotosArray) > 0)
                <div class="dl-info-item">
                    <span class="dl-info-label" style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 1.5rem; height: 1.5rem; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-camera" style="color: #0891b2; font-size: 0.7rem;"></i>
                        </div>
                        Dokumentasi Kegiatan ({{ count($fotosArray) }} foto)
                    </span>
                    <div class="dl-foto-grid">
                        @foreach($fotosArray as $index => $foto)
                        <div onclick="openGallery({{ $index }})" class="dl-foto-item">
                            <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi {{ $index + 1 }}">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN: Surat Terkait & Status --}}
        <div>
            @if($report->document)
            <div class="dl-card">
                <div class="dl-card-header dl-card-header-surat">
                    <h2>
                        <i class="fas fa-envelope"></i>
                        Surat Terkait
                    </h2>
                </div>
                <div class="dl-card-body">
                    <div class="dl-info-item">
                        <span class="dl-info-label">Nomor Agenda</span>
                        <p class="dl-info-value" style="font-family: monospace;">{{ $report->document->agenda_number }}</p>
                    </div>
                    <div class="dl-info-item">
                        <span class="dl-info-label">Judul Surat</span>
                        <p class="dl-info-value">{{ $report->document->subject ?? $report->document->title }}</p>
                    </div>
                    <div class="dl-info-item">
                        <span class="dl-info-label">Pengirim</span>
                        <p class="dl-info-value">{{ $report->document->sender }}</p>
                    </div>
                    <a href="{{ route('sidongan.documents.show', $report->document) }}" class="dl-btn dl-btn-link">
                        <i class="fas fa-eye"></i>
                        <span>Lihat Detail Surat</span>
                    </a>
                </div>
            </div>
            @endif

            {{-- Status Laporan --}}
            <div class="dl-card">
                <div class="dl-card-header dl-card-header-status">
                    <h2>
                        <i class="fas fa-info-circle"></i>
                        Status Laporan
                    </h2>
                </div>
                <div class="dl-card-body">
                    @php
                        $statusConfig = [
                            'draft' => ['class' => 'dl-status-draft', 'label' => 'Draft', 'color' => '#64748b', 'bg' => '#f1f5f9'],
                            'menunggu_verifikasi' => ['class' => 'dl-status-menunggu', 'label' => 'Menunggu Verifikasi', 'color' => '#d97706', 'bg' => '#fef3c7'],
                            'disetujui' => ['class' => 'dl-status-disetujui', 'label' => 'Disetujui', 'color' => '#059669', 'bg' => '#d1fae5'],
                            'ditolak' => ['class' => 'dl-status-ditolak', 'label' => 'Ditolak', 'color' => '#dc2626', 'bg' => '#fee2e2'],
                        ];
                        $status = $statusConfig[$report->status] ?? $statusConfig['draft'];
                    @endphp
                    <div class="dl-info-item">
                        <span class="dl-info-label" style="text-transform: uppercase; font-size: 0.75rem;">STATUS SAAT INI</span>
                        <div class="dl-status-badge {{ $status['class'] }}">
                            @if($report->status === 'disetujui')
                                <i class="fas fa-check-circle"></i>
                            @elseif($report->status === 'ditolak')
                                <i class="fas fa-times-circle"></i>
                            @elseif($report->status === 'menunggu_verifikasi')
                                <i class="fas fa-clock"></i>
                            @else
                                <i class="fas fa-file"></i>
                            @endif
                            {{ $status['label'] }}
                        </div>
                    </div>

                    <div class="dl-info-item">
                        <span class="dl-info-label">Dibuat oleh</span>
                        <p class="dl-info-value" style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 1.5rem; height: 1.5rem; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user" style="color: #0891b2; font-size: 0.7rem;"></i>
                            </div>
                            {{ $report->creator->name ?? 'Unknown' }}
                        </p>
                        @if($report->creator && $report->creator->sidongan_role)
                            @php
                                $roleLabels = [
                                    'ketua' => 'Ketua PKK',
                                    'sekretaris' => 'Sekretaris PKK',
                                    'bendahara' => 'Bendahara PKK',
                                    'staf_ahli_1' => 'Staf Ahli I',
                                    'staf_ahli_2' => 'Staf Ahli II',
                                    'pengurus_1' => 'Ketua Pengurus I',
                                    'pengurus_2' => 'Ketua Pengurus II',
                                    'pengurus_3' => 'Ketua Pengurus III',
                                    'pengurus_4' => 'Ketua Pengurus IV',
                                ];
                                $roleLabel = $roleLabels[$report->creator->sidongan_role] ?? ucfirst(str_replace('_', ' ', $report->creator->sidongan_role));
                            @endphp
                            <span class="dl-role-badge">{{ $roleLabel }}</span>
                        @endif
                    </div>
                    
                    <div class="dl-info-item">
                        <span class="dl-info-label">Tanggal Pembuatan</span>
                        <p class="dl-info-value" style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 1.5rem; height: 1.5rem; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clock" style="color: #0891b2; font-size: 0.7rem;"></i>
                            </div>
                            {{ $report->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                        </p>
                    </div>
                    
                    @if($report->catatan_verifikasi)
                    <div class="dl-catatan-section">
                        <span class="dl-info-label">Catatan Verifikasi</span>
                        <div class="dl-catatan-box" style="background: {{ $report->status === 'disetujui' ? '#f0fdf4' : ($report->status === 'ditolak' ? '#fef2f2' : '#fff7ed') }}; border-left: 3px solid {{ $report->status === 'disetujui' ? '#10b981' : ($report->status === 'ditolak' ? '#ef4444' : '#f59e0b') }};">
                            {{ $report->catatan_verifikasi }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL GALLERY --}}
<div id="galleryOverlay" class="dl-gallery-overlay" onclick="closeGallery(event)">
    <button class="dl-gallery-close" onclick="closeGallery()">
        <i class="fas fa-times"></i>
    </button>
    
    <div class="dl-gallery-container" onclick="event.stopPropagation()">
        <div class="dl-gallery-image-wrapper">
            <img id="galleryImage" class="dl-gallery-image" src="" alt="Dokumentasi">
        </div>
        
        <button class="dl-gallery-nav prev" onclick="navigateGallery(-1)">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="dl-gallery-nav next" onclick="navigateGallery(1)">
            <i class="fas fa-chevron-right"></i>
        </button>
        
        <div class="dl-gallery-bottom-bar">
            <span id="galleryCounter" class="dl-gallery-counter">1 / 1</span>
            <a id="galleryDownload" class="dl-gallery-download-btn" href="" download>
                <i class="fas fa-download"></i>
                <span>Unduh Foto</span>
            </a>
        </div>
        
        <div id="galleryThumbnails" class="dl-gallery-thumbnails"></div>
    </div>
</div>

<script>
    const galleryFotos = @json($fotosArray ?? []);
    let currentIndex = 0;
    let isAnimating = false;
    
    function openGallery(index) {
        currentIndex = index;
        updateGalleryImage('fade-in');
        updateGalleryUI(); 
        const overlay = document.getElementById('galleryOverlay');
        overlay.style.display = 'flex';
        setTimeout(() => {
            overlay.style.opacity = '1';
        }, 10);
        document.body.style.overflow = 'hidden';
    }
    
    function closeGallery(event) {
        if (event && event.target !== document.getElementById('galleryOverlay')) return;
        const overlay = document.getElementById('galleryOverlay');
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.style.display = 'none';
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
            img.className = 'dl-gallery-image ' + animClass;
            
            setTimeout(() => {
                img.style.opacity = '1';
                img.style.transform = 'translateX(0) scale(1)';
            }, 50);
            
            updateGalleryUI();
            
            setTimeout(() => {
                isAnimating = false;
                img.className = 'dl-gallery-image';
            }, 400);
        }, 200);
    }
    
    function updateGalleryImage(animClass = 'fade-in') {
        const img = document.getElementById('galleryImage');
        img.src = '{{ asset("storage") }}/' + galleryFotos[currentIndex];
        img.className = 'dl-gallery-image ' + animClass;
        updateGalleryUI();
    }
    
    function updateGalleryUI() {
        document.getElementById('galleryCounter').textContent = (currentIndex + 1) + ' / ' + galleryFotos.length;
        document.getElementById('galleryDownload').href = '{{ asset("storage") }}/' + galleryFotos[currentIndex];
        
        const prevBtn = document.querySelector('.dl-gallery-nav.prev');
        const nextBtn = document.querySelector('.dl-gallery-nav.next');
        
        if (galleryFotos.length <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
        
        updateThumbnails();
    }
    
    function updateThumbnails() {
        const container = document.getElementById('galleryThumbnails');
        container.innerHTML = '';
        galleryFotos.forEach((foto, index) => {
            const thumb = document.createElement('div');
            thumb.className = 'dl-gallery-thumb' + (index === currentIndex ? ' active' : '');
            thumb.innerHTML = '<img src="{{ asset("storage") }}/' + foto + '" alt="Thumb">';
            thumb.onclick = () => {
                if (index !== currentIndex) {
                    navigateGallery(index - currentIndex);
                }
            };
            container.appendChild(thumb);
        });
    }
    
    document.addEventListener('keydown', (e) => {
        const overlay = document.getElementById('galleryOverlay');
        if (!overlay || overlay.style.display !== 'flex') return;
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowLeft') navigateGallery(-1);
        if (e.key === 'ArrowRight') navigateGallery(1);
    });
</script>
@endsection