<div class="notification-item" onclick="markNotificationReadAndRedirect({{ $notif->id }}, '{{ route('sidongan.documents.show', $notif->related_id) }}')">
    <div class="notification-content">
        <div class="notification-icon">
            <i class="fas fa-bell"></i>
        </div>
        <div class="notification-text">
            <p class="notification-message">{{ Str::limit($notif->message, 80) }}</p>
            <span class="notification-time">{{ $notif->created_at->locale('id')->translatedFormat('d M Y, H.i') }}</span>
        </div>
    </div>
</div>

<style>
    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        background: #eff6ff;
        border-radius: 0.5rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .notification-item:hover {
        background: #dbeafe;
    }

    .notification-content {
        display: flex;
        gap: 0.75rem;
        align-items: start;
    }

    .notification-icon {
        width: 2.5rem;
        height: 2.5rem;
        background: #dbeafe;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-icon i {
        color: #3b82f6;
        font-size: 0.85rem;
    }

    .notification-text {
        flex: 1;
        min-width: 0;
    }

    .notification-message {
        font-size: 0.85rem;
        color: #0f172a;
        margin: 0 0 0.25rem 0;
        line-height: 1.4;
        font-weight: 500;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #94a3b8;
    }
</style>