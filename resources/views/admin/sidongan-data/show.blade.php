@extends('admin.layouts.app')
@section('title', 'Detail Surat - ' . $document->agenda_number)
@section('page-title', 'Detail Surat')

@section('content')

<div style="margin-bottom:1.5rem">
    <a href="{{ route('admin.sidongan-data.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:var(--text-muted);text-decoration:none;margin-bottom:1rem;transition:color 0.2s"
       onmouseover="this.style.color='var(--primary)'"
       onmouseout="this.style.color='var(--text-muted)'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali ke Daftar Surat
    </a>
</div>

{{-- Info Surat --}}
<div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
        <div style="flex:1;min-width:250px">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;flex-wrap:wrap">
                @php
                    $statusConfig = [
                        'menunggu_disposisi' => ['bg' => 'rgba(234,179,8,0.1)', 'text' => '#92400e', 'label' => 'Menunggu Disposisi'],
                        'berjalan' => ['bg' => 'rgba(249,115,22,0.1)', 'text' => '#c2410c', 'label' => 'Berjalan'],
                        'menunggu_verifikasi' => ['bg' => 'rgba(99,102,241,0.1)', 'text' => '#4338ca', 'label' => 'Menunggu Verifikasi'],
                        'selesai' => ['bg' => 'rgba(34,197,94,0.1)', 'text' => '#166534', 'label' => 'Selesai'],
                        'diarsipkan' => ['bg' => 'rgba(168,85,247,0.1)', 'text' => '#6b21a8', 'label' => 'Diarsipkan'],
                    ];
                    $config = $statusConfig[$document->status] ?? ['bg' => 'rgba(100,116,139,0.1)', 'text' => '#475569', 'label' => $document->status];
                @endphp
                <span style="display:inline-block;padding:0.375rem 0.75rem;background:{{ $config['bg'] }};color:{{ $config['text'] }};border-radius:20px;font-size:0.75rem;font-weight:600">
                    {{ $config['label'] }}
                </span>
                <span style="font-family:monospace;font-size:0.875rem;font-weight:600;color:#3b82f6">{{ $document->agenda_number }}</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:var(--text-dark);margin:0 0 0.5rem 0;line-height:1.3">{{ $document->subject }}</h1>
            <p style="color:var(--text-muted);margin:0;font-size:0.95rem">{{ $document->document_number }}</p>
        </div>
        <div style="text-align:right">
            <a href="{{ route('sidongan.dashboard') }}" target="_blank" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.625rem 1.25rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;transition:all 0.2s;text-decoration:none"
               onmouseover="this.style.background='#0d9488';this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='var(--primary)';this.style.transform='translateY(0)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Buka SIDONGAN
            </a>
        </div>
    </div>
    
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:1.5rem">
        <div>
            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.25rem 0;text-transform:uppercase;letter-spacing:0.5px">Pengirim</p>
            <p style="font-weight:600;color:var(--text-dark);margin:0">{{ $document->sender }}</p>
        </div>
        <div>
            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.25rem 0;text-transform:uppercase;letter-spacing:0.5px">Tanggal Surat</p>
            <p style="font-weight:600;color:var(--text-dark);margin:0">{{ $document->document_date ? $document->document_date->format('d F Y') : '-' }}</p>
        </div>
        <div>
            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.25rem 0;text-transform:uppercase;letter-spacing:0.5px">Kategori</p>
            <p style="font-weight:600;color:var(--text-dark);margin:0">{{ $document->category->name ?? '-' }}</p>
        </div>
        <div>
            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.25rem 0;text-transform:uppercase;letter-spacing:0.5px">Dibuat Oleh</p>
            <p style="font-weight:600;color:var(--text-dark);margin:0">{{ $document->creator->name ?? '-' }}</p>
        </div>
    </div>
    
    @if($document->file_path)
    <div style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid rgba(0,0,0,0.06)">
        <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.75rem 0;text-transform:uppercase;letter-spacing:0.5px">File Surat</p>
        <a href="{{ Storage::disk('public')->url($document->file_path) }}" target="_blank" style="display:inline-flex;align-items:center;gap:0.5rem;color:var(--primary);text-decoration:none;font-weight:600"
           onmouseover="this.style.textDecoration='underline'"
           onmouseout="this.style.textDecoration='none'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Download File
        </a>
    </div>
    @endif
</div>

{{-- Timeline Detail --}}
<div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0 0 1.5rem 0;display:flex;align-items:center;gap:0.5rem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        Timeline Surat
    </h3>
    
    <div style="position:relative;padding-left:2rem">
        <div style="position:absolute;left:0;top:0;bottom:0;width:2px;background:linear-gradient(180deg,rgba(59,130,246,0.3),rgba(59,130,246,0.1))"></div>
        
        @php
            use Carbon\Carbon;
            
            // Kumpulkan semua timeline events
            $timelineEvents = [];
            
            // 1. Surat Dibuat
            $timelineEvents[] = [
                'type' => 'created',
                'title' => 'Surat Dibuat',
                'time' => Carbon::parse($document->created_at),
                'user' => $document->creator,
                'icon' => 'plus',
                'color' => '#3b82f6',
                'current' => false,
            ];
            
            // 2. Disposisi
            if ($document->disposisi_data) {
                $dispo = is_string($document->disposisi_data) 
                    ? json_decode($document->disposisi_data, true) 
                    : $document->disposisi_data;
                
                if (isset($dispo['disposed_by'])) {
                    $disposedBy = \App\Models\User::find($dispo['disposed_by']);
                    $timelineEvents[] = [
                        'type' => 'disposisi',
                        'title' => 'Disposisi Surat',
                        'time' => isset($dispo['disposed_at']) ? Carbon::parse($dispo['disposed_at']) : Carbon::parse($document->updated_at),
                        'user' => $disposedBy,
                        'icon' => 'share-alt',
                        'color' => '#f97316',
                        'current' => false,
                        'detail' => 'Didisposisikan ke: ' . implode(', ', array_map(function($role) {
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
                            return $rolesMap[$role] ?? ucfirst(str_replace('_', ' ', $role));
                        }, $dispo['target_roles'] ?? [])),
                    ];
                }
            }
            
            // 3. Laporan Kegiatan & Verifikasi
            $reports = $document->activityReports->sortBy('created_at');
            foreach ($reports as $report) {
                // Laporan dibuat
                $timelineEvents[] = [
                    'type' => 'laporan',
                    'title' => 'Laporan Dibuat',
                    'time' => Carbon::parse($report->created_at),
                    'user' => $report->creator,
                    'icon' => 'clipboard-list',
                    'color' => '#22c55e',
                    'current' => false,
                    'detail' => $report->kegiatan_nama ?? 'Kegiatan',
                ];
                
                // Verifikasi
                if ($report->status === 'disetujui') {
                    $verifier = $report->verified_by ? \App\Models\User::find($report->verified_by) : null;
                    $timelineEvents[] = [
                        'type' => 'verifikasi',
                        'title' => 'Laporan Disetujui',
                        'time' => $report->verified_at ? Carbon::parse($report->verified_at) : Carbon::parse($report->updated_at),
                        'user' => $verifier,
                        'icon' => 'check-circle',
                        'color' => '#10b981',
                        'current' => false,
                        'detail' => 'Oleh: ' . ($verifier ? $verifier->name : 'Ketua PKK'),
                    ];
                } elseif ($report->status === 'ditolak') {
                    $verifier = $report->verified_by ? \App\Models\User::find($report->verified_by) : null;
                    $timelineEvents[] = [
                        'type' => 'verifikasi',
                        'title' => 'Laporan Ditolak',
                        'time' => $report->verified_at ? Carbon::parse($report->verified_at) : Carbon::parse($report->updated_at),
                        'user' => $verifier,
                        'icon' => 'times-circle',
                        'color' => '#ef4444',
                        'current' => false,
                        'detail' => 'Oleh: ' . ($verifier ? $verifier->name : 'Ketua PKK') . 
                                   ($report->catatan_verifikasi ? ' - "' . $report->catatan_verifikasi . '"' : ''),
                    ];
                }
            }
            
            // Urutkan berdasarkan waktu
            usort($timelineEvents, function($a, $b) {
                return $a['time']->timestamp <=> $b['time']->timestamp;
            });
            
            // Set current status
            $lastEvent = end($timelineEvents);
            $lastEvent['current'] = true;
        @endphp
        
        @foreach($timelineEvents as $index => $event)
        <div style="margin-bottom:1.5rem;position:relative">
            <div style="position:absolute;left:-0.375rem;width:0.75rem;height:0.75rem;background:{{ $event['current'] ? '#22c55e' : $event['color'] }};border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 2px {{ $event['current'] ? 'rgba(34,197,94,0.2)' : 'rgba(203,213,225,0.2)' }}"></div>
            <div style="margin-left:1rem">
                <p style="font-weight:600;color:var(--text-dark);margin:0 0 0.25rem 0">
                    {{ $event['title'] }}
                    @if($event['current'])
                        <span style="display:inline-block;padding:0.2rem 0.5rem;background:rgba(34,197,94,0.1);color:#166534;border-radius:4px;font-size:0.7rem;margin-left:0.5rem">Status Saat Ini</span>
                    @endif
                </p>
                <p style="font-size:0.875rem;color:var(--text-muted);margin:0 0 0.25rem 0">
                    {{ $event['time']->locale('id')->translatedFormat('d F Y, H:i') }}
                </p>
                @if($event['user'])
                <p style="font-size:0.85rem;color:var(--text-muted);margin:0 0 0.25rem 0">
                    Oleh: <strong>{{ $event['user']->name ?? '-' }}</strong>
                    @if($event['user']->sidongan_role)
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
                            $roleLabel = $roleLabels[$event['user']->sidongan_role] ?? ucfirst(str_replace('_', ' ', $event['user']->sidongan_role));
                        @endphp
                        <span style="display:inline-block;padding:0.15rem 0.4rem;background:rgba(59,130,246,0.1);color:#3b82f6;border-radius:4px;font-size:0.75rem;margin-left:0.5rem">{{ $roleLabel }}</span>
                    @endif
                </p>
                @endif
                @if(isset($event['detail']))
                <p style="font-size:0.85rem;color:var(--text-muted);margin:0">
                    {{ $event['detail'] }}
                </p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Laporan Kegiatan Detail --}}
<div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
        <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0;display:flex;align-items:center;gap:0.5rem">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="9 19 9 19"/><line x1="15" y1="19" x2="15" y2="19"/></svg>
            Laporan Kegiatan
            <span style="font-size:0.85rem;font-weight:500;color:var(--text-muted);margin-left:0.5rem">({{ $stats['total_laporan'] }})</span>
        </h3>
        <a href="{{ route('sidongan.dashboard') }}" target="_blank" style="padding:0.5rem 1rem;background:var(--primary);color:#fff;border:none;border-radius:6px;font-weight:600;cursor:pointer;text-decoration:none;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.5rem;font-size:0.875rem"
           onmouseover="this.style.background='#0d9488';this.style.transform='translateY(-2px)'"
           onmouseout="this.style.background='var(--primary)';this.style.transform='translateY(0)'">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Laporan
        </a>
    </div>
    
    @if($document->activityReports->count() > 0)
    <div style="display:grid;gap:1.5rem">
        @foreach($document->activityReports as $report)
        <div style="padding:1.5rem;background:#f8fafc;border-radius:10px;border:1px solid rgba(0,0,0,0.04);position:relative;
            @if($report->status === 'disetujui') border-left:4px solid #22c55e;
            @elseif($report->status === 'ditolak') border-left:4px solid #ef4444;
            @else border-left:4px solid #3b82f6; @endif">
            
            {{-- Status Badge --}}
            <div style="position:absolute;top:1rem;right:1rem">
                @php
                    $statusBadges = [
                        'menunggu_verifikasi' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Menunggu Verifikasi', 'icon' => 'clock'],
                        'disetujui' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'Disetujui', 'icon' => 'check-circle'],
                        'ditolak' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Ditolak', 'icon' => 'times-circle'],
                    ];
                    $badge = $statusBadges[$report->status] ?? $statusBadges['menunggu_verifikasi'];
                @endphp
                <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.375rem 0.75rem;background:{{ $badge['bg'] }};color:{{ $badge['text'] }};border-radius:20px;font-size:0.75rem;font-weight:600">
                    <i class="fas fa-{{ $badge['icon'] }}" style="font-size:0.65rem"></i>
                    {{ $badge['label'] }}
                </span>
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem">
                {{-- Info Pelapor --}}
                <div>
                    <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem">
                        @if($report->creator && $report->creator->avatar)
                            <img src="{{ asset('storage/' . $report->creator->avatar) }}" alt="{{ $report->creator->name }}" style="width:48px;height:48px;border-radius:50%;object-fit:cover">
                        @else
                            <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem">
                                {{ strtoupper(substr($report->creator->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p style="font-weight:600;color:var(--text-dark);margin:0">{{ $report->creator->name ?? '-' }}</p>
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
                                <span style="font-size:0.75rem;color:var(--text-muted)">{{ $roleLabel }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="margin-bottom:1rem">
                        <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Tanggal Laporan</p>
                        <p style="font-size:0.875rem;color:var(--text-dark);margin:0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:0.25rem"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ \Carbon\Carbon::parse($report->created_at)->locale('id')->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                    
                    @if($report->status !== 'menunggu_verifikasi' && $report->verified_at)
                    <div>
                        <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Diverifikasi</p>
                        <p style="font-size:0.875rem;color:var(--text-dark);margin:0">
                            {{ \Carbon\Carbon::parse($report->verified_at)->locale('id')->translatedFormat('d F Y, H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
                
                {{-- Detail Laporan --}}
                <div>
                    <div style="margin-bottom:1rem">
                        <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Nama Kegiatan</p>
                        <p style="font-size:1rem;font-weight:600;color:var(--text-dark);margin:0">{{ $report->kegiatan_nama ?? '-' }}</p>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">
                        <div>
                            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Tanggal Kegiatan</p>
                            <p style="font-size:0.875rem;color:var(--text-dark);margin:0">
                                {{ $report->kegiatan_tanggal ? \Carbon\Carbon::parse($report->kegiatan_tanggal)->locale('id')->translatedFormat('d F Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Waktu</p>
                            <p style="font-size:0.875rem;color:var(--text-dark);margin:0">
                                {{ $report->start_time && $report->end_time ? \Carbon\Carbon::parse($report->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($report->end_time)->format('H:i') : '-' }}
                            </p>
                        </div>
                    </div>
                    
                    <div style="margin-bottom:1rem">
                        <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Lokasi</p>
                        <p style="font-size:0.875rem;color:var(--text-dark);margin:0">
                            @php
                                $lokasiParts = [];
                                if (!empty($report->kelurahan)) $lokasiParts[] = $report->kelurahan;
                                if (!empty($report->kecamatan)) $lokasiParts[] = $report->kecamatan;
                                if (!empty($report->kabupaten)) $lokasiParts[] = $report->kabupaten;
                                if (!empty($report->provinsi)) $lokasiParts[] = $report->provinsi;
                                $lokasi = implode(', ', $lokasiParts);
                            @endphp
                            {{ $lokasi ?: '-' }}
                        </p>
                        @if(!empty($report->alamat_lengkap))
                        <p style="font-size:0.875rem;color:var(--text-muted);margin:0.25rem 0 0 0">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:0.25rem"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $report->alamat_lengkap }}
                        </p>
                        @endif
                    </div>
                    
                    <div style="margin-bottom:1rem">
                        <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">Deskripsi Kegiatan</p>
                        <p style="font-size:0.875rem;color:var(--text-dark);margin:0;line-height:1.6">{{ $report->deskripsi ?? '-' }}</p>
                    </div>
                    
                    @if($report->fotos)
                        @php $fotos = json_decode($report->fotos, true); @endphp
                        @if(is_array($fotos) && count($fotos) > 0)
                        <div>
                            <p style="font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.75rem 0;text-transform:uppercase;letter-spacing:0.5px">Dokumentasi ({{ count($fotos) }} foto)</p>
                            <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
                                @foreach(array_slice($fotos, 0, 4) as $foto)
                                <a href="{{ Storage::disk('public')->url($foto) }}" target="_blank" style="width:100px;height:100px;border-radius:8px;overflow:hidden;border:2px solid rgba(0,0,0,0.08);transition:all 0.2s"
                                   onmouseover="this.style.borderColor='var(--primary)';this.style.transform='scale(1.05)'"
                                   onmouseout="this.style.borderColor='rgba(0,0,0,0.08)';this.style.transform='scale(1)'">
                                    <img src="{{ Storage::disk('public')->url($foto) }}" alt="Foto" style="width:100%;height:100%;object-fit:cover">
                                </a>
                                @endforeach
                                @if(count($fotos) > 4)
                                <div style="width:100px;height:100px;border-radius:8px;background:rgba(59,130,246,0.1);display:flex;align-items:center;justify-content:center;color:var(--primary);font-weight:600;font-size:0.875rem">
                                    +{{ count($fotos) - 4 }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    @endif
                    
                    @if($report->status === 'ditolak' && $report->catatan_verifikasi)
                    <div style="margin-top:1rem;padding:1rem;background:#fee2e2;border-radius:8px;border-left:3px solid #ef4444">
                        <p style="font-size:0.75rem;font-weight:600;color:#991b1b;margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">
                            <i class="fas fa-times-circle" style="margin-right:0.25rem"></i> Catatan Penolakan
                        </p>
                        <p style="font-size:0.875rem;color:#7f1d1d;margin:0;font-style:italic">"{{ $report->catatan_verifikasi }}"</p>
                    </div>
                    @elseif($report->status === 'disetujui' && $report->catatan_verifikasi)
                    <div style="margin-top:1rem;padding:1rem;background:#d1fae5;border-radius:8px;border-left:3px solid #10b981">
                        <p style="font-size:0.75rem;font-weight:600;color:#065f46;margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px">
                            <i class="fas fa-check-circle" style="margin-right:0.25rem"></i> Catatan Persetujuan
                        </p>
                        <p style="font-size:0.875rem;color:#064e3b;margin:0;font-style:italic">"{{ $report->catatan_verifikasi }}"</p>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Delete Button --}}
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06);text-align:right">
                <form method="POST" action="{{ route('admin.sidongan-data.report.delete', $report->id) }}" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?')"
                      style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="padding:0.5rem 1rem;background:#fee2e2;color:#ef4444;border:none;border-radius:6px;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.5rem;font-size:0.875rem;font-weight:600"
                            onmouseover="this.style.background='#fecaca';this.style.transform='scale(1.05)'"
                            onmouseout="this.style.background='#fee2e2';this.style.transform='scale(1)'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        Hapus Laporan
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div style="padding:3rem 1rem;text-align:center;color:var(--text-muted)">
        <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Belum ada laporan</h3>
        <p style="font-size:0.9rem;margin:0">Laporan kegiatan akan muncul di sini.</p>
    </div>
    @endif
</div>

{{-- Notifikasi --}}
@if($notifications->count() > 0)
<div class="card" style="padding:1.5rem;margin-bottom:1.5rem">
    <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0 0 1.5rem 0;display:flex;align-items:center;gap:0.5rem">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a855f7" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        Notifikasi Terkait
    </h3>
    
    <div style="display:grid;gap:0.75rem">
        @foreach($notifications as $notif)
        <div style="padding:1rem;background:#fafafa;border-radius:8px;border:1px solid rgba(0,0,0,0.04)">
            <p style="font-weight:600;color:var(--text-dark);margin:0 0 0.25rem 0;font-size:0.95rem">{{ $notif->message }}</p>
            <p style="font-size:0.85rem;color:var(--text-muted);margin:0">{{ $notif->created_at->diffForHumans() }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection