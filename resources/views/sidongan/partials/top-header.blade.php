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
        
        {{-- Pemilih Tema 3-mode (ala SIEDA): System (ikut perangkat) / Terang / Gelap --}}
        <div class="theme-picker theme-picker--collapsed" data-theme-picker>
            <button type="button" class="theme-picker__opt" data-theme-option="system" title="Ikuti tema perangkat" aria-label="Tema sistem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
            </button>
            <button type="button" class="theme-picker__opt" data-theme-option="light" title="Tema terang" aria-label="Tema terang">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
            </button>
            <button type="button" class="theme-picker__opt" data-theme-option="dark" title="Tema gelap" aria-label="Tema gelap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>
            <span class="theme-picker__thumb" data-theme-thumb></span>
        </div>

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
                    @php
                        /* URL tujuan bergantung jenis notifikasi: Surat Keluar
                           (related_type = OutgoingLetterController) ke detail
                           Surat Keluar; sisanya ke detail Surat Masuk. Notifikasi
                           yang suratnya sudah tidak ada tidak diberi link. */
                        if ($notif->related_type === \App\Http\Controllers\Sidongan\OutgoingLetterController::class) {
                            $notifUrl = \App\Models\OutgoingLetter::find($notif->related_id)
                                ? route('sidongan.outgoing.show', $notif->related_id)
                                : null;
                        } else {
                            $notifUrl = route('sidongan.documents.show', $notif->related_id);
                        }
                    @endphp
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--border-light, #f1f5f9); background: #eff6ff; cursor: pointer; transition: background 0.2s;"
                        data-notif-id="{{ $notif->id }}" @if($notifUrl) data-notif-url="{{ $notifUrl }}" @endif>
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
                    <a href="{{ route('sidongan.notifications') }}" style="font-size: 0.875rem; color: #2563eb; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 0.3rem;">Lihat Semua Notifikasi<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
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
                <a href="{{ route('workspace.pilih') }}" style="width:100%;display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;background:none;border:none;cursor:pointer;color:var(--text-dark,#334155);transition:background 0.2s;text-align:left;font-size:0.85rem;text-decoration:none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="3" width="7" height="7" rx="1"/>
                        <rect x="14" y="14" width="7" height="7" rx="1"/>
                        <rect x="3" y="14" width="7" height="7" rx="1"/>
                    </svg>
                    <span>Ganti Ruang Kerja</span>
                </a>
                <a href="{{ route('sidongan.profile.edit') }}" style="width:100%;display:flex;align-items:center;gap:0.75rem;padding:0.65rem 1rem;background:none;border:none;cursor:pointer;color:var(--text-dark,#334155);transition:background 0.2s;text-align:left;font-size:0.85rem;text-decoration:none;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>Edit Profil</span>
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

{{-- Pemilih Tema 3-mode (ala SIEDA): ringkas saat idle (hanya ikon mode aktif),
     terbuka saat diklik untuk memilih System/Terang/Gelap, menutup otomatis setelah memilih --}}
<script>
(function() {
    var KEY = 'sidongan-theme';
    var html = document.documentElement;
    var picker = document.querySelector('[data-theme-picker]');
    if (!picker) return;

    function currentMode() {
        var s = localStorage.getItem(KEY);
        return (s === 'light' || s === 'dark') ? s : 'system';
    }

    function activeOpt() {
        return picker.querySelector('[data-theme-option="' + currentMode() + '"]');
    }

    function updateThumb() {
        var opt = activeOpt();
        if (!opt) return;
        var thumb = picker.querySelector('[data-theme-thumb]');
        thumb.style.transform = 'translateX(' + opt.offsetLeft + 'px)';
        thumb.style.width = opt.offsetWidth + 'px';
        picker.querySelectorAll('[data-theme-option]').forEach(function(b) {
            b.classList.toggle('active', b === opt);
        });
    }

    function isCollapsed() { return picker.classList.contains('theme-picker--collapsed'); }

    function collapse() { picker.classList.add('theme-picker--collapsed'); }

    function expand() {
        picker.classList.remove('theme-picker--collapsed');
        updateThumb();
    }

    // Perangkat dengan kursor (desktop): buka/tutup via hover. Perangkat sentuh: via ketukan.
    var canHover = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    picker.addEventListener('click', function(e) {
        // Ringkas: klik pertama hanya membuka pemilih (alur perangkat sentuh — di desktop dibuka via hover)
        if (isCollapsed()) {
            e.stopPropagation();
            expand();
            return;
        }
        var opt = e.target.closest('[data-theme-option]');
        if (!opt) return;
        var mode = opt.getAttribute('data-theme-option');
        if (mode === 'light' || mode === 'dark') {
            localStorage.setItem(KEY, mode);
        } else {
            localStorage.removeItem(KEY); // system = tidak ada preferensi tersimpan
        }
        var mq = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
        var dark = (mode === 'dark') || (mode !== 'light' && mq && mq.matches);
        html.classList.toggle('dark-mode', dark);
        updateThumb();
        // Beri tahu elemen lain (mis. favicon) bahwa tema berubah
        document.dispatchEvent(new CustomEvent('sidongan-theme-changed'));
        // Di perangkat sentuh (tanpa hover): tutup kembali setelah memilih.
        // Di desktop, penutupan ditangani mouseleave agar picker tetap terbuka selama kursor masih di atasnya.
        if (!canHover) setTimeout(collapse, 450);
    });

    // Desktop: terbuka saat kursor melayang di atasnya, menutup saat kursor pergi.
    // Delay singkat mencegah terbuka/menutup saat kursor hanya melintas.
    var expandTimer = null, collapseTimer = null;
    picker.addEventListener('mouseenter', function() {
        if (!canHover) return;
        clearTimeout(collapseTimer);
        if (isCollapsed()) expandTimer = setTimeout(expand, 100);
    });
    picker.addEventListener('mouseleave', function() {
        if (!canHover) return;
        clearTimeout(expandTimer);
        collapseTimer = setTimeout(collapse, 250);
    });

    // Klik di luar pemilih menutupnya kembali
    document.addEventListener('click', function(e) {
        if (!picker.contains(e.target)) collapse();
    });

    // Ikon harus mengikuti bila tema berubah dari luar (mis. tema perangkat berubah saat mode system)
    document.addEventListener('sidongan-theme-changed', updateThumb);
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateThumb);
    } else {
        updateThumb();
    }
    window.addEventListener('resize', updateThumb);
})();
</script>

{{-- Dikembangkan oleh Institut Teknologi Del --}}
