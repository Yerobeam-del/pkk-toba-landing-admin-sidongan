{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="notification-item" data-notif-id="{{ $notif->id }}" data-notif-url="{{ route('sidongan.documents.show', $notif->related_id) }}">
    <div class="notification-content">
        <div class="notification-icon">
            <i class="fas fa-bell"></i>
        </div>
        <div class="notification-text">
            <p class="notification-message">{{ Str::limit($notif->message, 80) }}</p>
            <span class="notification-time">{{ $notif->created_at->locale('id')->translatedFormat('d F Y, H.i') }}</span>
        </div>
    </div>
</div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-components-notification-item.css') }}">
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
