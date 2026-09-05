{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="page" id="page-desa">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Data Desa</h1>
            <p>Daftar desa dan kelurahan di Kabupaten Toba, Sumatera Utara</p>
            <div class="breadcrumb">
                <a data-nav="beranda">Beranda</a>
                <span>/</span>
                <span class="current">Desa</span>
            </div>
        </div>
    </div>

    <section class="desa-section">
        <div class="desa-container">
            {{-- Header dengan Controls --}}
            <div class="desa-header">
                <h2 class="desa-section-title">Daftar Desa &amp; Kelurahan</h2>

                {{-- Controls: Search di kiri, Per Page di kanan --}}
                <div class="desa-header-controls">
                    {{-- Search Box - DI KIRI --}}
                    <div class="desa-search-box">
                        <svg class="desa-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" id="desaSearch" class="desa-search-input" placeholder="Cari nama desa atau kecamatan..." autocomplete="off" aria-label="Cari desa">
                    </div>

                    {{-- Per Page Selector - DI KANAN --}}
                    <div class="desa-perpage-selector">
                        <label for="desaPerPageSelect" class="desa-perpage-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Tampilkan:
                        </label>
                        <select id="desaPerPageSelect" class="desa-perpage-select">
                            <option value="6" selected>6</option>
                            <option value="12">12</option>
                            <option value="24">24</option>
                            <option value="48">48</option>
                        </select>
                        <span class="desa-perpage-text">desa</span>
                    </div>
                </div>

                {{-- Filter Kecamatan (diisi otomatis oleh JS) --}}
                <div class="desa-filter u-hidden" id="desaFilter"></div>
            </div>

            {{-- Loading State --}}
            <div id="desa-loading" class="desa-loading">
                <div class="desa-loading-spinner"></div>
                <div class="desa-loading-text">Memuat data desa...</div>
            </div>

            {{-- Grid Container (Kartu desa akan muncul di sini) --}}
            <div class="desa-grid u-hidden" id="desaGrid"></div>

            {{-- PAGINATION --}}
            <div id="desaPaginationWrapper" class="desa-pagination-wrapper u-hidden">
                <div class="desa-pagination-container">
                    <button id="desaPrevBtn" class="desa-pagination-btn" data-desa-page="prev" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        <span class="desktop-only">Sebelumnya</span>
                    </button>

                    <div id="desaPageNumbers" class="desa-page-numbers"></div>

                    <button id="desaNextBtn" class="desa-pagination-btn" data-desa-page="next">
                        <span class="desktop-only">Selanjutnya</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>

                <div class="desa-pagination-info" id="desaPaginationInfo"></div>
            </div>

            {{-- Empty State --}}
            <div id="desa-empty-state" class="desa-empty-state u-hidden">
                <div class="desa-empty-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/>
                        <rect x="9" y="9" width="6" height="6"/>
                    </svg>
                </div>
                <h3 class="desa-empty-title">Belum Ada Data Desa</h3>
                <p class="desa-empty-text">Data desa dan kelurahan di Kabupaten Toba sedang dalam proses pengumpulan. Silakan kunjungi kembali nanti untuk informasi terbaru.</p>
                <a data-nav="beranda" class="desa-back-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </section>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-desa.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
