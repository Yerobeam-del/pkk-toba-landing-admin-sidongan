{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Detail Surat - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    $disposisiData = is_string($document->disposisi_data ?? '') ? json_decode($document->disposisi_data, true) : $document->disposisi_data;
    $dispo = $disposisiData;
    
    $statusConfig = [
        'menunggu_disposisi' => ['class' => 'ds-status-menunggu_disposisi', 'label' => 'Menunggu Disposisi Ketua'],
        'berjalan' => ['class' => 'ds-status-berjalan', 'label' => 'Sedang Berjalan'],
        'menunggu_verifikasi' => ['class' => 'ds-status-menunggu_verifikasi', 'label' => 'Menunggu Verifikasi'],
        'selesai' => ['class' => 'ds-status-selesai', 'label' => 'Selesai'],
        'diarsipkan' => ['class' => 'ds-status-diarsipkan', 'label' => 'Diarsipkan'],
    ];
    $status = $statusConfig[$document->status] ?? ['class' => '', 'label' => $document->status];
    
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
    
    $userReceivedDisposisi = false;
    if (is_array($disposisiData) && isset($disposisiData['target_roles']) && $document->status === 'berjalan') {
        $userReceivedDisposisi = in_array($currentUser->sidongan_role, $disposisiData['target_roles']);
    }

    $existingReport = null;
    $canLaporKegiatan = false;

    if ($userReceivedDisposisi) {
        // Satu surat = SATU laporan: cek apakah sudah ada laporan apa pun.
        $existingReport = \App\Models\ActivityReport::where('document_id', $document->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$existingReport || $existingReport->status === 'ditolak') {
            $canLaporKegiatan = true;
        }
    }
@endphp

<link rel="stylesheet" href="{{ asset('assets/sidongan/css/detail-surat.css') }}">

<div class="ds-container">
    {{-- Header --}}
    <div class="ds-header">
        <div class="ds-header-top">
            <div>
                <h1>Detail Surat</h1>
                <p>Informasi lengkap surat masuk</p>
            </div>
            
            <div class="ds-header-actions sd-header-actions">
                @php
                    // Ambil URL kembali dari session
                    $backUrl = session('document_back_url');
                    
                    // Validasi dan fallback
                    if (!$backUrl || 
                        str_contains($backUrl, '/disposisi/form') || 
                        str_contains($backUrl, '/disposisi-print') ||
                        str_contains($backUrl, '/create') ||
                        str_contains($backUrl, '/edit')) {
                        // Gunakan parameter 'from' dari URL jika ada
                        if (request('from') && 
                            !str_contains(request('from'), '/disposisi/form') &&
                            !str_contains(request('from'), '/disposisi-print')) {
                            $backUrl = request('from');
                        } else {
                            // Fallback ke daftar surat
                            $backUrl = route('sidongan.documents.index');
                        }
                    }
                @endphp

                <a href="{{ $backUrl }}" class="ds-btn ds-btn-back sd-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                
                @if($currentUser && $currentUser->hasSidonganRole('sekretaris') && $document->status === 'menunggu_disposisi')
                <a href="{{ route('sidongan.documents.edit', $document) }}?from={{ urlencode(url()->current()) }}" class="ds-btn ds-btn-edit">
                    <i class="fas fa-edit"></i>
                    <span>Edit Surat</span>
                </a>
                @endif
                
                @if($currentUser && $currentUser->hasSidonganRole('ketua') && $document->status === 'menunggu_disposisi')
                <a href="{{ route('sidongan.disposisi.form', $document) }}?from={{ urlencode(url()->current()) }}" class="ds-btn ds-btn-disposisi">
                    <i class="fas fa-paper-plane"></i>
                    <span>Disposisi</span>
                </a>
                @endif

                @if(is_array($disposisiData) && isset($disposisiData['action']) && ($currentUser->hasSidonganRole('sekretaris') || $currentUser->hasSidonganRole('ketua')))
                <a href="{{ route('sidongan.documents.disposisi-print', $document) }}?from={{ urlencode(url()->current()) }}" class="ds-btn ds-btn-print">
                    <i class="fas fa-print"></i>
                    <span>Cetak Disposisi</span>
                </a>
                @endif

                @if($canLaporKegiatan)
                <a href="{{ route('sidongan.lapor_kegiatan.create', ['document_id' => $document->id]) }}?from={{ urlencode(url()->current()) }}" class="ds-btn ds-btn-lapor">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Lapor Kegiatan</span>
                </a>
                @endif

                @php
                    $canArchive = false;
                    $dispoData = is_string($document->disposisi_data) 
                        ? json_decode($document->disposisi_data, true) 
                        : $document->disposisi_data;

                    // KONDISI 1: Jika tindak lanjut disposisi adalah "Di Arsipkan"
                    $isArsipDisposition = false;
                    if (isset($dispoData['action'])) {
                        $actionLower = strtolower($dispoData['action']);
                        if (strpos($actionLower, 'arsip') !== false) {
                            $isArsipDisposition = true;
                        }
                    }

                    if ($isArsipDisposition && $document->status === 'berjalan') {
                        // Langsung bisa arsip jika disposisi "Di Arsipkan" dan status berjalan
                        $canArchive = true;
                    } elseif ($document->status === 'selesai') {
                        // KONDISI 2: Status selesai berarti laporan terakhir sudah
                        // disetujui ketua (satu surat = satu laporan).
                        $latestReport = $document->activityReports()
                            ->orderBy('created_at', 'desc')
                            ->first();
                        $canArchive = $latestReport && $latestReport->status === 'disetujui';
                    }
                @endphp

                @if($currentUser && $currentUser->hasSidonganRole('sekretaris') && $canArchive)
                <form id="archiveConfirmForm" action="{{ route('sidongan.documents.archive', $document) }}" method="POST" class="ds-form-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="ds-btn ds-btn-archive">
                        <i class="fas fa-archive"></i>
                        <span>Arsipkan</span>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Status & Subject --}}
    <div class="ds-status-section">
        <div class="ds-status-badges">
            <span class="ds-badge-agenda">{{ $document->agenda_number }}</span>
            <span class="ds-badge-status {{ $status['class'] }}">{{ $status['label'] }}</span>
        </div>
        <h2 class="ds-subject-title">{{ $document->subject ?? $document->title }}</h2>
    </div>

    {{-- Data Surat & Agenda --}}
    <div class="ds-card">
        <div class="ds-card-header ds-card-header-blue">
            <h3>
                <i class="fas fa-file-alt"></i>
                Informasi Surat
            </h3>
        </div>
        <div class="ds-card-body">
            <div class="ds-info-grid">
                {{-- Data Surat --}}
                <div>
                    <h4 class="ds-section-title">
                        <i class="fas fa-envelope u-a25"></i>
                        Data Surat
                    </h4>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Pengirim</span>
                        <span class="ds-info-value">{{ $document->sender ?? '-' }}</span>
                    </div>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Nomor Surat</span>
                        <span class="ds-info-value">{{ $document->document_number ?? '-' }}</span>
                    </div>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Tanggal Surat</span>
                        <span class="ds-info-value">
                            {{ $document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Perihal</span>
                        <span class="ds-info-value">{{ $document->subject ?? $document->title }}</span>
                    </div>
                </div>

                {{-- Data Agenda --}}
                <div>
                    <h4 class="ds-section-title">
                        <i class="fas fa-clipboard-list u-a25"></i>
                        Data Agenda
                    </h4>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Nomor Agenda</span>
                        <span class="ds-info-value ds-info-value-mono">{{ $document->agenda_number ?? '-' }}</span>
                    </div>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Tanggal Agenda</span>
                        <span class="ds-info-value">
                            {{ $document->created_at ? \Carbon\Carbon::parse($document->created_at)->locale('id')->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                    <div class="ds-info-row">
                        <span class="ds-info-label">Dibuat oleh</span>
                        <span class="ds-info-value">{{ $document->creator->name ?? 'Sekretaris PKK' }}</span>
                    </div>
                    <div class="ds-saran-wrapper">
                        <span class="ds-info-label">Saran Sekretaris:</span>
                        <div class="ds-saran-box">
                            {{ $document->suggestion ?? $document->description ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lampiran Surat --}}
    <div class="ds-card">
        <div class="ds-card-header ds-card-header-green">
            <h3>
                <i class="fas fa-paperclip"></i>
                Lampiran Surat
            </h3>
        </div>
        <div class="ds-card-body">
            @if($document->file_path)
            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" rel="noopener noreferrer" class="ds-lampiran-box">
                <div class="ds-lampiran-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="ds-lampiran-info">
                    <p class="ds-lampiran-name">{{ $document->file_name }}</p>
                    <p class="ds-lampiran-size">
                        {{ $document->file_size ? round($document->file_size / 1024, 2) . ' KB' : 'File surat' }}
                    </p>
                </div>
                <div class="ds-lampiran-btn">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Buka</span>
                </div>
            </a>
            @else
            <div class="ds-no-attachment">
                <i class="fas fa-paperclip ds-no-attachment-icon"></i>
                <p class="ds-no-attachment-text">Tidak ada lampiran file untuk surat ini.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Disposisi Ketua --}}
    @if(is_array($dispo) && isset($dispo['action']))
    <div class="ds-card">
        <div class="ds-card-header ds-card-header-orange">
            <h3>
                <i class="fas fa-share-square ds-dispo-icon-orange"></i>
                Disposisi Ketua
            </h3>
        </div>
        <div class="ds-card-body">
            <div class="ds-disposisi-grid">
                <div class="ds-disposisi-row">
                    <div>
                        <span class="ds-disposisi-label">Didisposisikan kepada:</span>
                        <div class="ds-disposisi-targets">
                            @foreach($dispo['target_roles'] as $role)
                            <span class="ds-disposisi-badge">
                                <i class="fas fa-user-group"></i>
                                {{ $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <span class="ds-disposisi-label">Tindakan:</span>
                        <span class="ds-disposisi-action">{{ $dispo['action'] }}</span>
                    </div>
                </div>
                
                @if(isset($dispo['comment']) && $dispo['comment'])
                <div class="ds-disposisi-comment">
                    <span class="ds-disposisi-comment-label">Komentar Disposisi:</span>
                    <p class="ds-disposisi-comment-text">"{{ $dispo['comment'] }}"</p>
                </div>
                @endif
                
                <div class="ds-disposisi-footer">
                    @php
                        $disposedBy = null;
                        if (isset($dispo['disposed_by'])) {
                            $disposedBy = \App\Models\User::find($dispo['disposed_by']);
                        }
                    @endphp
                    Didisposisikan oleh <strong>{{ $disposedBy->name ?? 'Ketua PKK' }}</strong> pada 
                    {{ isset($dispo['disposed_at']) ? \Carbon\Carbon::parse($dispo['disposed_at'])->locale('id')->translatedFormat('d F Y, H.i') : $document->updated_at->locale('id')->translatedFormat('d F Y, H.i') }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Laporan Kegiatan --}}
    @if(isset($activityReports) && $activityReports->count() > 0)
    <div class="ds-card">
        <div class="ds-card-header ds-card-header-blue">
            <h3>
                <i class="fas fa-clipboard-list"></i>
                Laporan Kegiatan
            </h3>
        </div>
        <div class="ds-card-body">
            @foreach($activityReports as $report)
                @php
                    $theme = [
                        'draft' => 'ds-laporan-card-draft',
                        'menunggu_verifikasi' => 'ds-laporan-card-menunggu_verifikasi',
                        'disetujui' => 'ds-laporan-card-disetujui',
                        'ditolak' => 'ds-laporan-card-ditolak',
                    ];
                    $labelMap = [
                        'draft' => 'Draft',
                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                    ];
                    $cardClass = $theme[$report->status] ?? 'ds-laporan-card-menunggu_verifikasi';
                    $statusLabel = $labelMap[$report->status] ?? 'Menunggu Verifikasi';
                @endphp
                
                <div class="ds-laporan-card {{ $cardClass }}">
                    <div class="ds-laporan-header">
                        <div class="ds-laporan-creator">
                            <div class="ds-laporan-avatar" style="overflow: hidden;">
                                @if($report->creator && $report->creator->avatar)
                                    <img src="{{ asset('storage/' . $report->creator->avatar) }}" 
                                         alt="{{ $report->creator->name }}"
                                         class="ds-avatar-img"
                                         onerror="this.style.display='none';this.parentElement.innerHTML='{{ strtoupper(substr($report->creator->name ?? 'U', 0, 1)) }}';this.parentElement.style.display='flex';this.parentElement.style.alignItems='center';this.parentElement.style.justifyContent='center';this.parentElement.style.color='white';this.parentElement.style.fontWeight='700';this.parentElement.style.fontSize='1rem';this.parentElement.style.overflow='';">
                                @else
                                    {{ strtoupper(substr($report->creator->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="ds-laporan-name u-flex-center-gap-2-wrap">
                                    {{ $report->creator->name ?? 'Sekretaris PKK' }}
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
                                        <span class="ds-role-badge">
                                            {{ $roleLabel }}
                                        </span>
                                    @endif
                                </p>
                                <p class="ds-laporan-date">{{ $report->created_at->locale('id')->translatedFormat('d F Y, H.i') }}</p>
                            </div>
                        </div>
                        <span class="ds-laporan-status-badge">{{ $statusLabel }}</span>
                    </div>
                    
                    <div class="ds-laporan-info-grid">
                        <div>
                            <span class="ds-laporan-info-label">Tanggal Kegiatan:</span>
                            <p class="ds-laporan-info-value">
                                @if($report->kegiatan_tanggal)
                                    {{ \Carbon\Carbon::parse($report->kegiatan_tanggal)->locale('id')->translatedFormat('d F Y') }}
                                @else
                                    <span class="ds-text-muted">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="ds-laporan-info-label">Waktu Kegiatan:</span>
                            <p class="ds-laporan-info-value">
                                @if($report->start_time && $report->end_time)
                                    {{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}
                                @else
                                    <span class="ds-text-muted">Waktu tidak tersedia</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    @php
                        $lokasiParts = [];
                        if (!empty($report->kelurahan)) $lokasiParts[] = $report->kelurahan;
                        if (!empty($report->kecamatan)) $lokasiParts[] = $report->kecamatan;
                        if (!empty($report->kabupaten)) $lokasiParts[] = $report->kabupaten;
                        if (!empty($report->provinsi)) $lokasiParts[] = $report->provinsi;
                        $lokasiLengkap = implode(', ', $lokasiParts);
                    @endphp
                    
                    @if($lokasiLengkap || !empty($report->alamat_lengkap))
                    <div class="ds-laporan-lokasi-box">
                        @if($lokasiLengkap)
                            <p class="ds-laporan-lokasi-hierarki">
                                <i class="fas fa-map-marker-alt ds-map-icon"></i>
                                {{ $lokasiLengkap }}
                            </p>
                        @endif
                        @if(!empty($report->alamat_lengkap))
                            <p class="ds-laporan-lokasi-alamat">
                                <i class="fas fa-location-arrow"></i>
                                {{ $report->alamat_lengkap }}
                            </p>
                        @endif
                    </div>
                    @endif
                    
                    <div class="ds-laporan-deskripsi">
                        {{ $report->deskripsi }}
                    </div>
                    
                    @php
                        $fotos = is_string($report->fotos ?? '') ? json_decode($report->fotos, true) : $report->fotos;
                        $fotosArray = is_array($fotos) ? $fotos : [];
                    @endphp
                    @if(count($fotosArray) > 0)
                    <div class="u-mb-5">
                        <span class="ds-laporan-info-label ds-foto-label">
                            <i class="fas fa-camera u-mr-1"></i>
                            Dokumentasi ({{ count($fotosArray) }} foto):
                        </span>
                        <div class="ds-laporan-foto-grid">
                            @foreach($fotosArray as $index => $foto)
                            <div data-report-id="{{ $report->id }}" data-index="{{ $index }}" class="ds-laporan-foto-item">
                                <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <a href="{{ route('sidongan.lapor_kegiatan.show', $report->id) }}" class="ds-laporan-detail-btn">
                        <i class="fas fa-eye"></i>
                        <span>Lihat Detail</span>
                    </a>
                    <div class="ds-clearfix"></div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Alur Kegiatan - Timeline --}}
    <div class="ds-card">
        <div class="ds-card-header ds-card-header-blue">
            <h3>
                <i class="fas fa-stream"></i>
                Alur Kegiatan
            </h3>
        </div>
        <div class="ds-card-body">
            <div class="ds-timeline">
                @php
                    $reports = $activityReports ?? collect();
                    $hasDisposisi = !empty($document->disposisi_data);
                    
                    $timelineEvents = [];
                    
                    // 1. Surat Dibuat
                    $timelineEvents[] = [
                        'type' => 'created',
                        'timestamp' => \Carbon\Carbon::parse($document->created_at),
                        'data' => $document,
                    ];
                    
                    // 2. Disposisi
                    if ($hasDisposisi) {
                        $disposedAt = isset($dispo['disposed_at']) 
                            ? \Carbon\Carbon::parse($dispo['disposed_at']) 
                            : \Carbon\Carbon::parse($document->updated_at);
                        
                        $timelineEvents[] = [
                            'type' => 'disposisi',
                            'timestamp' => $disposedAt,
                            'data' => $dispo,
                        ];
                    }
                    
                    // 3. Laporan Kegiatan & Verifikasi
                    foreach ($reports as $report) {
                        $timelineEvents[] = [
                            'type' => 'laporan',
                            'timestamp' => \Carbon\Carbon::parse($report->created_at),
                            'data' => $report,
                            'subtype' => 'create',
                        ];
                        
                        if (in_array($report->status ?? '', ['disetujui', 'ditolak'])) {
                            $verifAt = $report->verified_at 
                                ? \Carbon\Carbon::parse($report->verified_at) 
                                : \Carbon\Carbon::parse($report->updated_at);
                            
                            $timelineEvents[] = [
                                'type' => 'laporan',
                                'timestamp' => $verifAt,
                                'data' => $report,
                                'subtype' => 'verify',
                            ];
                        }
                    }
                    
                    // 4. ✅ PENGARSIPAN (BARU!)
                    if ($document->status === 'diarsipkan') {
                        $archivedAt = $document->updated_at;
                        
                        $timelineEvents[] = [
                            'type' => 'archive',
                            'timestamp' => $archivedAt,
                            'data' => $document,
                        ];
                    }
                    
                    usort($timelineEvents, function($a, $b) {
                        return $a['timestamp']->timestamp <=> $b['timestamp']->timestamp;
                    });
                    
                    $totalItems = count($timelineEvents);
                    $currentItem = 0;
                @endphp
                
                @foreach($timelineEvents as $event)
                    @php $currentItem++; @endphp
                    
                    @if($event['type'] === 'created')
                        <div class="ds-timeline-item">
                            <div class="ds-timeline-icon-col">
                                <div class="ds-timeline-icon ds-timeline-icon-blue">
                                    @if($document->creator && $document->creator->avatar)
                                        <img src="{{ asset('storage/' . $document->creator->avatar) }}" 
                                             alt="{{ $document->creator->name }}" 
                                             class="ds-timeline-avatar-img"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        <div class="ds-avatar-fallback ds-avatar-fallback-blue">
                                            {{ strtoupper(substr($document->creator->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @else
                                        <i class="fas fa-user"></i>
                                    @endif
                                </div>
                                @if($currentItem < $totalItems)
                                    <div class="ds-timeline-line u-bg-slate-200"></div>
                                @endif
                            </div>
                            <div class="ds-timeline-content">
                                <div class="ds-timeline-header">
                                    <div class="u-flex-center-gap-2-wrap">
                                        <h4 class="ds-timeline-title u-m-0">{{ $document->creator->name ?? 'Sekretaris PKK' }}</h4>
                                        @if($document->creator && $document->creator->sidongan_role)
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
                                                $roleLabel = $roleLabels[$document->creator->sidongan_role] ?? ucfirst(str_replace('_', ' ', $document->creator->sidongan_role));
                                            @endphp
                                            <span class="ds-timeline-role-badge">{{ $roleLabel }}</span>
                                        @endif
                                    </div>
                                    <span class="ds-timeline-date">{{ $event['timestamp']->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                                </div>
                                <p class="ds-timeline-desc">
                                    Membuat agenda dan mengupload surat dari {{ $document->sender ?? 'Pengirim' }}
                                </p>
                            </div>
                        </div>
                    
                    @elseif($event['type'] === 'disposisi')
                        <div class="ds-timeline-item">
                            <div class="ds-timeline-icon-col">
                                <div class="ds-timeline-icon ds-timeline-icon-orange">
                                    @php
                                        $disposedByUser = null;
                                        if (isset($event['data']['disposed_by'])) {
                                            $disposedByUser = \App\Models\User::find($event['data']['disposed_by']);
                                        }
                                    @endphp
                                    @if($disposedByUser && $disposedByUser->avatar)
                                        <img src="{{ asset('storage/' . $disposedByUser->avatar) }}" 
                                             alt="{{ $disposedByUser->name }}" 
                                             class="ds-timeline-avatar-img"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        <div class="ds-avatar-fallback ds-avatar-fallback-orange">
                                            {{ strtoupper(substr($disposedByUser->name ?? 'K', 0, 1)) }}
                                        </div>
                                    @else
                                        <i class="fas fa-share-alt"></i>
                                    @endif
                                </div>
                                @if($currentItem < $totalItems)
                                    <div class="ds-timeline-line u-bg-slate-200"></div>
                                @endif
                            </div>
                            <div class="ds-timeline-content">
                                <div class="ds-timeline-header">
                                    <div class="u-flex-center-gap-2-wrap">
                                        <h4 class="ds-timeline-title u-m-0">{{ $disposedByUser->name ?? 'Ketua PKK' }}</h4>
                                        @if($disposedByUser && $disposedByUser->sidongan_role)
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
                                                $roleLabel = $roleLabels[$disposedByUser->sidongan_role] ?? ucfirst(str_replace('_', ' ', $disposedByUser->sidongan_role));
                                            @endphp
                                            <span class="ds-timeline-role-badge">{{ $roleLabel }}</span>
                                        @endif
                                    </div>
                                    <span class="ds-timeline-date">
                                        {{ $event['timestamp']->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </div>
                                <p class="ds-timeline-desc">
                                    Melakukan disposisi kepada:
                                    @if(isset($event['data']['target_roles']))
                                        @foreach($event['data']['target_roles'] as $role)
                                            <span class="ds-timeline-role-badge">
                                                {{ $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role)) }}
                                            </span>
                                        @endforeach
                                    @endif
                                </p>
                                @if(isset($event['data']['comment']) && $event['data']['comment'])
                                <p class="ds-timeline-quote">"{{ $event['data']['comment'] }}"</p>
                                @endif
                            </div>
                        </div>
                    
                    @elseif($event['type'] === 'laporan')
                        @php
                            $report = $event['data'];
                            $isVerified = $event['subtype'] === 'verify';
                            $verifStatus = $report->status ?? null;
                            $verifColor = $verifStatus === 'disetujui' ? '#10b981' : '#ef4444';
                            $verifIcon = $verifStatus === 'disetujui' ? 'check' : 'times';
                            $verifComment = $report->catatan_verifikasi ?? null;
                            
                            $timelineLokasiParts = [];
                            if ($report->kelurahan) $timelineLokasiParts[] = $report->kelurahan;
                            if ($report->kecamatan) $timelineLokasiParts[] = $report->kecamatan;
                            if ($report->kabupaten) $timelineLokasiParts[] = $report->kabupaten;
                            if ($report->provinsi) $timelineLokasiParts[] = $report->provinsi;
                            $timelineLokasi = implode(', ', $timelineLokasiParts);
                        @endphp
                        
                        @if(!$isVerified)
                            <div class="ds-timeline-item">
                                <div class="ds-timeline-icon-col">
                                    <div class="ds-timeline-icon ds-timeline-icon-green">
                                        @if($report->creator && $report->creator->avatar)
                                            <img src="{{ asset('storage/' . $report->creator->avatar) }}" 
                                                 alt="{{ $report->creator->name }}" 
                                                 class="ds-timeline-avatar-img"
                                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <div class="ds-avatar-fallback ds-avatar-fallback-green">
                                                {{ strtoupper(substr($report->creator->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @else
                                            <i class="fas fa-clipboard-list"></i>
                                        @endif
                                    </div>
                                    @if($currentItem < $totalItems)
                                        <div class="ds-timeline-line u-bg-slate-200"></div>
                                    @endif
                                </div>
                                <div class="ds-timeline-content">
                                    <div class="ds-timeline-header">
                                        <div class="u-flex-center-gap-2-wrap">
                                            <h4 class="ds-timeline-title u-m-0">
                                                {{ $report->creator->name ?? 'Sekretaris PKK' }}
                                            </h4>
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
                                                <span class="ds-timeline-role-badge">{{ $roleLabel }}</span>
                                            @endif
                                        </div>
                                        <span class="ds-timeline-date">
                                            {{ $event['timestamp']->locale('id')->translatedFormat('d F Y, H:i') }}
                                        </span>
                                    </div>
                                    <p class="ds-timeline-desc">
                                        Membuat laporan kegiatan: <strong>{{ $report->kegiatan_nama }}</strong>
                                    </p>
                                    @if($timelineLokasi)
                                    <p class="ds-timeline-meta">
                                        <i class="fas fa-map-marker-alt"></i>
                                        {{ $timelineLokasi }}
                                    </p>
                                    @endif
                                    @if($report->alamat_lengkap)
                                    <p class="ds-timeline-meta">
                                        <i class="fas fa-location-arrow"></i>
                                        {{ Str::limit($report->alamat_lengkap, 80) }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                    @else
                        {{-- VERIFIKASI DENGAN FOTO PROFIL DAN INFO LAPORAN YANG JELAS --}}
                        <div class="ds-timeline-item">
                            <div class="ds-timeline-icon-col">
                                <div class="ds-timeline-icon" style="background: {{ $verifColor }}; box-shadow: 0 0 0 4px {{ $verifColor }}30;">
                                    @php
                                        $verifier = null;
                                        if ($report->verified_by) {
                                            $verifier = \App\Models\User::find($report->verified_by);
                                        }
                                    @endphp
                                    @if($verifier && $verifier->avatar)
                                        <img src="{{ asset('storage/' . $verifier->avatar) }}" 
                                             alt="{{ $verifier->name }}" 
                                             class="ds-timeline-avatar-img"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        <div class="ds-avatar-fallback" style="display:none;background:{{ $verifColor }};">
                                            {{ strtoupper(substr($verifier->name ?? 'K', 0, 1)) }}
                                        </div>
                                    @else
                                        <i class="fas fa-{{ $verifIcon }}"></i>
                                    @endif
                                </div>
                                @if($currentItem < $totalItems)
                                    <div class="ds-timeline-line u-bg-slate-200"></div>
                                @endif
                            </div>
                            <div class="ds-timeline-content">
                                <div class="ds-timeline-header">
                                    <div class="u-flex-center-gap-2-wrap">
                                        <h4 class="ds-timeline-title u-m-0">
                                            {{ $verifier->name ?? 'Ketua PKK' }}
                                        </h4>
                                        @if($verifier && $verifier->sidongan_role)
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
                                                $roleLabel = $roleLabels[$verifier->sidongan_role] ?? ucfirst(str_replace('_', ' ', $verifier->sidongan_role));
                                            @endphp
                                            <span class="ds-timeline-role-badge">{{ $roleLabel }}</span>
                                        @endif
                                    </div>
                                    <span class="ds-timeline-date">
                                        {{ $event['timestamp']->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </div>
                                
                                @if($verifStatus === 'disetujui')
                                    <p class="ds-timeline-desc">
                                        <span class="ds-timeline-verif-badge ds-timeline-verif-badge-success">
                                            <i class="fas fa-check-circle"></i>
                                            Menyetujui laporan dari <strong>{{ $report->creator->name ?? 'Unknown' }}</strong>
                                        </span>
                                    </p>
                                    <div class="ds-verif-detail ds-verif-detail-success">
                                        <p class="ds-verif-detail-title">
                                            <strong>Kegiatan:</strong> {{ $report->kegiatan_nama }}
                                        </p>
                                        @if($report->kegiatan_tanggal)
                                        <p class="ds-verif-detail-meta">
                                            <i class="fas fa-calendar u-mr-1"></i>
                                            {{ \Carbon\Carbon::parse($report->kegiatan_tanggal)->locale('id')->translatedFormat('d F Y') }}
                                        </p>
                                        @endif
                                        @if($report->start_time && $report->end_time)
                                        <p class="ds-verif-detail-meta">
                                            <i class="fas fa-clock u-mr-1"></i>
                                            {{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}
                                        </p>
                                        @endif
                                    </div>
                                @elseif($verifStatus === 'ditolak')
                                    <p class="ds-timeline-desc">
                                        <span class="ds-timeline-verif-badge ds-timeline-verif-badge-danger">
                                            <i class="fas fa-times-circle"></i>
                                            Menolak laporan dari <strong>{{ $report->creator->name ?? 'Unknown' }}</strong>
                                        </span>
                                    </p>
                                    <div class="ds-verif-detail ds-verif-detail-danger">
                                        <p class="ds-verif-detail-title">
                                            <strong>Kegiatan:</strong> {{ $report->kegiatan_nama }}
                                        </p>
                                        @if($report->kegiatan_tanggal)
                                        <p class="ds-verif-detail-meta">
                                            <i class="fas fa-calendar u-mr-1"></i>
                                            {{ \Carbon\Carbon::parse($report->kegiatan_tanggal)->locale('id')->translatedFormat('d F Y') }}
                                        </p>
                                        @endif
                                        @if($report->start_time && $report->end_time)
                                        <p class="ds-verif-detail-meta">
                                            <i class="fas fa-clock u-mr-1"></i>
                                            {{ \Carbon\Carbon::parse($report->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($report->end_time)->format('H:i') }}
                                        </p>
                                        @endif
                                    </div>
                                @endif
                                
                                @if($verifComment)
                                <div class="ds-timeline-note {{ $verifStatus === 'disetujui' ? 'ds-timeline-note-success' : 'ds-timeline-note-danger' }}">
                                    "{{ $verifComment }}"
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @elseif($event['type'] === 'archive')
                        {{-- PENGARSIPAN SURAT --}}
                        <div class="ds-timeline-item">
                            <div class="ds-timeline-icon-col">
                                <div class="ds-timeline-icon ds-timeline-icon-purple">
                                    @if($document->creator && $document->creator->avatar)
                                        <img src="{{ asset('storage/' . $document->creator->avatar) }}" 
                                             alt="{{ $document->creator->name }}" 
                                             class="ds-timeline-avatar-img"
                                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                        <div class="ds-avatar-fallback ds-avatar-fallback-purple">
                                            {{ strtoupper(substr($document->creator->name ?? 'U', 0, 1)) }}
                                        </div>
                                    @else
                                        <i class="fas fa-archive"></i>
                                    @endif
                                </div>
                                @if($currentItem < $totalItems)
                                    <div class="ds-timeline-line u-bg-slate-200"></div>
                                @endif
                            </div>
                            <div class="ds-timeline-content">
                                <div class="ds-timeline-header">
                                    <div class="u-flex-center-gap-2-wrap">
                                        <h4 class="ds-timeline-title u-m-0">{{ $document->creator->name ?? 'Sekretaris PKK' }}</h4>
                                        @if($document->creator && $document->creator->sidongan_role)
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
                                                $roleLabel = $roleLabels[$document->creator->sidongan_role] ?? ucfirst(str_replace('_', ' ', $document->creator->sidongan_role));
                                            @endphp
                                            <span class="ds-timeline-role-badge">{{ $roleLabel }}</span>
                                        @endif
                                    </div>
                                    <span class="ds-timeline-date">
                                        {{ $event['timestamp']->locale('id')->translatedFormat('d F Y, H:i') }}
                                    </span>
                                </div>
                                <p class="ds-timeline-desc">
                                    <span class="ds-timeline-verif-badge ds-timeline-verif-badge-archive">
                                        <i class="fas fa-archive"></i>
                                        Mengarsipkan
                                    </span>
                                    Surat dengan nomor agenda <strong>{{ $document->agenda_number }}</strong>
                                </p>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- MODAL GALLERY --}}
@php
    $currentUser = auth()->guard('sidongan')->user();
    $disposisiData = is_string($document->disposisi_data ?? '') ? json_decode($document->disposisi_data, true) : $document->disposisi_data;
    $dispo = $disposisiData;
    
    $statusConfig = [
        'menunggu_disposisi' => ['class' => 'ds-status-menunggu_disposisi', 'label' => 'Menunggu Disposisi Ketua'],
        'berjalan' => ['class' => 'ds-status-berjalan', 'label' => 'Sedang Berjalan'],
        'menunggu_verifikasi' => ['class' => 'ds-status-menunggu_verifikasi', 'label' => 'Menunggu Verifikasi'],
        'selesai' => ['class' => 'ds-status-selesai', 'label' => 'Selesai'],
        'diarsipkan' => ['class' => 'ds-status-diarsipkan', 'label' => 'Diarsipkan'],
    ];
    $status = $statusConfig[$document->status] ?? ['class' => '', 'label' => $document->status];
    
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
    
    $userReceivedDisposisi = false;
    if (is_array($disposisiData) && isset($disposisiData['target_roles']) && $document->status === 'berjalan') {
        $userReceivedDisposisi = in_array($currentUser->sidongan_role, $disposisiData['target_roles']);
    }

    $existingReport = null;
    $canLaporKegiatan = false;

    if ($userReceivedDisposisi) {
        // Satu surat = SATU laporan: cek apakah sudah ada laporan apa pun.
        $existingReport = \App\Models\ActivityReport::where('document_id', $document->id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$existingReport || $existingReport->status === 'ditolak') {
            $canLaporKegiatan = true;
        }
    }
@endphp
<div id="galleryOverlay" class="dl-gallery-overlay"
     data-fotos='{{ json_encode($document->file_path ? [$document->file_path] : []) }}'
     data-report-fotos='{{ json_encode($allReportFotos) }}'
     data-storage="{{ asset('storage') }}">
    <button class="dl-gallery-close" >
        <i class="fas fa-times"></i>
    </button>
    
    <div class="dl-gallery-container sd-lightbox" >
        <div class="dl-gallery-image-wrapper">
            <img id="galleryImage" class="dl-gallery-image" src="" alt="Dokumentasi">
        </div>
        
        <button class="dl-gallery-nav prev" >
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="dl-gallery-nav next" >
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

<link rel="stylesheet" href="{{ asset('assets/sidongan/css/detail-laporan.css') }}">

@push('scripts')
    <script src="{{ asset('assets/sidongan/js/documents-show.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
