{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div id="global-loading-screen" class="loading-screen">
    {{-- Animated Clouds Background --}}
    <div class="cloud c1"></div>
    <div class="cloud c2"></div>
    <div class="cloud c3"></div>
    <div class="cloud c4"></div>

    {{-- Logo PKK dengan Animasi Loading --}}
    <div class="loading-wrapper">
        <div class="logo-container">
            <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="Logo PKK">
        </div>

        {{-- Loading Dots --}}
        <div class="loading-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/shared/css/components-loading-screen.css') }}">
    @endpush
    @endonce


    @once
    @push('scripts')
    <script src="{{ asset('assets/shared/js/components-loading-screen.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
