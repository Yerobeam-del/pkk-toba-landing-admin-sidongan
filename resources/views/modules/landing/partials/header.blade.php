{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<nav class="navbar" id="navbar" data-home-url="{{ route("landing.home") }}">
    {{-- ... CSS tetap sama ... --}}
        @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/landing/css/modules-landing-partials-header.css') }}">
    @endpush
    @endonce


    <div class="navbar-inner">
        <a href="{{ route('landing.home') }}" class="navbar-brand">
            <img src="{{ asset('assets/landing/images/Logo-PKK-Transparent.png') }}" alt="Logo" class="navbar-logo">
            <div class="navbar-divider"></div>
            <div class="navbar-title">
                <span class="navbar-title-main">TP-PKK</span>
                <span class="navbar-title-sub">Kabupaten Toba</span>
                <span class="navbar-title-region">Provinsi Sumatera Utara</span>
            </div>
        </a>

        {{-- Menu Desktop --}}
        <ul class="navbar-links" id="navLinks">
            <li><a href="{{ route('landing.home') }}" class="nav-link" data-page="beranda">Beranda</a></li>
            <li><a href="{{ route('landing.home') }}#struktur" class="nav-link" data-page="struktur">Struktur</a></li>
            <li><a href="{{ route('landing.home') }}#aplikasi" class="nav-link" data-page="aplikasi">Aplikasi</a></li>
            <li><a href="{{ route('landing.home') }}#berita" class="nav-link" data-page="berita">Berita</a></li>
            <li><a href="{{ route('landing.home') }}#desa" class="nav-link" data-page="desa">Desa</a></li>
            <li><a href="{{ route('landing.home') }}#sk" class="nav-link" data-page="sk">SK & Dokumen</a></li>
            <li><a href="{{ route('landing.home') }}#template" class="nav-link" data-page="template">Template</a></li>
            <li><a href="{{ route('landing.home') }}#tentang" class="nav-link" data-page="tentang">Tentang</a></li>
        </ul>

        <button class="hamburger" id="hamburgerBtn">
            <span></span><span></span><span></span>
        </button>
    </div>

    {{-- Menu Mobile --}}
    <div class="mobile-menu" id="mobileMenu">
        <a href="{{ route('landing.home') }}" class="nav-link" data-page="beranda">Beranda</a>
        <a href="{{ route('landing.home') }}#struktur" class="nav-link" data-page="struktur">Struktur</a>
        <a href="{{ route('landing.home') }}#aplikasi" class="nav-link" data-page="aplikasi">Aplikasi</a>
        <a href="{{ route('landing.home') }}#berita" class="nav-link" data-page="berita">Berita</a>
        <a href="{{ route('landing.home') }}#desa" class="nav-link" data-page="desa">Desa</a>
        <a href="{{ route('landing.home') }}#sk" class="nav-link" data-page="sk">SK & Dokumen</a>
        <a href="{{ route('landing.home') }}#template" class="nav-link" data-page="template">Template</a>
        <a href="{{ route('landing.home') }}#tentang" class="nav-link" data-page="tentang">Tentang</a>
    </div>
</nav>

<script src="{{ asset('assets/landing/js/header.js') }}"></script>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
