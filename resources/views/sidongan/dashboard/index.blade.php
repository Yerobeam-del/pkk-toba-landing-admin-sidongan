{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Dashboard - SIDONGAN')

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
@endphp

<div class="dashboard-container">
    {{-- Header --}}
    <div class="dashboard-header">
        <h1>Selamat Datang di SIDONGAN</h1>
        <p>Dashboard admin untuk mengelola dokumen organisasi, agenda, dan naskah PKK Kabupaten Toba.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-grid">
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Total Surat',
            'value' => $totalSurat ?? 0,
            'icon' => 'fa-envelope',
            'color' => 'blue'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Sedang Berjalan',
            'value' => $sedangBerjalan ?? 0,
            'icon' => 'fa-spinner fa-spin',
            'color' => 'orange'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Menunggu Proses',
            'value' => $menungguProses ?? 0,
            'icon' => 'fa-clock',
            'color' => 'yellow'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Selesai',
            'value' => $selesai ?? 0,
            'icon' => 'fa-check-circle',
            'color' => 'green'
        ])
        
        @include('sidongan.dashboard.components.stat-card', [
            'title' => 'Diarsipkan',
            'value' => $diarsipkan ?? 0,
            'icon' => 'fa-archive',
            'color' => 'purple'
        ])
    </div>

    {{-- Aksi Cepat --}}
    <div class="section-spacing">
        @include('sidongan.dashboard.components.quick-actions')
    </div>

    {{-- Surat Terbaru & Notifikasi --}}
    <div class="dashboard-grid">
        {{-- Surat Terbaru --}}
        <div class="card">
            <div class="card-header">
                <h3>Surat Terbaru</h3>
                <a href="{{ route('sidongan.documents.index') }}" class="link">Lihat Semua →</a>
            </div>
            
            <div class="card-body p-0">
                @forelse($recentDocuments as $doc)
                    @include('sidongan.dashboard.components.document-item', ['doc' => $doc])
                @empty
                    @include('sidongan.dashboard.components.empty-state', [
                        'icon' => 'fa-inbox',
                        'message' => 'Belum ada surat'
                    ])
                @endforelse
            </div>
        </div>

        {{-- Notifikasi --}}
        <div class="card">
            <div class="card-header">
                <h3>Notifikasi</h3>
                <a href="{{ route('sidongan.notifications') }}" class="link">Semua →</a>
            </div>
            
            <div class="card-body notification-list">
                @forelse($notifications as $notif)
                    @include('sidongan.dashboard.components.notification-item', ['notif' => $notif])
                @empty
                    @include('sidongan.dashboard.components.empty-state', [
                        'icon' => 'fa-check-circle',
                        'title' => 'Semua Notifikasi Sudah Dibaca',
                        'message' => 'Tidak ada notifikasi baru',
                        'color' => 'green'
                    ])
                @endforelse
            </div>
        </div>
    </div>

    {{-- Alur Proses Surat --}}
    <div class="workflow-section">
        @include('sidongan.dashboard.components.workflow')
    </div>

    {{-- Chart & Activity Log --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
        {{-- Chart --}}
        <div class="card" style="padding: 1.5rem;">
            <div class="card-header" style="margin-bottom: 1rem;">
                <h3>Tren Surat 6 Bulan Terakhir</h3>
            </div>
            <div style="position: relative; height: 250px;">
                <canvas id="sidonganChart"></canvas>
            </div>
        </div>

        {{-- Activity Log --}}
        <div class="card" style="padding: 1.5rem;">
            <div class="card-header" style="margin-bottom: 1rem;">
                <h3>Aktivitas Terbaru</h3>
            </div>
            @if(isset($recentActivity) && $recentActivity->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($recentActivity->take(8) as $activity)
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem; border-radius: 0.5rem; transition: background 0.2s;">
                    <div style="width: 2.25rem; height: 2.25rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: {{ $activity['color'] === 'blue' ? '#dbeafe' : '#dcfce7' }};">
                        <i class="fas {{ $activity['icon'] }}" style="color: {{ $activity['color'] === 'blue' ? '#2563eb' : '#16a34a' }}; font-size: 0.85rem;"></i>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 0.85rem; font-weight: 600; color: #0f172a; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $activity['title'] }}</p>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $activity['user'] }} &middot; {{ $activity['time']->locale('id')->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align: center; padding: 2rem; color: #94a3b8;">
                <i class="fas fa-clock" style="font-size: 2rem; margin-bottom: 0.75rem; display: block;"></i>
                <p style="margin: 0;">Belum ada aktivitas</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- CSS Dashboard --}}
<link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-index.css') }}">

{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4/dist/chart.umd.min.js"></script>

<script>
(function() {
    var ctx = document.getElementById('sidonganChart');
    if (!ctx) return;

    var now = new Date();
    var labels = [];
    for (var i = 5; i >= 0; i--) {
        var d = new Date(now.getFullYear(), now.getMonth() - i, 1);
        labels.push(d.toLocaleDateString('id-ID', { month: 'short', year: '2-digit' }));
    }

    // Data statis untuk bulan ini; idealnya dari controller
    var suratData = [{{ $totalSurat ?? 0 }}, 0, 0, 0, 0, 0];

    var gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(20,184,166,0.3)');
    gradient.addColorStop(1, 'rgba(20,184,166,0.02)');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Surat Masuk',
                data: suratData,
                backgroundColor: gradient,
                borderColor: '#14b8a6',
                borderWidth: 2,
                borderRadius: 6,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
                x: { ticks: { font: { size: 11 } } }
            }
        }
    });
})();
</script>

{{-- Script untuk mark as read + redirect --}}
<script src="{{ asset('assets/sidongan/js/sidongan-dashboard-index.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
