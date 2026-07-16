@php
    $reports = $doc->activityReports ?? collect();
    $latestReport = $reports->first();
    $rejectedReport = $reports->where('status', 'ditolak')->first();
    $hasReport = $reports->count() > 0;
    
    // Ambil data disposisi
    $disposisiData = is_string($doc->disposisi_data) ? json_decode($doc->disposisi_data, true) : $doc->disposisi_data;
    $targetRoles = $disposisiData['target_roles'] ?? [];
    
    // Mapping role ke nama user
    $targetUsers = [];
    if (!empty($targetRoles)) {
        $targetUsers = \App\Models\User::whereIn('sidongan_role', $targetRoles)->get();
    }
    
    // Cek siapa yang sudah lapor dan siapa yang belum
    $reportedUsers = [];
    $unreportedUsers = [];
    
    foreach ($targetUsers as $user) {
        $userReport = $reports->where('created_by', $user->id)->first();
        if ($userReport) {
            $reportedUsers[] = [
                'user' => $user,
                'report' => $userReport,
                'status' => $userReport->status
            ];
        } else {
            $unreportedUsers[] = $user;
        }
    }
    
    $allReported = count($unreportedUsers) === 0 && count($reportedUsers) > 0;
    $partialReported = count($reportedUsers) > 0 && count($unreportedUsers) > 0;
    
    // Tentukan status label
    if ($doc->status === 'menunggu_disposisi') {
        $statusLabel = 'Menunggu Disposisi';
        $statusColor = '#fef3c7';
        $statusTextColor = '#92400e';
        $statusIcon = 'fa-hourglass-half';
    } elseif ($doc->status === 'berjalan') {
        if ($rejectedReport) {
            $statusLabel = 'Perlu Laporan Ulang';
            $statusColor = '#fee2e2';
            $statusTextColor = '#991b1b';
            $statusIcon = 'fa-times-circle';
        } elseif ($allReported) {
            $statusLabel = 'Menunggu Verifikasi';
            $statusColor = '#dbeafe';
            $statusTextColor = '#1e40af';
            $statusIcon = 'fa-clock';
        } elseif ($partialReported) {
            $statusLabel = count($reportedUsers) . '/' . count($targetUsers) . ' Sudah Melapor';
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
            
            @if($rejectedReport)
                <div class="document-alert alert-danger">
                    <div class="alert-author">
                        <i class="fas fa-times-circle"></i>
                        {{ $rejectedReport->creator->name ?? 'Unknown' }} - Laporan Ditolak
                    </div>
                    @if($rejectedReport->catatan_verifikasi)
                        <div class="alert-message">
                            "{{ Str::limit($rejectedReport->catatan_verifikasi, 50) }}"
                        </div>
                    @endif
                </div>
            @elseif($doc->status === 'berjalan' && $doc->disposisi_data)
                @if($partialReported || $allReported)
                    <div class="document-alert alert-info">
                        <div class="alert-section">
                            <div class="alert-label">Sudah Melapor:</div>
                            @foreach($reportedUsers as $item)
                                <div class="alert-user">
                                    <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                    {{ $item['user']->name }}
                                    <span class="report-status">
                                        @if($item['status'] === 'menunggu_verifikasi')
                                            (Menunggu Verifikasi)
                                        @elseif($item['status'] === 'disetujui')
                                            (Disetujui)
                                        @elseif($item['status'] === 'ditolak')
                                            (Ditolak)
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        
                        @if(count($unreportedUsers) > 0)
                            <div class="alert-section" style="margin-top: 0.35rem;">
                                <div class="alert-label">Belum Melapor:</div>
                                @foreach($unreportedUsers as $user)
                                    <div class="alert-user">
                                        <i class="fas fa-clock" style="color: #f59e0b;"></i>
                                        {{ $user->name }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>
        <div class="document-item-arrow">
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
</a>

<style>
    .document-item {
        display: block;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        transition: background 0.2s;
    }

    .document-item:hover {
        background: #f8fafc;
    }

    .document-item-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }

    .document-item-main {
        flex: 1;
        min-width: 0;
    }

    .document-item-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
        flex-wrap: wrap;
    }

    .document-agenda {
        font-family: monospace;
        font-size: 0.75rem;
        font-weight: 600;
        color: #3b82f6;
        background: #eff6ff;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
    }

    .document-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .document-status i {
        font-size: 0.6rem;
    }

    .document-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 0.35rem 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .document-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.75rem;
        color: #64748b;
    }

    .document-info span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .document-info i {
        font-size: 0.65rem;
    }

    .document-alert {
        margin-top: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-left: 3px solid;
        border-radius: 0.25rem;
        font-size: 0.7rem;
    }

    .alert-danger {
        background: #fef2f2;
        border-color: #ef4444;
    }

    .alert-danger .alert-author {
        color: #991b1b;
        font-weight: 600;
        margin-bottom: 0.15rem;
    }

    .alert-danger .alert-message {
        color: #7f1d1d;
        font-style: italic;
    }

    .alert-info {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1e40af;
    }

    .alert-section {
        margin-bottom: 0.25rem;
    }

    .alert-label {
        font-weight: 700;
        margin-bottom: 0.15rem;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .alert-user {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.1rem 0;
        font-size: 0.7rem;
    }

    .alert-user i {
        font-size: 0.6rem;
    }

    .report-status {
        font-size: 0.65rem;
        font-style: italic;
        color: #64748b;
    }

    .document-item-arrow {
        flex-shrink: 0;
        color: #cbd5e1;
    }

    .document-item-arrow i {
        font-size: 0.875rem;
    }
</style>