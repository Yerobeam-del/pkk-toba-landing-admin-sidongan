<!DOCTYPE html>
<html lang="id" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SIDONGAN — Sistem Informasi Dokumen Organisasi Agenda dan Naskah</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-ThemeAware.svg') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Landing page CSS -->
    <link href="{{ asset('assets/sidongan/css/landing-style.css') }}" rel="stylesheet">
</head>

<body>

    <!-- ═══════════════ NAVBAR ═══════════════ -->
    <nav class="landing-nav">
        <div class="nav-container">
            <div class="nav-left">
                <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN.svg') }}" alt="SIDONGAN" class="nav-logo">
                <span class="nav-brand">SIDONGAN</span>
            </div>
            <div class="nav-right">
                <a href="{{ route('sidongan.login') }}" class="btn btn-login-nav">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    LOGIN
                </a>
            </div>
        </div>
    </nav>

    <!-- ═══════════════ HERO ═══════════════ -->
    <section class="hero-section">
        <!-- Decorative elements -->
        <div class="hero-decor decor-1" aria-hidden="true">
            <svg width="140" height="140" viewBox="0 0 140 140" fill="none">
                <path d="M70 10C40 10 15 35 15 65C15 95 40 120 70 120C100 120 125 95 125 65" stroke="rgba(139,92,246,.2)" stroke-width="35" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="hero-decor decor-2" aria-hidden="true">
            <svg width="100" height="100" viewBox="0 0 100 100" fill="none">
                <path d="M50 5C25 5 5 25 5 50C5 75 25 95 50 95C75 95 95 75 95 50" stroke="rgba(139,92,246,.18)" stroke-width="30" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="hero-decor decor-dots" aria-hidden="true">
            <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                <circle cx="10" cy="10" r="3" fill="rgba(139,92,246,.25)"/>
                <circle cx="35" cy="10" r="3" fill="rgba(139,92,246,.25)"/>
                <circle cx="60" cy="10" r="3" fill="rgba(139,92,246,.25)"/>
                <circle cx="85" cy="10" r="3" fill="rgba(139,92,246,.25)"/>
                <circle cx="110" cy="10" r="3" fill="rgba(139,92,246,.25)"/>
                <circle cx="10" cy="35" r="3" fill="rgba(139,92,246,.20)"/>
                <circle cx="35" cy="35" r="3" fill="rgba(139,92,246,.20)"/>
                <circle cx="60" cy="35" r="3" fill="rgba(139,92,246,.20)"/>
                <circle cx="85" cy="35" r="3" fill="rgba(139,92,246,.20)"/>
                <circle cx="110" cy="35" r="3" fill="rgba(139,92,246,.20)"/>
                <circle cx="10" cy="60" r="3" fill="rgba(139,92,246,.15)"/>
                <circle cx="35" cy="60" r="3" fill="rgba(139,92,246,.15)"/>
                <circle cx="60" cy="60" r="3" fill="rgba(139,92,246,.15)"/>
                <circle cx="85" cy="60" r="3" fill="rgba(139,92,246,.15)"/>
                <circle cx="110" cy="60" r="3" fill="rgba(139,92,246,.15)"/>
            </svg>
        </div>
        <div class="hero-decor decor-wave" aria-hidden="true">
            <svg width="250" height="60" viewBox="0 0 250 60" fill="none">
                <path d="M0 30 Q30 0 60 30 T120 30 T180 30 T240 30" stroke="rgba(139,92,246,.18)" stroke-width="3" fill="none"/>
            </svg>
        </div>
        <div class="hero-decor decor-circle" aria-hidden="true">
            <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
                <circle cx="40" cy="40" r="36" stroke="rgba(139,92,246,.18)" stroke-width="2" fill="none"/>
                <circle cx="40" cy="40" r="24" stroke="rgba(139,92,246,.14)" stroke-width="1.5" fill="none"/>
                <circle cx="40" cy="40" r="12" stroke="rgba(139,92,246,.10)" stroke-width="1" fill="none"/>
            </svg>
        </div>

        <!-- Floating Ulos accents -->
        <div class="ulos-float ulos-1" aria-hidden="true"></div>
        <div class="ulos-float ulos-2" aria-hidden="true"></div>

        <div class="hero-container">
            <!-- LEFT: Info + CTA -->
            <div class="hero-left">
                <div class="hero-logos">
                    <img src="{{ asset('assets/admin/images/Logo-Kabupaten-Toba-Transparent.png') }}" alt="Kabupaten Toba" class="hero-logo-small">
                    <img src="{{ asset('assets/admin/images/Logo-PKK-Transparent.png') }}" alt="TP-PKK" class="hero-logo-small">
                </div>
                <h1 class="hero-title">
                    Sistem Informasi<br>
                    <span class="accent">Dokumen Organisasi,<br>Agenda, dan Naskah</span>
                </h1>
                <p class="hero-desc">
                    SIDONGAN adalah aplikasi berbasis digital untuk mengelola surat-menyurat, komunikasi internal, dan arsip dokumen secara elektronik antar pengurus PKK Kabupaten Toba. Aplikasi ini dirancang untuk memperlancar arus informasi, mempercepat proses disposisi surat, dan menciptakan tata kelola administrasi yang efisien, transparan, dan akuntabel.
                </p>

                <ul class="hero-features">
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Surat antar Ketua, Sekretaris, Bendahara & Staf Ahli
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Komunikasi antar Ketua Pengurus
                    </li>
                    <li>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Tracking & Arsip Surat Digital
                    </li>
                </ul>

                <div class="cta-group">
                    <a href="{{ route('sidongan.login') }}" class="btn btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                        <span>Login Sekarang</span>
                    </a>
                </div>
            </div>

            <!-- RIGHT: Ketua & Wakil Photos -->
            <div class="hero-right">
                <div class="leaders-group">
                    <!-- Ketua -->
                    <div class="leader-card">
                        <div class="leader-photo">
                            <img src="{{ asset('assets/admin/images/leader-ketua.png') }}" alt="Ny. Astita Effendi Sintong P. Napitupulu">
                        </div>
                        <div class="leader-label">
                            <span class="leader-name">Ny. Astita Effendi Sintong P. Napitupulu</span>
                            <span class="leader-role">Ketua TP-PKK Kab. Toba</span>
                        </div>
                    </div>

                    <!-- Wakil Ketua -->
                    <div class="leader-card">
                        <div class="leader-photo">
                            <img src="{{ asset('assets/admin/images/leader-wakil.png') }}" alt="Ny. Riama Audi Murphy Sitorus">
                        </div>
                        <div class="leader-label">
                            <span class="leader-name">Ny. Riama Audi Murphy Sitorus</span>
                            <span class="leader-role">Wakil Ketua TP-PKK Kab. Toba</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════ FOOTER ═══════════════ -->
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-inner">
                <div class="footer-left">
                    <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN.svg') }}" alt="SIDONGAN" class="footer-logo" style="height: 28px; filter: brightness(0) invert(1);">
                    <span class="footer-brand">SIDONGAN</span>
                </div>
                <p class="footer-copy">
                    &copy; {{ date('Y') }} Sistem Informasi Dokumen Organisasi Agenda dan Naskah — PKK Kabupaten Toba
                    <span class="footer-dev">Developed by eGov Center IT Del</span>
                </p>
            </div>
        </div>
    </footer>

</body>

</html>
