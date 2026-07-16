@extends('sidongan.layouts.app')
@section('title', 'Notifikasi - SIDONGAN')

@section('content')
<style>
    .notif-item {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 3px solid transparent;
        position: relative;
        overflow: hidden;
    }
    .notif-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, #f97316, #ea580c);
        opacity: 0;
        transition: opacity 0.3s;
    }
    .notif-item:hover {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        border-left-color: #f97316;
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
    }
    .notif-item:hover::before {
        opacity: 1;
    }
    .notif-item.read {
        opacity: 0.7;
        background: #f9fafb;
    }
    .stats-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(249, 115, 22, 0.2);
    }
    .btn-action {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .animate-slide-in {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    .pulse-dot {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
    }
</style>

<div style="max-width: 900px; margin: 0 auto;">
    {{-- Header --}}
    <div style="margin-bottom: 2rem;" class="animate-slide-in" style="animation-delay: 0s;">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">
            Notifikasi
        </h1>
        <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">
            Pusat informasi dan pemberitahuan aktivitas sistem
        </p>
    </div>

    {{-- Stats Card --}}
    <div class="stats-card animate-slide-in" 
         style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; margin-bottom: 2rem; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);"
         style="animation-delay: 0.1s;">
        <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
        <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative; z-index: 1;">
            <div style="flex: 1;">
                <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                    Notifikasi Baru
                </p>
                <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;" id="unreadCount">
                    {{ $unreadCount }}
                </p>
            </div>
            <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <svg style="width: 2.5rem; height: 2.5rem; stroke: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    @if($unreadCount > 0)
    <div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;" class="animate-slide-in" style="animation-delay: 0.2s;">
        <button onclick="markAllAsRead()" 
                class="btn-action"
                style="background: white; border: 2px solid #ea580c; color: #ea580c; padding: 0.625rem 1.25rem; border-radius: 0.625rem; cursor: pointer; font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 2px 8px rgba(234, 88, 12, 0.1);">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Tandai Semua Sudah Dibaca
        </button>
    </div>
    @endif

    {{-- List --}}
    <div style="background: white; border-radius: 1rem; border: 1px solid #e5e7eb; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);" class="animate-slide-in" style="animation-delay: 0.3s;">
        @forelse($notifications as $notification)
        <div class="notif-item animate-slide-in" 
             style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6; display: flex; gap: 1.25rem; align-items: flex-start; cursor: pointer; animation-delay: {{ 0.4 + ($loop->index * 0.1) }}s;"
             onclick="markAsRead({{ $notification->id }}, this)">
            
            {{-- Icon --}}
            <div style="flex-shrink: 0;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #ffedd5, #fed7aa); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(249, 115, 22, 0.15);">
                    <svg style="width: 24px; height: 24px; stroke: #ea580c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
            </div>
            
            {{-- Content --}}
            <div style="flex: 1; min-width: 0;">
                <p style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0 0 0.5rem 0; line-height: 1.4;">
                    {{ $notification->title }}
                </p>
                <p style="font-size: 0.9rem; color: #4b5563; margin: 0 0 0.75rem 0; line-height: 1.6;">
                    {{ $notification->message }}
                </p>
                <p style="font-size: 0.8rem; color: #9ca3af; margin: 0; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ $notification->created_at->locale('id')->translatedFormat('d M Y, H:i') }}
                </p>
            </div>
            
            {{-- Unread Indicator --}}
            <div style="flex-shrink: 0; padding-top: 8px;">
                <span class="pulse-dot" style="display: inline-flex; width: 10px; height: 10px; background: #ea580c; border-radius: 50%; box-shadow: 0 0 0 0 rgba(234, 88, 12, 0.7);"></span>
            </div>
        </div>
        @empty
        {{-- Empty State --}}
        <div style="text-align: center; padding: 4rem 2rem;" class="animate-slide-in" style="animation-delay: 0.4s;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); animation: float 3s ease-in-out infinite;">
                <svg style="width: 40px; height: 40px; stroke: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 0.5rem 0;">Semua Notifikasi Sudah Dibaca</h3>
            <p style="font-size: 0.95rem; color: #6b7280; margin: 0; line-height: 1.6;">Tidak ada notifikasi baru saat ini.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($notifications->hasPages())
    <div style="margin-top: 2rem;" class="animate-slide-in" style="animation-delay: 0.5s;">
        {{ $notifications->links() }}
    </div>
    @endif
</div>

<script>
function markAsRead(notificationId, element) {
    element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
    element.style.opacity = '0';
    element.style.transform = 'translateX(20px)';
    
    fetch(`/sidongan/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            setTimeout(() => {
                element.remove();
                let el = document.getElementById('unreadCount');
                if(el) {
                    let count = parseInt(el.textContent) - 1;
                    el.textContent = Math.max(0, count);
                    if(count === 0) {
                        setTimeout(() => location.reload(), 500);
                    }
                }
            }, 300);
        } else {
            element.style.opacity = '1';
            element.style.transform = 'translateX(0)';
        }
    })
    .catch(() => {
        element.style.opacity = '1';
        element.style.transform = 'translateX(0)';
    });
}

function markAllAsRead() {
    if(confirm('Apakah Anda yakin ingin menghapus semua notifikasi?')) {
        fetch('/sidongan/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Animate out all notifications
                document.querySelectorAll('.notif-item').forEach((item, index) => {
                    setTimeout(() => {
                        item.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                    }, index * 50);
                });
                
                setTimeout(() => {
                    location.reload();
                }, 500);
            }
        });
    }
}
</script>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
</style>
@endsection