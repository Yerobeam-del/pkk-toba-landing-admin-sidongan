{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Notifikasi - SIDONGAN')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-notifications-index.css') }}">


<div class="sd-notif-wrapper">
    {{-- Header --}}
    <div class="animate-slide-in u-mb-8" style="animation-delay: 0s;">
        <h1 class="u-h2-slate">
            Notifikasi
        </h1>
        <p class="u-text-muted-lead">
            Pusat informasi dan pemberitahuan aktivitas sistem
        </p>
    </div>

    {{-- Stats Card --}}
    <div class="stats-card animate-slide-in sd-notif-stats">
        <div class="u-deco-circle-tr"></div>
        <div class="u-deco-circle-bl"></div>
        
        <div class="sd-notif-stats-inner">
            <div class="u-flex-1">
                <p class="u-a7">
                    Notifikasi Baru
                </p>
                <p class="u-a8" id="unreadCount">
                    {{ $unreadCount }}
                </p>
            </div>
            <div class="u-icon-badge-lg">
                <svg class="sd-notif-stats-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @if($unreadCount > 0)
    <div class="sd-notif-mark-all-wrap animate-slide-in">
        <button data-action="hapus-semua-notifikasi" 
                class="sd-notif-mark-all btn-action">
            <svg class="sd-notif-mark-all-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Tandai Semua Sudah Dibaca
        </button>
    </div>
    @endif

    {{-- List --}}
    <div class="sd-notif-list animate-slide-in">
        @forelse($notifications as $notification)
        <div class="sd-notif-item notif-item animate-slide-in" 
             data-notif-id="{{ $notification->id }}">
            
            {{-- Icon --}}
            <div class="u-shrink-0">
                <div class="sd-notif-icon">
                    <svg class="sd-notif-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Content --}}
            <div class="u-flex-1-min">
                <p class="sd-notif-title">
                    {{ $notification->title }}
                </p>
                <p class="sd-notif-message">
                    {{ $notification->message }}
                </p>
                <p class="sd-notif-time">
                    <svg class="sd-notif-time-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $notification->created_at->locale('id')->translatedFormat('d F Y, H:i') }}
                </p>
            </div>
            
            {{-- Unread Indicator --}}
            <div class="sd-notif-dot-wrap">
                <span class="pulse-dot sd-notif-dot"></span>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div class="sd-notif-empty animate-slide-in">
            <div class="sd-notif-empty-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="sd-notif-empty-title">Semua Notifikasi Sudah Dibaca</h3>
            <p class="sd-notif-empty-desc">Tidak ada notifikasi baru saat ini.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div class="sd-notif-pagination animate-slide-in">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

    <script src="{{ asset('assets/sidongan/js/sidongan-notifications-index.js') }}"></script>


    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-notifications-index.css') }}">

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
