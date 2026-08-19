{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="page" id="page-template">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Template PKK</h1>
            <p>Template surat dan formulir yang dapat dicetak untuk keperluan PKK</p>
            <div class="breadcrumb">
                <a data-nav="beranda">Beranda</a>
                <span>/</span>
                <span class="current">Template</span>
            </div>
        </div>
    </div>

    <section class="template-section">
        <div class="template-container">
            <div class="template-header">
                <h2 class="template-section-title">Daftar Template</h2>
                <div class="template-controls">
                    <div class="template-search-box">
                        <svg class="template-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input type="text" id="templateSearchInput" class="template-search-input" placeholder="Cari template...">
                    </div>
                    <div class="template-perpage-selector">
                        <label for="templatePerPageSelect" class="template-perpage-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                                <path d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Tampilkan:
                        </label>
                        <select id="templatePerPageSelect" class="template-perpage-select" >
                            <option value="6" selected>6</option>
                            <option value="12">12</option>
                            <option value="24">24</option>
                        </select>
                        <span class="template-perpage-text">template</span>
                    </div>
                </div>
            </div>

            <div id="templateLoadingState" class="template-loading">
                <div class="template-loading-spinner"></div>
                <div class="template-loading-text">Memuat template...</div>
            </div>

            <div id="templateCardsGrid" class="template-cards-grid u-hidden"></div>

            <div id="templatePaginationWrapper" class="template-pagination-wrapper hidden">
                <div class="template-pagination-container">
                    <button id="templatePrevBtn" class="template-pagination-btn" data-template-page="prev" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        <span class="desktop-only">Sebelumnya</span>
                    </button>
                    <div id="templatePageNumbers" class="template-page-numbers"></div>
                    <button id="templateNextBtn" class="template-pagination-btn" data-template-page="next">
                        <span class="desktop-only">Selanjutnya</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                <div class="template-pagination-info" id="templatePaginationInfo"></div>
            </div>

            <div id="templateEmptyState" class="template-empty-state u-hidden">
                <div class="template-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <h3 class="template-empty-title" id="templateEmptyTitle">Belum Ada Template</h3>
                <div id="templateEmptySearchTerm" class="template-empty-search-term u-hidden"></div>
                <p class="template-empty-text" id="templateEmptyText">
                    Template surat dan formulir akan segera diunggah.
                    Silakan kunjungi kembali nanti untuk update terbaru.
                </p>
                <button type="button" id="templateBtnShowAll" data-action="clear-search-template" class="template-back-btn template-btn-secondary u-hidden">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    Tampilkan Semua Template
                </button>
            </div>
        </div>
    </section>

    <div id="templatePreviewModal" class="template-preview-modal u-hidden">
        <div class="template-preview-modal-overlay" data-close-template-preview="1"></div>
        <div class="template-preview-modal-content">
            <div class="template-preview-modal-header">
                <h3 id="templatePreviewTitle">Preview Dokumen</h3>
                <button type="button" class="template-preview-modal-close" data-close-template-preview="1">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <div class="template-preview-modal-body" id="templatePreviewBody"></div>
            <div class="template-preview-modal-footer">
                <a id="templatePreviewOpenBtn" href="#" target="_blank" class="template-preview-open-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Buka di Tab Baru
                </a>
                <a id="templatePreviewDownloadBtn" href="#" download class="template-preview-download-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Download
                </a>
            </div>
        </div>
    </div>
</div>

    @once
    @push('scripts')
    <script src="{{ asset('assets/landing/js/modules-landing-sections-page-template.js') }}"></script>
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
