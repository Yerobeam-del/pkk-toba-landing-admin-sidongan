{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="page" id="page-berita">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Berita & Kegiatan</h1>
            <p>Informasi terbaru seputar kegiatan dan program PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a data-nav="beranda">Beranda</a>
                <span>/</span>
                <span class="current">Berita</span>
            </div>
        </div>
    </div>

    <section class="news-full-section">
        <div class="news-container">
            {{-- Header dengan Controls --}}
            <div class="news-header">
                <h2 class="news-section-title">Daftar Berita</h2>
                <div class="news-controls">
                {{-- Sort Selector --}}
                <div class="news-sort-selector">
                    <label for="newsSortSelect" class="news-sort-label">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <path d="M3 6h18M6 12h12M9 18h6"/>
                        </svg>
                        Urutkan:
                    </label>
                    <select id="newsSortSelect" class="news-sort-select"  title="Pilih cara pengurutan berita">
                        <option value="latest" selected>Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="title_asc">Judul A-Z</option>
                        <option value="title_desc">Judul Z-A</option>
                    </select>
                </div>

                {{-- Tambahkan info text di bawah selector --}}
                <div class="news-sort-info">
                    <p class="sort-description" id="sortDescription">
                        Menampilkan berita dari yang paling baru dipublikasikan
                    </p>
                </div>

                    {{-- Per Page Selector --}}
                    <div class="news-perpage-selector">
                        <label for="newsPerPageSelect" class="news-perpage-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Tampilkan:
                        </label>
                        <select id="newsPerPageSelect" class="news-perpage-select" >
                            <option value="6" selected>6</option>
                            <option value="12">12</option>
                            <option value="24">24</option>
                        </select>
                        <span class="news-perpage-text">berita</span>
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <div id="news-loading" class="news-loading">
                <div class="news-loading-text">Memuat berita terbaru...</div>
            </div>

            {{-- News Grid --}}
            <div class="news-full-grid" id="newsFullGrid"></div>

            {{-- Pagination --}}
            <div id="newsPaginationWrapper" class="news-pagination-wrapper hidden">
                <div class="news-pagination-container">
                    <button id="newsPrevBtn" class="news-pagination-btn" data-news-page="prev" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        <span class="desktop-only">Sebelumnya</span>
                    </button>
                    <div id="newsPageNumbers" class="news-page-numbers"></div>
                    <button id="newsNextBtn" class="news-pagination-btn" data-news-page="next">
                        <span class="desktop-only">Selanjutnya</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                <div class="news-pagination-info" id="newsPaginationInfo"></div>
            </div>

            {{-- Empty State --}}
            <div id="news-empty-state" class="news-empty-state">
                <div class="news-empty-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                        <path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>
                    </svg>
                </div>
                <h3 class="news-empty-title">Belum Ada Berita Terbaru</h3>
                <p class="news-empty-text">Tim kami sedang mempersiapkan informasi terkini seputar kegiatan dan program PKK Kabupaten Toba. Silakan kunjungi kembali nanti untuk update terbaru.</p>
                <a data-nav="beranda" class="news-back-btn">
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
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-berita.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
