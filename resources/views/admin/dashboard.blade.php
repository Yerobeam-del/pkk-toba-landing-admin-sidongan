@extends('admin.layouts.app')

@section('content')
<div class="dashboard-header">
    <div>
        <h1 class="page-title">Selamat Datang di Admin Panel</h1>
        <p class="page-subtitle">Dashboard untuk mengelola konten website PKK Kabupaten Toba.</p>
    </div>
</div>

{{-- Stats Cards Row 1 --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:1.5rem">
    {{-- Total Berita --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">TOTAL BERITA</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $totalBerita ?? 0 }}</p>
                <p style="font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0">
                    @php $bulanIni = $statistikBulanIni['berita'] ?? 0; @endphp
                    {{ $bulanIni }} bulan ini
                </p>
            </div>
        </div>
    </div>

    {{-- Total Aplikasi --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">TOTAL APLIKASI</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $totalAplikasi ?? 0 }}</p>
                <p style="font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0">Aplikasi Aktif</p>
            </div>
        </div>
    </div>

    {{-- Pengurus --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#ed8936,#dd6b20);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">PENGURUS</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $totalPengurus ?? 0 }}</p>
                <p style="font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0">Anggota Aktif</p>
            </div>
        </div>
    </div>

    {{-- Total Users --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">TOTAL USERS</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $totalUsers ?? 0 }}</p>
                <p style="font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0">
                    @php $usersBulanIni = $statistikBulanIni['users'] ?? 0; @endphp
                    {{ $usersBulanIni }} bulan ini
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards Row 2 --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    {{-- Template --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">TEMPLATE</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $totalTemplate ?? 0 }}</p>
                <p style="font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0">Dokumen Tersedia</p>
            </div>
        </div>
    </div>

    {{-- SK & Dokumen --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#319795,#285e61);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">SK & DOKUMEN</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $totalSKDokumen ?? 0 }}</p>
                <p style="font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0">Dokumen Resmi</p>
            </div>
        </div>
    </div>
</div>

{{-- Recent Activities & Latest Items --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2rem">

    {{-- Latest News --}}
    <div class="card" style="border:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h2 class="card-title" style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                <path d="M18 14h-8"/>
                <path d="M15 18h-5"/>
                <path d="M10 6h8v4h-8V6Z"/>
            </svg>
            Berita Terbaru
        </h2>
        <div style="display:flex;flex-direction:column;gap:0.75rem">
            @forelse($beritaTerbaru ?? [] as $berita)
                <div style="padding:0.75rem;background:#f8fafc;border-radius:8px;border-left:3px solid var(--primary)">
                    <div style="font-weight:600;color:var(--text-dark);font-size:0.9rem;margin-bottom:0.25rem">
                        {{ Str::limit($berita->title, 50) }}
                    </div>
                    <div style="font-size:0.8rem;color:var(--text-muted)">
                        {{ $berita->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);text-align:center;padding:1rem">Belum ada berita</p>
            @endforelse
        </div>
    </div>

    {{-- Latest Users --}}
    <div class="card" style="border:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
        <h2 class="card-title" style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#e53e3e" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
            Pengguna Terbaru
        </h2>
        <div style="display:flex;flex-direction:column;gap:0.75rem">
            @forelse($usersTerbaru ?? [] as $user)
                <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#f8fafc;border-radius:8px">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;color:var(--text-dark);font-size:0.9rem">
                            {{ Str::limit($user->name, 30) }}
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-muted)">
                            {{ $user->role?->display_name ?? '-' }}
                        </div>
                    </div>
                </div>
            @empty
                <p style="color:var(--text-muted);text-align:center;padding:1rem">Belum ada pengguna</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="card" style="border:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);">
    <h2 class="card-title">Aksi Cepat</h2>
    <div class="quick-actions">
        <a href="{{ route('admin.berita.create') }}" class="quick-action-btn">
            <div class="action-icon" style="background:rgba(15,107,99,0.1);color:var(--primary)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                    <path d="M18 14h-8"/>
                    <path d="M15 18h-5"/>
                    <path d="M10 6h8v4h-8V6Z"/>
                </svg>
            </div>
            <span>Tambah Berita</span>
        </a>

        <a href="{{ route('admin.struktur.index') }}" class="quick-action-btn">
            <div class="action-icon" style="background:rgba(59,130,246,0.1);color:#3b82f6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <span>Struktur Organisasi</span>
        </a>

        <a href="{{ route('admin.template.index') }}" class="quick-action-btn">
            <div class="action-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
            </div>
            <span>Template Dokumen</span>
        </a>

        <a href="{{ route('admin.aplikasi.index') }}" class="quick-action-btn">
            <div class="action-icon" style="background:rgba(34,197,94,0.1);color:#22c55e">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
            </div>
            <span>Kelola Aplikasi</span>
        </a>

        <a href="{{ route('admin.user-management.index') }}" class="quick-action-btn">
            <div class="action-icon" style="background:rgba(239,68,68,0.1);color:#ef4444">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <span>Manajemen Akun</span>
        </a>

        <a href="{{ route('admin.sk.index') }}" class="quick-action-btn">
            <div class="action-icon" style="background:rgba(14,165,233,0.1);color:#0ea5e9">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <span>SK & Dokumen</span>
        </a>
    </div>
</div>
@endsection
