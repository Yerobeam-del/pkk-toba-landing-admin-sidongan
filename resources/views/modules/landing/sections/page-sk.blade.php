{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="page" id="page-sk">
    <div class="page-header">
        <div class="page-header-content">
            <h1>SK & Dokumen</h1>
            <p>Surat Keputusan dan dokumen resmi PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a data-nav="beranda">Beranda</a>
                <span>/</span>
                <span class="current">SK & Dokumen</span>
            </div>
        </div>
    </div>

    <section class="sk-section">
        <div class="sk-container">
            {{-- Header Section --}}
            <div class="sk-header">
                <div class="sk-header-top">
                    <h2 class="sk-section-title">Daftar Dokumen</h2>

                    {{-- CONTROLS: Search di kiri, Dropdown di kanan --}}
                    <div class="sk-header-controls">
                        {{-- Search Box - DI KIRI --}}
                        <div class="sk-search-box">
                            <svg class="sk-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"/>
                                <path d="m21 21-4.35-4.35"/>
                            </svg>
                            <input type="text" id="searchInput" class="sk-search-input" placeholder="Cari dokumen...">
                        </div>

                        {{-- Per Page Selector - DI KANAN --}}
                        <div class="sk-perpage-selector">
                            <label for="perPageSelect" class="sk-perpage-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                    <path d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                Tampilkan:
                            </label>
                            <select id="perPageSelect" class="sk-perpage-select" >
                                <option value="5" selected>5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                            </select>
                            <span class="sk-perpage-text">dokumen</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loading State --}}
            <div id="loadingState" class="sk-loading">
                <div class="sk-loading-spinner"></div>
                <div class="sk-loading-text">Memuat dokumen...</div>
            </div>

            {{-- Documents Table - Desktop --}}
            <div id="documentsTable" class="sk-table-container">
                <table class="sk-table">
                    <colgroup>
                        <col style="width: 60px">
                        <col style="width: auto">
                        <col style="width: 160px">
                        <col style="width: 130px">
                        <col style="width: 120px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="sk-col-no">No</th>
                            <th class="sk-col-name">
                                <svg class="sk-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                </svg>
                                Nama Dokumen
                            </th>
                            <th class="sk-col-date">
                                <svg class="sk-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                Tanggal
                            </th>
                            <th class="sk-col-size">
                                <svg class="sk-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                Ukuran
                            </th>
                            <th class="sk-col-action">
                                <svg class="sk-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                </svg>
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody id="documentsBody">
                        {{-- Documents will be loaded by loadSKDocuments() in navigation.js --}}
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards View --}}
            <div id="mobileDocumentsList" class="sk-mobile-list"></div>

            {{-- PAGINATION --}}
            <div id="paginationWrapper" class="sk-pagination-wrapper">
                <div class="sk-pagination-container">
                    <button id="prevPageBtn" class="sk-pagination-btn" data-sk-page="prev" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        <span class="desktop-only">Sebelumnya</span>
                    </button>

                    <div id="pageNumbers" class="sk-page-numbers"></div>

                    <button id="nextPageBtn" class="sk-pagination-btn" data-sk-page="next">
                        <span class="desktop-only">Selanjutnya</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>

                <div class="sk-pagination-info" id="paginationInfo"></div>
            </div>

            {{-- Empty State --}}
            <div id="emptyState" class="sk-empty-state u-hidden">
                <div class="sk-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>

                {{-- Title --}}
                <h3 class="sk-empty-title" id="emptyStateTitle">Belum Ada Dokumen</h3>

                {{-- Search Term Display (box terpisah) --}}
                <div id="emptyStateSearchTerm" class="sk-empty-search-term u-hidden"></div>

                {{-- Message --}}
                <p class="sk-empty-text" id="emptyStateText">
                    Dokumen SK dan surat resmi akan segera diunggah.
                    Silakan kunjungi kembali nanti untuk update terbaru.
                </p>

                {{-- Tombol 1: Kembali ke Beranda --}}
                <a id="btnBackToHome" href="#" data-nav="beranda" class="sk-back-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Kembali ke Beranda
                </a>

                {{-- Tombol 2: Tampilkan Semua Dokumen --}}
                <button type="button" id="btnShowAllDocs" data-action="clear-search-sk" class="sk-back-btn sk-btn-secondary u-hidden">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    Tampilkan Semua Dokumen
                </button>
            </div>
        </div>
    </section>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-sk.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
