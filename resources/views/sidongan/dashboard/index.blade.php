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
</div>

{{-- CSS Dashboard --}}
<style>
    .dashboard-container {
        padding: 0 1.5rem;
        padding-bottom: 0;
    }

    .dashboard-header {
        margin-bottom: 2rem;
    }

    .dashboard-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .dashboard-header p {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
        align-items: stretch;
    }

    .card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 1.5rem;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card:last-child {
        margin-bottom: 0 !important;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-body.p-0 {
        padding: 0;
    }

    .link {
        font-size: 0.875rem;
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
    }

    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .section-spacing {
        margin-bottom: 2rem;
    }

    .workflow-section {
        margin-bottom: 0 !important;
    }

    .workflow-section .card {
        margin-bottom: 0 !important;
    }

    /* Mobile Responsive */
    @media (max-width: 1024px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .section-spacing {
            margin-bottom: 1.5rem;
        }
    }
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    /* Card terakhir (Diarsipkan) full width di mobile */
    .stat-card:last-of-type,
    .stats-grid > div:last-child {
        grid-column: 1 / -1 !important;
        width: 100% !important;
    }

    .dashboard-header {
        margin-bottom: 1rem;
    }

    .dashboard-header h1 {
        font-size: 1.25rem;
    }

    .dashboard-header p {
        font-size: 0.8rem;
    }

    .dashboard-grid {
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .section-spacing {
        margin-bottom: 1rem;
    }

    .card {
        margin-bottom: 1rem;
    }
}
</style>

{{-- Script untuk mark as read + redirect --}}
<script>
function markNotificationReadAndRedirect(notificationId, redirectUrl) {
    fetch(`/sidongan/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.href = redirectUrl;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        window.location.href = redirectUrl;
    });
}
</script>
@endsection