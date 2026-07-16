@extends('admin.layouts.app')
@section('title', 'Manajemen Aplikasi')
@section('page-title', 'Aplikasi & Sistem')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .aplikasi-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .aplikasi-header h1 {
        font-size: 1.25rem !important;
    }
    
    .aplikasi-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .stats-grid {
        grid-template-columns: 1fr !important;
    }
    
    .tabs-container {
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }
    
    .tabs-container::-webkit-scrollbar {
        height: 4px;
    }
    
    .tabs-container::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 4px;
    }
    
    .tab-btn {
        white-space: nowrap !important;
        flex-shrink: 0 !important;
    }
    
    /* Switch view: hide desktop table, show mobile card */
    .desktop-table-view {
        display: none !important;
    }
    
    .mobile-card-view {
        display: block !important;
    }
}

/* Desktop: show table, hide card */
@media (min-width: 769px) {
    .desktop-table-view {
        display: block !important;
    }
    
    .mobile-card-view {
        display: none !important;
    }
}
</style>

{{-- Header Section --}}
<div class="aplikasi-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Aplikasi & Sistem</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola aplikasi dan sistem informasi PKK Kabupaten Toba</p>
    </div>
    <a href="{{ route('admin.aplikasi.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Aplikasi
    </a>
</div>

{{-- Stats Cards --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Total Aplikasi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $applications->count() }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#38a169,#2f855a);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Aplikasi Aktif</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $applications->where('status','active')->where('is_active',true)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#dd6b20,#c05621);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Maintenance</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $applications->where('status','maintenance')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22h20"/><path d="M12 2v20"/><path d="M12 22V2"/><path d="M2 12h20"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Dalam Pengembangan</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $applications->where('status','development')->count() }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards Baru untuk Visibility --}}
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    
    {{-- Tampil di Beranda --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#14b8a6,#0d9488);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Tampil di Beranda</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['show_in_beranda'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tampil di Footer --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                </svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Tampil di Footer</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['show_in_footer'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Tampil di Floating --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                    <polyline points="2 17 12 22 22 17"></polyline>
                    <polyline points="2 12 12 17 22 12"></polyline>
                </svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Tampil di Floating</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $stats['show_in_floating'] ?? 0 }}</p>
            </div>
        </div>
    </div>

</div>

{{-- Modern Tabs --}}
<div class="tabs-container" style="display:flex;gap:0.25rem;margin-bottom:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem;overflow-x:auto">
    <button class="tab-btn active" onclick="switchTab('all', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid var(--primary)">
        Semua Aplikasi
    </button>
    <button class="tab-btn" onclick="switchTab('active', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent">
        Aktif <span style="background:rgba(56,161,105,0.15);color:#2f855a;padding:2px 8px;border-radius:12px;font-size:0.75rem;margin-left:4px">{{ $applications->where('status','active')->where('is_active',true)->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('maintenance', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent">
        Maintenance <span style="background:rgba(221,107,32,0.15);color:#c05621;padding:2px 8px;border-radius:12px;font-size:0.75rem;margin-left:4px">{{ $applications->where('status','maintenance')->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('development', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent">
        Pengembangan <span style="background:rgba(128,90,213,0.15);color:#6b46c1;padding:2px 8px;border-radius:12px;font-size:0.75rem;margin-left:4px">{{ $applications->where('status','development')->count() }}</span>
    </button>
</div>

{{-- Main Card --}}
<div class="card" style="padding:0;overflow:hidden;border:1px solid rgba(0,0,0,0.06);border-radius:12px">
    
    {{-- ========================================== --}}
    {{-- TAB: SEMUA APLIKASI --}}
    {{-- ========================================== --}}
    <div id="tab-all" class="tab-content active">
        {{-- Desktop Table View --}}
        <div class="desktop-table-view">
            <div class="table-container" style="padding:1rem">
                @if($applications->count() > 0)
                <table style="width:100%;border-collapse:collapse;min-width:800px">
                    <thead>
                        <tr style="text-align:left;border-bottom:2px solid rgba(0,0,0,0.08)">
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Logo</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Nama Aplikasi</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Kategori</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Status</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">URL</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Urutan</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($applications as $app)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                            <td style="padding:1rem">
                                @if($app->icon)
                                <img src="{{ asset('storage/'.$app->icon) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#f8fafc">
                                @else
                                <div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.8rem">
                                    {{ strtoupper(substr($app->short_name, 0, 2)) }}
                                </div>
                                @endif
                            </td>
                            <td style="padding:1rem">
                                <div style="font-weight:600;color:var(--text-dark)">{{ $app->name }}</div>
                                <small style="color:var(--text-muted);font-size:0.85rem">{{ $app->short_name }}</small>
                            </td>
                            <td style="padding:1rem">
                                <span style="background:{{ $app->category == 'aplikasi' ? 'rgba(20,184,166,0.1)' : 'rgba(59,130,246,0.1)' }};color:{{ $app->category == 'aplikasi' ? 'var(--primary)' : '#2563eb' }};padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">
                                    {{ ucfirst($app->category) }}
                                </span>
                            </td>
                            <td style="padding:1rem">
                                @include('admin.aplikasi.partials.status-badge', ['app' => $app])
                            </td>
                            <td style="padding:1rem">
                                @if($app->url && $app->url !== '#')
                                <a href="{{ $app->url }}" target="_blank" style="color:var(--primary);text-decoration:none;font-size:0.85rem;border-bottom:1px dotted var(--primary)">
                                    {{ Str::limit($app->url, 25) }}
                                </a>
                                @else
                                <span style="color:var(--text-muted);font-size:0.85rem">-</span>
                                @endif
                            </td>
                            <td style="padding:1rem;color:var(--text-muted);font-size:0.9rem">{{ $app->sort_order }}</td>
                            <td style="padding:1rem;text-align:right">
                                @include('admin.aplikasi.partials.action-buttons', ['app' => $app])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    @include('admin.aplikasi.partials.empty-state', ['message' => 'Belum ada aplikasi. Silakan tambah aplikasi pertama Anda.'])
                @endif
            </div>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="mobile-card-view" style="padding:1rem">
            @if($applications->count() > 0)
                @foreach($applications as $app)
                    @include('admin.aplikasi.partials.app-card', ['app' => $app])
                @endforeach
            @else
                @include('admin.aplikasi.partials.empty-state', ['message' => 'Belum ada aplikasi. Silakan tambah aplikasi pertama Anda.'])
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB: APLIKASI AKTIF --}}
    {{-- ========================================== --}}
    <div id="tab-active" class="tab-content" style="display:none">
        @php $activeApps = $applications->where('status','active')->where('is_active',true); @endphp
        
        {{-- Desktop Table View --}}
        <div class="desktop-table-view">
            <div class="table-container" style="padding:1rem">
                @if($activeApps->count() > 0)
                <table style="width:100%;border-collapse:collapse;min-width:600px">
                    <thead>
                        <tr style="text-align:left;border-bottom:2px solid rgba(0,0,0,0.08)">
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Logo</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Nama</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">URL</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeApps as $app)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                            <td style="padding:1rem">
                                @if($app->icon)
                                <img src="{{ asset('storage/'.$app->icon) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#f8fafc">
                                @else
                                <div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,#38a169,#2f855a);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">
                                    {{ strtoupper(substr($app->short_name, 0, 2)) }}
                                </div>
                                @endif
                            </td>
                            <td style="padding:1rem">
                                <div style="font-weight:600;color:var(--text-dark)">{{ $app->name }}</div>
                                <small style="color:var(--text-muted);font-size:0.85rem">{{ $app->short_name }}</small>
                            </td>
                            <td style="padding:1rem">
                                @if($app->url)
                                <a href="{{ $app->url }}" target="_blank" style="color:var(--primary);text-decoration:none;font-size:0.85rem;border-bottom:1px dotted var(--primary)">{{ Str::limit($app->url, 30) }}</a>
                                @else
                                <span style="color:var(--text-muted);font-size:0.85rem">-</span>
                                @endif
                            </td>
                            <td style="padding:1rem;text-align:right">
                                @include('admin.aplikasi.partials.action-buttons', ['app' => $app])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    @include('admin.aplikasi.partials.empty-state', ['message' => 'Belum ada aplikasi aktif.'])
                @endif
            </div>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="mobile-card-view" style="padding:1rem">
            @if($activeApps->count() > 0)
                @foreach($activeApps as $app)
                    @include('admin.aplikasi.partials.app-card', ['app' => $app])
                @endforeach
            @else
                @include('admin.aplikasi.partials.empty-state', ['message' => 'Belum ada aplikasi aktif.'])
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB: MAINTENANCE --}}
    {{-- ========================================== --}}
    <div id="tab-maintenance" class="tab-content" style="display:none">
        @php $maintenanceApps = $applications->where('status','maintenance'); @endphp
        
        {{-- Desktop Table View --}}
        <div class="desktop-table-view">
            <div class="table-container" style="padding:1rem">
                @if($maintenanceApps->count() > 0)
                <table style="width:100%;border-collapse:collapse;min-width:600px">
                    <thead>
                        <tr style="text-align:left;border-bottom:2px solid rgba(0,0,0,0.08)">
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Logo</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Nama</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Status</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($maintenanceApps as $app)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                            <td style="padding:1rem">
                                @if($app->icon)
                                <img src="{{ asset('storage/'.$app->icon) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#f8fafc">
                                @else
                                <div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,#dd6b20,#c05621);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">
                                    {{ strtoupper(substr($app->short_name, 0, 2)) }}
                                </div>
                                @endif
                            </td>
                            <td style="padding:1rem">
                                <div style="font-weight:600;color:var(--text-dark)">{{ $app->name }}</div>
                                <small style="color:var(--text-muted);font-size:0.85rem">{{ $app->short_name }}</small>
                            </td>
                            <td style="padding:1rem">
                                @include('admin.aplikasi.partials.status-badge', ['app' => $app])
                            </td>
                            <td style="padding:1rem;text-align:right">
                                @include('admin.aplikasi.partials.action-buttons', ['app' => $app])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    @include('admin.aplikasi.partials.empty-state', ['message' => 'Tidak ada aplikasi dalam maintenance.'])
                @endif
            </div>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="mobile-card-view" style="padding:1rem">
            @if($maintenanceApps->count() > 0)
                @foreach($maintenanceApps as $app)
                    @include('admin.aplikasi.partials.app-card', ['app' => $app])
                @endforeach
            @else
                @include('admin.aplikasi.partials.empty-state', ['message' => 'Tidak ada aplikasi dalam maintenance.'])
            @endif
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAB: PENGEMBANGAN --}}
    {{-- ========================================== --}}
    <div id="tab-development" class="tab-content" style="display:none">
        @php $devApps = $applications->where('status','development'); @endphp
        
        {{-- Desktop Table View --}}
        <div class="desktop-table-view">
            <div class="table-container" style="padding:1rem">
                @if($devApps->count() > 0)
                <table style="width:100%;border-collapse:collapse;min-width:600px">
                    <thead>
                        <tr style="text-align:left;border-bottom:2px solid rgba(0,0,0,0.08)">
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Logo</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Nama</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Status</th>
                            <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devApps as $app)
                        <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                            <td style="padding:1rem">
                                @if($app->icon)
                                <img src="{{ asset('storage/'.$app->icon) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#f8fafc">
                                @else
                                <div style="width:40px;height:40px;border-radius:8px;background:linear-gradient(135deg,#805ad5,#6b46c1);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">
                                    {{ strtoupper(substr($app->short_name, 0, 2)) }}
                                </div>
                                @endif
                            </td>
                            <td style="padding:1rem">
                                <div style="font-weight:600;color:var(--text-dark)">{{ $app->name }}</div>
                                <small style="color:var(--text-muted);font-size:0.85rem">{{ $app->short_name }}</small>
                            </td>
                            <td style="padding:1rem">
                                @include('admin.aplikasi.partials.status-badge', ['app' => $app])
                            </td>
                            <td style="padding:1rem;text-align:right">
                                @include('admin.aplikasi.partials.action-buttons', ['app' => $app])
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                    @include('admin.aplikasi.partials.empty-state', ['message' => 'Belum ada aplikasi dalam pengembangan.'])
                @endif
            </div>
        </div>
        
        {{-- Mobile Card View --}}
        <div class="mobile-card-view" style="padding:1rem">
            @if($devApps->count() > 0)
                @foreach($devApps as $app)
                    @include('admin.aplikasi.partials.app-card', ['app' => $app])
                @endforeach
            @else
                @include('admin.aplikasi.partials.empty-state', ['message' => 'Belum ada aplikasi dalam pengembangan.'])
            @endif
        </div>
    </div>

</div>

<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.style.color = 'var(--text-muted)';
        b.style.borderBottom = '2px solid transparent';
    });
    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
    
    btn.style.color = 'var(--primary)';
    btn.style.borderBottom = '2px solid var(--primary)';
    document.getElementById('tab-' + tabId).style.display = 'block';
}

async function confirmDeleteApp(id, name) {
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Aplikasi <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                {
                    title: 'Hapus Aplikasi?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
            
            if (confirmed) {
                document.getElementById('delete-app-' + id).submit();
            }
        } else {
            if (confirm(`Hapus aplikasi "${name}"?`)) {
                document.getElementById('delete-app-' + id).submit();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (confirm(`Hapus aplikasi "${name}"?`)) {
            document.getElementById('delete-app-' + id).submit();
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const firstBtn = document.querySelector('.tab-btn');
    if(firstBtn) switchTab('all', firstBtn);
});
</script>

@endsection