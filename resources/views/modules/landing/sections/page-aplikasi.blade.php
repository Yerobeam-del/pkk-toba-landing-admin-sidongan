{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="page active" id="page-aplikasi">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Aplikasi & Layanan</h1>
            <p>Sistem informasi digital PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a data-nav="beranda">Beranda</a>
                <span>/</span>
                <span class="current">Aplikasi</span>
            </div>
        </div>
    </div>

    <section class="apps-landing-section">
        <div class="apps-container">

            {{-- SEARCH BAR --}}
            <div class="search-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="globalSearch" class="search-input" placeholder="Cari aplikasi atau layanan (nama panjang/pendek)...">
            </div>

            {{-- SECTION: APLIKASI --}}
            <div class="category-block" id="block-aplikasi">
                <div class="section-head">
                    <span class="badge-app">APLIKASI</span>
                    <h2 class="section-title">Sistem yang Tersedia</h2>
                    <p class="section-desc">Akses berbagai aplikasi digital untuk mendukung kinerja PKK, termasuk yang sedang diperbaiki dan yang akan segera hadir</p>
                </div>

                <div class="carousel-wrapper">
                    <button class="nav-btn prev-btn" data-carousel="track-aplikasi" data-dir="-1">&#10094;</button>
                    <div class="carousel-track" id="track-aplikasi">
                        <div class="loading-state">Memuat data aplikasi...</div>
                    </div>
                    <button class="nav-btn next-btn" data-carousel="track-aplikasi" data-dir="1">&#10095;</button>
                </div>

                <div class="see-all-wrapper">
                    <button class="btn-see-all-apps" data-open-modal="modal-aplikasi">
                        Lihat Semua Aplikasi
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            <div class="section-divider"></div>

            {{-- SECTION: LAYANAN --}}
            <div class="category-block" id="block-layanan">
                <div class="section-head">
                    <span class="badge-lay">LAYANAN DIGITAL</span>
                    <h2 class="section-title">Layanan Publik</h2>
                    <p class="section-desc">Layanan mandiri yang dapat diakses oleh masyarakat dan pengurus, termasuk yang sedang diperbaiki dan yang akan segera hadir</p>
                </div>

                <div class="carousel-wrapper">
                    <button class="nav-btn prev-btn" data-carousel="track-layanan" data-dir="-1">&#10094;</button>
                    <div class="carousel-track" id="track-layanan">
                        <div class="loading-state">Memuat data layanan...</div>
                    </div>
                    <button class="nav-btn next-btn" data-carousel="track-layanan" data-dir="1">&#10095;</button>
                </div>

                <div class="see-all-wrapper">
                    <button class="btn-see-all-apps btn-layanan" data-open-modal="modal-layanan">
                        Lihat Semua Layanan
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </div>
    </section>

    {{-- MODAL: SEMUA APLIKASI --}}
    <div id="modal-aplikasi" class="modal-overlay" data-close-outside="modal-aplikasi">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Semua Aplikasi</h3>
                <button class="modal-close" data-close-modal="modal-aplikasi">&times;</button>
            </div>
            <div class="modal-grid" id="grid-aplikasi"></div>
        </div>
    </div>

    {{-- MODAL: SEMUA LAYANAN --}}
    <div id="modal-layanan" class="modal-overlay" data-close-outside="modal-layanan">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Semua Layanan</h3>
                <button class="modal-close" data-close-modal="modal-layanan">&times;</button>
            </div>
            <div class="modal-grid" id="grid-layanan"></div>
        </div>
    </div>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-aplikasi.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
