@extends('sidongan.layouts.app')
@section('title', 'Verifikasi Laporan - SIDONGAN')

@section('content')
<style>
    .verif-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 3px solid transparent;
        position: relative;
        overflow: hidden;
    }
    /* HAPUS .verif-item::before karena tidak diperlukan */
    /* HAPUS .verif-item:hover karena akan conflict dengan inline style */
    
    .btn-action {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .stats-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-slide-in {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
</style>

<div style="padding: 0 1.5rem;">
    {{-- Header --}}
    <div style="margin-bottom: 2rem;" class="animate-slide-in">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">
            Verifikasi Laporan
        </h1>
        <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">
            Tinjau dan verifikasi laporan kegiatan dari Sekretaris
        </p>
    </div>

    {{-- Stats Cards --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
        {{-- Menunggu Verifikasi - UNGU --}}
        <div class="stats-card animate-slide-in" 
            style="background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(168, 85, 247, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-clock" style="font-size: 2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                        Menunggu Verifikasi
                    </p>
                    <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">
                        {{ $documents->where('status', 'menunggu_verifikasi')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Disetujui - HIJAU --}}
        <div class="stats-card animate-slide-in" 
            style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                        Disetujui
                    </p>
                    <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">
                        {{ $documents->where('status', 'disetujui')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Ditolak - MERAH --}}
        <div class="stats-card animate-slide-in" 
            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-times-circle" style="font-size: 2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                        Ditolak / Revisi
                    </p>
                    <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">
                        {{ $documents->where('status', 'ditolak')->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="animate-slide-in">
        <form id="filterForm" action="{{ route('sidongan.verifikasi') }}" method="GET">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Cari Laporan</label>
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.875rem; pointer-events: none;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari berdasarkan judul kegiatan..." 
                            style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.1)'" 
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Status Verifikasi</label>
                    <div style="position: relative;">
                        <select name="status" style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; background: white; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem; transition: all 0.2s;"
                                onchange="document.getElementById('filterForm').submit()"
                                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.1)'" 
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                            <option value="">Semua Status</option>
                            <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Tampilkan</label>
                    <div style="position: relative;">
                        <select name="per_page" id="perPageSelect" style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; background: white; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem; transition: all 0.2s;"
                                onchange="document.getElementById('filterForm').submit()"
                                onfocus="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 0 0 3px rgba(124, 58, 237, 0.1)'" 
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
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
        @if($documents->count() > 0)
            <div style="padding: 0;">
                @foreach($documents as $report)
                    @php
                        $statusConfig = [
                            'menunggu_verifikasi' => ['bg' => '#f5f3ff', 'border' => '#ddd6fe', 'text' => '#6d28d9', 'btn' => '#7c3aed', 'label' => 'Menunggu Verifikasi', 'icon' => 'fa-clock', 'hover' => '#f5f3ff'],
                            'disetujui' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'btn' => '#22c55e', 'label' => 'Disetujui', 'icon' => 'fa-check-circle', 'hover' => '#f0fdf4'],
                            'ditolak' => ['bg' => '#fef2f2', 'border' => '#fecaca', 'text' => '#991b1b', 'btn' => '#ef4444', 'label' => 'Ditolak', 'icon' => 'fa-times-circle', 'hover' => '#fef2f2'],
                        ];
                        $theme = $statusConfig[$report->status] ?? $statusConfig['menunggu_verifikasi'];
                    @endphp
                    
                    <div class="verif-item animate-slide-in" 
                        style="padding: 1.5rem 1.75rem; border-bottom: {{ $loop->last ? 'none' : '1px solid #f3f4f6' }};
                                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                border-left: 3px solid transparent;"
                        onmouseover="this.style.borderLeftColor='{{ $theme['btn'] }}'; this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" 
                        onmouseout="this.style.borderLeftColor='transparent'; this.style.transform='translateX(0)'; this.style.boxShadow='none'">
                        
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                                    @if($report->document)
                                    <span style="font-size: 0.8rem; font-family: monospace; background: white; color: {{ $theme['text'] }}; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700; border: 1px solid {{ $theme['border'] }}; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        {{ $report->document->agenda_number }}
                                    </span>
                                    @endif
                                    <span style="font-size: 0.75rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-weight: 600; background: {{ $theme['btn'] }}; color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                                        <i class="fas {{ $theme['icon'] }}" style="margin-right: 0.35rem; font-size: 0.65rem;"></i>
                                        {{ $theme['label'] }}
                                    </span>
                                </div>
                                
                                <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 0.75rem 0; line-height: 1.4;">
                                    {{ $report->kegiatan_nama }}
                                </h4>
                                
                                <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; color: #64748b; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 1.5rem; height: 1.5rem; background: {{ $theme['bg'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="color: {{ $theme['btn'] }}; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $report->creator->name ?? 'Sekretaris PKK' }}
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 1.5rem; height: 1.5rem; background: {{ $theme['bg'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-calendar" style="color: {{ $theme['btn'] }}; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $report->kegiatan_tanggal->locale('id')->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-shrink: 0; flex-direction: column;">
                                @if($report->status === 'menunggu_verifikasi')
                                <a href="{{ route('sidongan.verifikasi.form', $report->id) }}" 
                                class="btn-action"
                                style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, {{ $theme['btn'] }}, {{ $theme['btn'] }}); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba({{ hexdec(substr($theme['btn'], 1, 2)) }}, {{ hexdec(substr($theme['btn'], 3, 2)) }}, {{ hexdec(substr($theme['btn'], 5, 2)) }}, 0.2);">
                                    <i class="fas fa-clipboard-check"></i>
                                    <span>Verifikasi</span>
                                </a>
                                @endif
                                <a href="{{ route('sidongan.lapor_kegiatan.show', $report->id) }}" 
                                class="btn-action"
                                style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: white; color: {{ $theme['text'] }}; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; border: 2px solid {{ $theme['border'] }}; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s;"
                                onmouseover="this.style.background='{{ $theme['bg'] }}'; this.style.borderColor='{{ $theme['btn'] }}'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.1)'" 
                                onmouseout="this.style.background='white'; this.style.borderColor='{{ $theme['border'] }}'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)'">
                                    <i class="fas fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </div>
                        </div>
                        
                        @if($report->deskripsi)
                        <div style="background: {{ $theme['bg'] }}; border-left: 3px solid {{ $theme['btn'] }}; border-radius: 0.5rem; padding: 0.875rem 1rem;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <div style="width: 2rem; height: 2rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <i class="fas fa-align-left" style="color: {{ $theme['btn'] }}; font-size: 0.875rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="font-size: 0.8rem; color: {{ $theme['text'] }}; margin: 0 0 0.375rem 0; font-weight: 700;">Deskripsi Kegiatan</p>
                                    <p style="font-size: 0.875rem; color: #64748b; margin: 0; line-height: 1.5;">
                                        {{ Str::limit($report->deskripsi, 120) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($report->catatan_verifikasi)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed {{ $theme['border'] }};">
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0 0 0.35rem 0; font-weight: 600;">Catatan Verifikasi:</p>
                            <p style="font-size: 0.85rem; color: #475569; margin: 0; font-style: italic;">{{ $report->catatan_verifikasi }}</p>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($documents->hasPages())
            <div style="padding: 1.25rem 1.75rem; border-top: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.875rem; color: #64748b;">
                    Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> laporan
                </div>
                
                <div style="display: flex; gap: 0.35rem; align-items: center;">
                    @if($documents->onFirstPage())
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f1f5f9; color: #94a3b8; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #7c3aed; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
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
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                            1
                        </a>
                        @if($startPage > 2)
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; color: #94a3b8; font-size: 0.875rem;">...</span>
                        @endif
                    @endif
                    
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $currentPage)
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #7c3aed; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $documents->url($i) }}" 
                            style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor
                    
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; color: #94a3b8; font-size: 0.875rem;">...</span>
                        @endif
                        <a href="{{ $documents->url($lastPage) }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                            {{ $lastPage }}
                        </a>
                    @endif
                    
                    @if($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #7c3aed; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#6d28d9'" onmouseout="this.style.background='#7c3aed'">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f1f5f9; color: #94a3b8; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
            @else
            <div style="padding: 1.25rem 1.75rem; border-top: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.875rem; color: #64748b;">
                    Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> laporan
                </div>
                <div style="font-size: 0.875rem; color: #94a3b8;">
                    <i class="fas fa-info-circle"></i> Semua laporan ditampilkan dalam satu halaman
                </div>
            </div>
            @endif
        @else
            <div style="text-align: center; padding: 4rem 2rem;" class="animate-slide-in">
                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); animation: float 3s ease-in-out infinite;">
                    <i class="fas fa-inbox" style="color: #10b981; font-size: 3rem;"></i>
                </div>
                <h4 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">Tidak Ada Laporan</h4>
                <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">
                    Belum ada laporan kegiatan yang sesuai dengan filter.
                </p>
            </div>
        @endif
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    // Auto-submit search after delay (debounce 500ms)
    searchInput?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });
</script>
@endsection