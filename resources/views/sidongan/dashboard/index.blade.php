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
</div>

{{-- CSS Dashboard --}}
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-index.css') }}">


{{-- Script untuk mark as read + redirect --}}
    <script src="{{ asset('assets/sidongan/js/sidongan-dashboard-index.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
