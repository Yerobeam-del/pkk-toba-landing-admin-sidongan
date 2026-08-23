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
<div class="dash-stats-row">
    {{-- Total Berita --}}
    <div class="stat-card dash-stat-blue">
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
    <div class="stat-card dash-stat-green">
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
    <div class="stat-card dash-stat-orange">
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
    <div class="stat-card dash-stat-red">
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
    <div class="stat-card dash-stat-purple">
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
    <div class="stat-card dash-stat-teal">
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

<div class="card dash-card">
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

{{-- Chart & Activity Log --}}
<div class="dash-chart-grid">
    {{-- Chart --}}
    <div class="card dash-card">
        <h2 class="card-title dash-card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
            Aktivitas 6 Bulan Terakhir
        </h2>
        <div style="position:relative;height:200px">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    {{-- Activity Log --}}
    <div class="card dash-card">
        <h2 class="card-title dash-card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Aktivitas Terbaru
        </h2>
        <div style="display:flex;flex-direction:column;gap:0.5rem;max-height:220px;overflow-y:auto">
            @forelse($recentActivities as $activity)
            <div class="dash-activity-item">
                <div class="dash-activity-icon {{ $activity['type'] === 'berita' ? 'dash-activity-icon--berita' : 'dash-activity-icon--user' }}">
                    @if($activity['type'] === 'berita')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    @endif
                </div>
                <div class="dash-activity-text">
                    <div class="dash-activity-title">{{ $activity['text'] }}</div>
                    <div class="dash-activity-time">{{ $activity['time']->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <p style="color:var(--text-muted);text-align:center;padding:1.5rem">Belum ada aktivitas</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent Items --}}
<div class="dash-recent-grid">

    {{-- Latest News --}}
    <div class="card dash-card">
        <h2 class="card-title dash-card-title">
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
                <div class="dash-recent-item">
                    <div class="dash-recent-title">
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
    <div class="card dash-card">
        <h2 class="card-title dash-card-title">
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
                    <div class="dash-user-avatar">
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
@push('styles')
<style>
    @media (max-width: 768px) {
        .dashboard-grid-2 { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart');
    if (!ctx) return;

    const chartData = @json($chartData);
    const labels = chartData.map(d => {
        const [y, m] = d.month.split('-');
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        return months[parseInt(m) - 1] + ' ' + y.slice(2);
    });
    const data = chartData.map(d => d.count);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Aktivitas',
                data: data,
                backgroundColor: 'rgba(20, 184, 166, 0.2)',
                borderColor: 'rgba(20, 184, 166, 1)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { family: 'Plus Jakarta Sans', weight: '600' },
                    bodyFont: { family: 'Plus Jakarta Sans' },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
