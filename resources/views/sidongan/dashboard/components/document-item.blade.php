{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@php
    $reports = $doc->activityReports ?? collect();
    $latestReport = $reports->first();
    $hasReport = $reports->count() > 0;
    
    // Ambil data disposisi
    $disposisiData = is_string($doc->disposisi_data) ? json_decode($doc->disposisi_data, true) : $doc->disposisi_data;
    
    // Satu surat = SATU laporan: status ditentukan laporan terakhir surat ini
    // (dibuat oleh siapa pun dari role tujuan disposisi).
    $laporanDitolak = $latestReport && $latestReport->status === 'ditolak';
    
    // Tentukan status label
    if ($doc->status === 'menunggu_disposisi') {
        $statusLabel = 'Menunggu Disposisi';
        $statusColor = '#fef3c7';
        $statusTextColor = '#92400e';
        $statusIcon = 'fa-hourglass-half';
    } elseif ($doc->status === 'berjalan') {
        if ($laporanDitolak) {
            $statusLabel = 'Perlu Laporan Ulang';
            $statusColor = '#fee2e2';
            $statusTextColor = '#991b1b';
            $statusIcon = 'fa-times-circle';
        } elseif ($latestReport && $latestReport->status === 'menunggu_verifikasi') {
            $statusLabel = 'Menunggu Verifikasi';
            $statusColor = '#dbeafe';
            $statusTextColor = '#1e40af';
            $statusIcon = 'fa-clock';
        } elseif ($hasReport) {
            $statusLabel = 'Sudah Dilaporkan';
            $statusColor = '#fef3c7';
            $statusTextColor = '#92400e';
            $statusIcon = 'fa-hourglass-half';
        } else {
            $statusLabel = 'Belum Dilaporkan';
            $statusColor = '#f1f5f9';
            $statusTextColor = '#475569';
            $statusIcon = 'fa-file-circle-xmark';
        }
    } elseif ($doc->status === 'selesai') {
        $statusLabel = 'Selesai';
        $statusColor = '#d1fae5';
        $statusTextColor = '#065f46';
        $statusIcon = 'fa-check-circle';
    } elseif ($doc->status === 'diarsipkan') {
        $statusLabel = 'Diarsipkan';
        $statusColor = '#f3e8ff';
        $statusTextColor = '#7c3aed';
        $statusIcon = 'fa-archive';
    } else {
        $statusLabel = ucfirst(str_replace('_', ' ', $doc->status));
        $statusColor = '#f1f5f9';
        $statusTextColor = '#475569';
        $statusIcon = 'fa-circle';
    }
@endphp

<a href="{{ route('sidongan.documents.show', $doc) }}" class="document-item">
    <div class="document-item-content">
        <div class="document-item-main">
            <div class="document-item-meta">
                <span class="document-agenda">{{ $doc->agenda_number ?? '-' }}</span>
                <span class="document-status" style="background: {{ $statusColor }}; color: {{ $statusTextColor }};">
                    <i class="fas {{ $statusIcon }}"></i>
                    {{ $statusLabel }}
                </span>
            </div>
            <h4 class="document-title">{{ Str::limit($doc->subject ?? $doc->title, 70) }}</h4>
            <div class="document-info">
                <span>
                    <i class="fas fa-user"></i>
                    {{ $doc->sender ?? '-' }}
                </span>
                <span>
                    <i class="fas fa-calendar"></i>
                    {{ $doc->document_date ? $doc->document_date->locale('id')->translatedFormat('d F Y') : '-' }}
                </span>
            </div>
            
            @if($laporanDitolak)
                <div class="document-alert alert-danger">
                    <div class="alert-author">
                        <i class="fas fa-times-circle"></i>
                        {{ $latestReport->creator->name ?? 'Unknown' }} - Laporan Ditolak
                    </div>
                    @if($latestReport->catatan_verifikasi)
                        <div class="alert-message">
                            "{{ Str::limit($latestReport->catatan_verifikasi, 50) }}"
                        </div>
                    @endif
                </div>
            @elseif($doc->status === 'berjalan' && $doc->disposisi_data && $latestReport)
                <div class="document-alert alert-info">
                    <div class="alert-section">
                        <div class="alert-label">Laporan Kegiatan:</div>
                        <div class="alert-user">
                            <i class="fas fa-file-alt" style="color: #3b82f6;"></i>
                            {{ $latestReport->creator->name ?? 'Unknown' }}
                            <span class="report-status">
                                @if($latestReport->status === 'menunggu_verifikasi')
                                    (Menunggu Verifikasi)
                                @elseif($latestReport->status === 'disetujui')
                                    (Disetujui)
                                @elseif($latestReport->status === 'ditolak')
                                    (Ditolak)
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="document-item-arrow">
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
</a>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-components-document-item.css') }}">
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
