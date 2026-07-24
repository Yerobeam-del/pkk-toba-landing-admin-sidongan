@php
// Query aplikasi yang aktif, status active, DAN show_in_floating = true
$floatingApps = \App\Models\Application::where('is_active', true)
    ->where('status', 'active')
    ->where('show_in_floating', true)
    ->orderBy('sort_order')
    ->get();
@endphp

{{-- Floating App Button --}}
<div class="floating-app-btn" id="floatingAppBtn">
    {{-- Menu Items --}}
    <div class="floating-menu" id="floatingMenu" style="pointer-events: none;">
        @forelse($floatingApps as $app)
        <a href="{{ $app->url && $app->url !== '#' ? $app->url : '#' }}"
           target="{{ $app->url && $app->url !== '#' ? '_blank' : '_self' }}"
           class="floating-menu-item"
           style="pointer-events: auto;">
            <span class="floating-menu-label">{{ $app->name }}</span>
            <div class="floating-menu-icon">
                @if($app->icon)
                    <img src="{{ asset('storage/' . $app->icon) }}"
                         alt="{{ $app->short_name }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span style="display:none;">{{ substr($app->short_name, 0, 2) }}</span>
                @else
                    <span>{{ substr($app->short_name, 0, 2) }}</span>
                @endif
            </div>
        </a>
        @empty
        <a href="#" class="floating-menu-item" style="pointer-events: auto;">
            <span class="floating-menu-label">Tidak Ada Aplikasi</span>
            <div class="floating-menu-icon">
                <span>NA</span>
            </div>
        </a>
        @endforelse
    </div>

    {{-- Trigger Button --}}
    <button class="floating-trigger" id="floatingTrigger" type="button" onclick="toggleFloatingMenu(event)">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
            <polyline points="2 17 12 22 22 17"></polyline>
            <polyline points="2 12 12 17 22 12"></polyline>
        </svg>
        <div class="floating-trigger-pulse"></div>
    </button>
</div>

<script>
function toggleFloatingMenu(event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');
    if (!btn || !menu) return;

    const isOpen = btn.classList.contains('open');

    if (isOpen) {
        // Tutup menu
        btn.classList.remove('open');
        // Nonaktifkan pointer events setelah animasi selesai (misal 300ms)
        setTimeout(() => {
            if (!btn.classList.contains('open')) {
                menu.style.pointerEvents = 'none';
            }
        }, 300);
    } else {
        // Buka menu
        btn.classList.add('open');
        // Aktifkan pointer events segera agar bisa diklik
        menu.style.pointerEvents = 'auto';
    }
}

// Close menu when clicking outside
document.addEventListener('click', function(e) {
    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');

    if (btn && menu && !btn.contains(e.target)) {
        btn.classList.remove('open');
        setTimeout(() => {
            if (!btn.classList.contains('open')) {
                menu.style.pointerEvents = 'none';
            }
        }, 300);
    }
});

// Close menu on scroll (opsional, untuk UX mobile yang lebih baik)
let scrollTimeout;
window.addEventListener('scroll', function() {
    const btn = document.getElementById('floatingAppBtn');
    const menu = document.getElementById('floatingMenu');
    if (btn && menu && btn.classList.contains('open')) {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            btn.classList.remove('open');
            menu.style.pointerEvents = 'none';
        }, 100);
    }
}, { passive: true });
</script>
