{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@php
    $user = auth()->guard('sidongan')->user();
    $currentUser = $user;
    
    if ($user) {
        $sidonganNotifications = \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->latest()
            ->take(5)
            ->get();
            
        $sidonganUnreadCount = \App\Models\Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    } else {
        $sidonganNotifications = collect();
        $sidonganUnreadCount = 0;
    }
@endphp

<header class="top-header">
    <button class="toggle-btn" id="toggleBtn" title="Toggle Sidebar">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="9" y1="3" x2="9" y2="21"></line>
        </svg>
    </button>
    <div class="header-right u-relative">
        
        {{-- Dark Mode Toggle --}}
        <button data-action="toggle-dark-mode" class="toggle-btn" title="Ganti tema">
            <i class="fas fa-moon sd-dark-icon" style="font-size: 1rem;"></i>
            <i class="fas fa-sun sd-light-icon" style="font-size: 1rem; display: none;"></i>
        </button>

        <div class="u-relative">
            <button data-action="toggle-notification-popup" class="toggle-btn" style="position: relative; margin-right: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                @if($sidonganUnreadCount > 0)
                <span style="position: absolute; top: 2px; right: 2px; width: 16px; height: 16px; background: #ef4444; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center;">
                    <span style="color: white; font-size: 10px; font-weight: 700; line-height: 1;">{{ $sidonganUnreadCount > 9 ? '9+' : $sidonganUnreadCount }}</span>
                </span>
                @endif
            </button>
            
            <div id="notificationPopup" style="display: none; position: absolute; right: 0; top: calc(100% + 0.5rem); width: 400px; background: var(--card-bg, #fff); border-radius: 0.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border: 1px solid var(--border-light, #e2e8f0); z-index: 1000;">
                
                <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-light, #e2e8f0); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-dark, #1e293b); margin: 0;">Notifikasi</h3>
                    @if($sidonganUnreadCount > 0)
                    <button data-action="mark-all-read" style="font-size: 0.75rem; color: #2563eb; background: none; border: none; cursor: pointer; font-weight: 500;">Tandai semua dibaca</button>
                    @endif
                </div>
                
                <div style="max-height: 350px; overflow-y: auto;">
                    @forelse($sidonganNotifications as $notif)
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-light, #f1f5f9); background: #eff6ff; cursor: pointer; transition: background 0.2s;"
                        data-notif-id="{{ $notif->id }}" data-notif-url="{{ route('sidongan.documents.show', $notif->related_id) }}">
                        <div style="display: flex; gap: 0.75rem; align-items: start;">
                            <div style="width: 2rem; height: 2rem; background: #dbeafe; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-bell" style="color: #3b82f6; font-size: 0.85rem;"></i>
                            </div>
                            <div class="u-flex-1-min">
                                <p style="font-size: 0.85rem; font-weight: 500; color: var(--text-dark, #0f172a); margin: 0 0 0.25rem 0; line-height: 1.4;">
                                    {{ Str::limit($notif->message, 80) }}
                                </p>
                                <span style="font-size: 0.7rem; color: var(--text-muted, #94a3b8);">
                                    {{ $notif->created_at->locale('id')->translatedFormat('d F Y, H.i') }}
                                </span>
                            </div>
                            <div style="width: 0.5rem; height: 0.5rem; background: #3b82f6; border-radius: 50%; flex-shrink: 0; margin-top: 0.5rem;"></div>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 3rem 1.25rem; text-align: center;">
                        <div style="width: 64px; height: 64px; background: var(--surface-bg, #f0fdf4); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                            <svg style="width: 2rem; height: 2rem; stroke: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <p style="font-size: 0.9rem; color: var(--text-dark, #1e293b); margin: 0; font-weight: 600;">Semua Notifikasi Sudah Dibaca</p>
                        <p style="font-size: 0.8rem; color: var(--text-muted, #64748b); margin: 0.25rem 0 0 0;">Tidak ada notifikasi baru</p>
                    </div>
                    @endforelse
                </div>
                
                <div style="padding: 0.75rem 1.25rem; border-top: 1px solid var(--border-light, #e2e8f0); text-align: center;">
                    <a href="{{ route('sidongan.notifications') }}" style="font-size: 0.875rem; color: #2563eb; text-decoration: none; font-weight: 500;">Lihat Semua Notifikasi →</a>
                </div>
            </div>
        </div>
        
        @if($currentUser)
        <button data-action="toggle-user-menu" class="user-profile-btn" style="display:flex;align-items:center;gap:0.75rem;background:none;border:none;cursor:pointer;padding:0.5rem 0.75rem;border-radius:8px;transition:background 0.2s">
            <div class="user-text" style="text-align:right;display:flex;flex-direction:column;align-items:flex-end">
                <span style="font-weight:600;font-size:0.9rem;color:var(--text-dark,#334155);line-height:1.2">{{ $currentUser->name }}</span>
                <span style="font-size:0.7rem;color:var(--text-muted,#94a3b8)">{{ $currentUser->sidongan_role_name }}</span>
            </div>
            @php
                $nameParts = explode(' ', $currentUser->name);
                $initials = count($nameParts) >= 2 
                    ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                    : strtoupper(substr($currentUser->name, 0, 2));
            @endphp
            <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,{{ $currentUser->sidongan_role === 'ketua' ? '#dc2626' : ($currentUser->sidongan_role === 'sekretaris' ? '#2563eb' : '#4f46e5') }},#14b8a6);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid var(--card-bg, #fff);box-shadow:0 2px 4px rgba(0,0,0,0.1)">
                @if($currentUser->avatar)
                    <img src="{{ asset('storage/' . $currentUser->avatar) }}" 
                        alt="{{ $currentUser->name }}" 
                        style="width:100%;height:100%;object-fit:cover" 
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span style="display:none;color:#fff;font-weight:700;font-size:0.85rem;letter-spacing:0.5px;">{{ $initials }}</span>
                @else
                    <span style="color:#fff;font-weight:700;font-size:0.85rem;letter-spacing:0.5px;">{{ $initials }}</span>
                @endif
            </div>
            <svg id="userMenuArrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted, #64748b)" stroke-width="2" style="transition:transform 0.2s">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>            <div id="userMenu" class="user-menu">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--border-light, #f1f5f9)">
                <div style="font-weight:600;font-size:0.9rem;color:var(--text-dark,#334155)">{{ $currentUser->name }}</div>
                <div style="font-size:0.75rem;color:var(--text-muted,#64748b)">{{ $currentUser->sidongan_role_name }}</div>
            </div>
            <div style="padding:0.5rem 0;border-bottom:1px solid var(--border-light, #f1f5f9)">
                <a href="{{ route('sidongan.profile.edit') }}" style="width:100%;display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;background:none;border:none;cursor:pointer;color:var(--text-dark,#334155);transition:background 0.2s;text-align:left;font-size:0.85rem;text-decoration:none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Edit Profil</span>
                </a>
            </div>
            <div style="padding:0.5rem 0;border-bottom:1px solid var(--border-light, #f1f5f9)">
                <a href="https://{{ config('app.landing_domain', 'tp-pkk.tobakab.go.id') }}/personal-email" target="_blank" rel="noopener noreferrer" style="width:100%;display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;background:none;border:none;cursor:pointer;color:var(--text-dark,#334155);transition:background 0.2s;text-align:left;font-size:0.85rem;text-decoration:none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 7l-10 7L2 7"/>
                    </svg>
                    <span>Pengaturan Email</span>
                </a>
            </div>
            <form method="POST" action="{{ route('sidongan.logout') }}" style="padding:0.5rem 0">
                @csrf
                <button type="submit" style="width:100%;display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;background:none;border:none;cursor:pointer;color:#ef4444;transition:background 0.2s;text-align:left;font-size:0.9rem">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
        @endif
    </div>
</header>

{{-- Dark Mode Toggle Script --}}
<script>
(function() {
    var html = document.documentElement;
    var isDark = html.classList.contains('dark-mode');
    var sunIcon = document.querySelector('.sd-light-icon');
    var moonIcon = document.querySelector('.sd-dark-icon');
    
    function updateIcons() {
        var dark = html.classList.contains('dark-mode');
        if (sunIcon) sunIcon.style.display = dark ? 'inline' : 'none';
        if (moonIcon) moonIcon.style.display = dark ? 'none' : 'inline';
    }
    updateIcons();
    
    document.querySelectorAll('[data-action="toggle-dark-mode"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            html.classList.toggle('dark-mode');
            var dark = html.classList.contains('dark-mode');
            localStorage.setItem('sidongan-theme', dark ? 'dark' : 'light');
            updateIcons();
        });
    });
})();
</script>

{{-- Dikembangkan oleh Institut Teknologi Del --}}
