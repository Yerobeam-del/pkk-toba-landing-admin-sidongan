{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<section class="hero">
    {{-- 1. Container Background Slider --}}
    <div class="hero-bg-slider" id="heroBgSlider">
        {{-- Fallback jika JS gagal load --}}
        <div class="hero-bg-slide active" style="background-image: url('{{ asset('assets/landing/images/Background_1.jpg') }}')"></div>
    </div>

    {{-- 2. Overlay --}}
    <div class="hero-bg-overlay"></div>

    {{-- 3. Particles --}}
    <div class="hero-particles" id="particles"></div>

    {{-- 4. Konten Hero --}}
    <div class="hero-content">
        {{-- Badge --}}
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            <span>Portal Resmi Digital</span>
        </div>

        {{-- Logo --}}
        <div class="hero-logo-container">
            <img src="{{ asset('assets/landing/images/PKK-Logo.png') }}" alt="PKK Logo" class="hero-logo">
            <div class="hero-logo-glow"></div>
        </div>

        {{-- Heading --}}
        <h1>
            Selamat Datang di Portal<br>
            <span class="highlight">PKK Kabupaten Toba</span>
        </h1>

        {{-- Subtitle --}}
        <p class="hero-subtitle">
            Melayani masyarakat Kabupaten Toba melalui transformasi digital untuk pemberdayaan keluarga dan kesejahteraan masyarakat yang lebih baik.
        </p>

        {{-- CTA Button --}}
        <a href="#quickAccess" data-action="scroll-quick-access" class="hero-cta" role="button" aria-label="Jelajahi Layanan">
            <span>Jelajahi Layanan</span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 17l9.2-9.2M17 17V7.8H7.8"/>
            </svg>
        </a>
    </div>

    {{-- 5. Indicators/Dots --}}
    <div class="hero-slider-indicators" id="sliderIndicators"></div>
</section>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-hero.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
