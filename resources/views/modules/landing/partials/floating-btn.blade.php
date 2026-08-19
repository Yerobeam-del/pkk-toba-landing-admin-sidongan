{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@php
    // $floatingApps disediakan oleh FloatingButtonComposer (lihat AppServiceProvider).
    // Jangan query ulang di sini — dulu ada @php yang menimpanya sehingga
    // composer-nya tidak pernah berpengaruh.

    // Warna diturunkan dari kolom `color` di Admin Panel > Manajemen Aplikasi.
    // Rumusnya sama dengan halaman Aplikasi & beranda agar seluruh landing page konsisten.
    $fabWarna = function ($hex, $rasioPutih) {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $m = fn($v) => (int) round($v + (255 - $v) * $rasioPutih);
        return sprintf('rgb(%d, %d, %d)', $m($r), $m($g), $m($b));
    };
@endphp

{{-- Floating App Button --}}
<div class="floating-app-btn" id="floatingAppBtn">
    {{-- Menu Items --}}
    <div class="floating-menu" id="floatingMenu" style="pointer-events: none;">
        @forelse($floatingApps as $app)
        @php $warna = $app->effective_color; @endphp
        <a href="{{ $app->url && $app->url !== '#' ? $app->url : '#' }}"
           target="{{ $app->url && $app->url !== '#' ? '_blank' : '_self' }}"
           class="floating-menu-item"
           style="pointer-events: auto; --fab-c: {{ $warna }}; --fab-c-2: {{ $fabWarna($warna, 0.28) }};">
            <span class="floating-menu-label">{{ $app->short_name ?? $app->name }}</span>
            <div class="floating-menu-icon">
                @if($app->icon)
                    <img src="{{ asset('storage/' . $app->icon) }}"
                         alt="{{ $app->short_name }}"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span style="display:none;">{{ substr($app->short_name ?? 'AP', 0, 2) }}</span>
                @else
                    <span>{{ substr($app->short_name ?? 'AP', 0, 2) }}</span>
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
    <button class="floating-trigger" id="floatingTrigger" type="button" data-action="toggle-floating">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
            <polyline points="2 17 12 22 22 17"></polyline>
            <polyline points="2 12 12 17 22 12"></polyline>
        </svg>
        <div class="floating-trigger-pulse"></div>
    </button>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-partials-floating-btn.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
