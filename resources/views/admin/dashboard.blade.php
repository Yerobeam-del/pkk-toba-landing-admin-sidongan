{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
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
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">TOTAL BERITA</p>
                <p class="u-h1-hero">{{ $totalBerita ?? 0 }}</p>
                <p class="u-caption">
                    @php $bulanIni = ($statistikBulanIni['berita'] ?? 0); @endphp
                    {{ $bulanIni }} bulan ini
                </p>
            </div>
        </div>
    </div>

    {{-- Total Aplikasi --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#38a169,#2f855a);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">TOTAL APLIKASI</p>
                <p class="u-h1-hero">{{ $totalAplikasi ?? 0 }}</p>
                <p class="u-caption">Aplikasi Aktif</p>
            </div>
        </div>
    </div>

    {{-- Pengurus --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#ed8936,#dd6b20);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">PENGURUS</p>
                <p class="u-h1-hero">{{ $totalPengurus ?? 0 }}</p>
                <p class="u-caption">Anggota Aktif</p>
            </div>
        </div>
    </div>

    {{-- Total Users --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">TOTAL USERS</p>
                <p class="u-h1-hero">{{ $totalUsers ?? 0 }}</p>
                <p class="u-caption">
                    @php $usersBulanIni = ($statistikBulanIni['users'] ?? 0); @endphp
                    {{ $usersBulanIni }} bulan ini
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Stats Cards Row 2 --}}
<div class="u-a4">
    {{-- Template --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">TEMPLATE</p>
                <p class="u-h1-hero">{{ $totalTemplate ?? 0 }}</p>
                <p class="u-caption">Dokumen Tersedia</p>
            </div>
        </div>
    </div>

    {{-- SK & Dokumen --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#319795,#285e61);color:#fff;padding:1.25rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.08)">
        <div class="u-flex-start-gap-4">
            <div class="u-icon-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="u-flex-1">
                <p class="u-subtitle">SK & DOKUMEN</p>
                <p class="u-h1-hero">{{ $totalSKDokumen ?? 0 }}</p>
                <p class="u-caption">Dokumen Resmi</p>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
@php
    // Setiap aksi dipetakan ke permission yang sama dengan middleware di routes/web.php.
    // Tombol yang izinnya tidak dimiliki user akan tampil abu-abu dan tidak bisa diklik.
    $quickActions = [
        [
            'label' => 'Tambah Berita', 'route' => 'admin.berita.create', 'permission' => 'manage-berita',
            'bg' => 'rgba(15,107,99,0.1)', 'fg' => 'var(--primary)',
            'icon' => '<path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>',
        ],
        [
            'label' => 'Struktur Organisasi', 'route' => 'admin.struktur.index', 'permission' => 'manage-struktur',
            'bg' => 'rgba(59,130,246,0.1)', 'fg' => '#3b82f6',
            'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ],
        [
            'label' => 'Template Dokumen', 'route' => 'admin.template.index', 'permission' => 'manage-template',
            'bg' => 'rgba(139,92,246,0.1)', 'fg' => '#8b5cf6',
            'icon' => '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>',
        ],
        [
            'label' => 'Kelola Aplikasi', 'route' => 'admin.aplikasi.index', 'permission' => 'manage-aplikasi',
            'bg' => 'rgba(34,197,94,0.1)', 'fg' => '#22c55e',
            'icon' => '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
        ],
        [
            'label' => 'Manajemen Akun', 'route' => 'admin.user-management.index', 'permission' => 'manage-users',
            'bg' => 'rgba(239,68,68,0.1)', 'fg' => '#ef4444',
            'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ],
        [
            'label' => 'SK & Dokumen', 'route' => 'admin.sk.index', 'permission' => 'manage-dokumen',
            'bg' => 'rgba(14,165,233,0.1)', 'fg' => '#0ea5e9',
            'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>',
        ],
    ];
@endphp

<div class="card" style="border:none;box-shadow:0 2px 8px rgba(0,0,0,0.06);margin-bottom:2rem">
    <h2 class="card-title">Aksi Cepat</h2>
    <div class="quick-actions">
        @foreach ($quickActions as $aksi)
            @php $boleh = auth()->user()->hasPermission($aksi['permission']); @endphp

            @if ($boleh)
                <a href="{{ route($aksi['route']) }}" class="quick-action-btn">
                    <div class="action-icon" style="background:{{ $aksi['bg'] }};color:{{ $aksi['fg'] }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $aksi['icon'] !!}</svg>
                    </div>
                    <span>{{ $aksi['label'] }}</span>
                </a>
            @else
                {{-- Dirender sebagai <span>, bukan <a>, supaya benar-benar tidak bisa diklik --}}
                <span class="quick-action-btn is-locked" aria-disabled="true"
                      title="Akun Anda tidak memiliki akses ke {{ $aksi['label'] }}">
                    <div class="action-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $aksi['icon'] !!}</svg>
                    </div>
                    <span>{{ $aksi['label'] }}</span>
                    <svg class="lock-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
            @endif
        @endforeach
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
                        {{ Str::limit($berita->title ?? 'Tanpa Judul', 50) }}
                    </div>
                    <div class="u-text-muted-xs2">
                        {{ $berita->created_at?->diffForHumans() ?? '-' }}
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
                <div class="u-a56">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.85rem">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="u-flex-1-min">
                        <div class="u-a32">
                            {{ Str::limit($user->name ?? 'User', 30) }}
                        </div>
                        <div class="u-text-muted-xs">
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
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
