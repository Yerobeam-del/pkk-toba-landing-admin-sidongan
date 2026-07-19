<div class="page" id="page-template">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Template PKK</h1>
            <p>Template surat dan formulir yang dapat dicetak untuk keperluan PKK</p>
            <div class="breadcrumb">
                <a onclick="navigateTo('beranda')">Beranda</a>
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
                        <select id="templatePerPageSelect" class="template-perpage-select" onchange="changeTemplatePerPage(this.value)">
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

            <div id="templateCardsGrid" class="template-cards-grid" style="display:none"></div>

            <div id="templatePaginationWrapper" class="template-pagination-wrapper hidden">
                <div class="template-pagination-container">
                    <button id="templatePrevBtn" class="template-pagination-btn" onclick="changeTemplatePage('prev')" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        <span class="desktop-only">Previous</span>
                    </button>
                    <div id="templatePageNumbers" class="template-page-numbers"></div>
                    <button id="templateNextBtn" class="template-pagination-btn" onclick="changeTemplatePage('next')">
                        <span class="desktop-only">Next</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </button>
                </div>
                <div class="template-pagination-info" id="templatePaginationInfo"></div>
            </div>

            <div id="templateEmptyState" class="template-empty-state" style="display:none">
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
                <div id="templateEmptySearchTerm" class="template-empty-search-term" style="display:none"></div>
                <p class="template-empty-text" id="templateEmptyText">
                    Template surat dan formulir akan segera diunggah.
                    Silakan kunjungi kembali nanti untuk update terbaru.
                </p>
                <button id="templateBtnShowAll" onclick="clearTemplateSearchAndShowAll()" class="template-back-btn template-btn-secondary" style="display:none">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    Tampilkan Semua Template
                </button>
            </div>
        </div>
    </section>

    <div id="templatePreviewModal" class="template-preview-modal" style="display:none">
        <div class="template-preview-modal-overlay" onclick="closeTemplatePreview()"></div>
        <div class="template-preview-modal-content">
            <div class="template-preview-modal-header">
                <h3 id="templatePreviewTitle">Preview Dokumen</h3>
                <button class="template-preview-modal-close" onclick="closeTemplatePreview()">
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

<script>
// ==========================================
// TEMPLATE PAGE VARIABLES
// ==========================================
let currentTemplatePage = 1;
let templatePagination = null;
let currentTemplatePerPage = 6;
let currentTemplateSearchTerm = '';
let originalTemplateSearchTerm = '';
let templateSearchTimeout;
let currentTemplateOriginalFileName = '';
let currentTemplateFileUrl = '';

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const savedPerPage = localStorage.getItem('template_per_page') || '6';
    currentTemplatePerPage = parseInt(savedPerPage);

    const perPageSelect = document.getElementById('templatePerPageSelect');
    if (perPageSelect) perPageSelect.value = currentTemplatePerPage;

    if (typeof loadTemplateDocuments === 'function') {
        loadTemplateDocuments(1);
    }
});

// ==========================================
// PER PAGE SELECTOR
// ==========================================
function changeTemplatePerPage(value) {
    currentTemplatePerPage = parseInt(value);
    localStorage.setItem('template_per_page', currentTemplatePerPage);
    currentTemplateSearchTerm = '';
    originalTemplateSearchTerm = '';

    const searchInput = document.getElementById('templateSearchInput');
    if (searchInput) searchInput.value = '';

    // AUTO SCROLL KE JUDUL "Daftar Template" DENGAN OFFSET
    const sectionTitle = document.querySelector('.template-section-title');
    if (sectionTitle) {
        const navbarHeight = 80; // Tinggi navbar
        const elementPosition = sectionTitle.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - navbarHeight;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    if (typeof loadTemplateDocuments === 'function') loadTemplateDocuments(1);
}

// ==========================================
// SEARCH FUNCTIONALITY
// ==========================================
const templateSearchInput = document.getElementById('templateSearchInput');
if (templateSearchInput) {
    templateSearchInput.addEventListener('input', function(e) {
        const term = e.target.value;
        originalTemplateSearchTerm = term;

        clearTimeout(templateSearchTimeout);
        templateSearchTimeout = setTimeout(function() {
            currentTemplateSearchTerm = term.toLowerCase().trim();
            if (typeof loadTemplateDocuments === 'function') loadTemplateDocuments(1);
        }, 300);
    });
}

function clearTemplateSearchAndShowAll() {
    currentTemplateSearchTerm = '';
    originalTemplateSearchTerm = '';
    const searchInput = document.getElementById('templateSearchInput');
    if (searchInput) searchInput.value = '';
    if (typeof loadTemplateDocuments === 'function') loadTemplateDocuments(1);
}

// ==========================================
// LOAD TEMPLATE DOCUMENTS
// ==========================================
function loadTemplateDocuments(page) {
    page = page || 1;
    console.log('Load template documents, page:', page);
    currentTemplatePage = page;

    const loadingEl = document.getElementById('templateLoadingState');
    const cardsGrid = document.getElementById('templateCardsGrid');
    const emptyEl = document.getElementById('templateEmptyState');
    const paginationWrapper = document.getElementById('templatePaginationWrapper');

    if (loadingEl) loadingEl.style.display = 'block';
    if (cardsGrid) cardsGrid.style.display = 'none';
    if (emptyEl) emptyEl.style.display = 'none';
    if (paginationWrapper) paginationWrapper.classList.add('hidden');

    let apiUrl = '/api/v1/templates?page=' + page + '&per_page=' + currentTemplatePerPage;
    if (currentTemplateSearchTerm) {
        apiUrl += '&search=' + encodeURIComponent(currentTemplateSearchTerm);
    }

    fetch(apiUrl)
        .then(function(res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function(result) {
            if (!result.success) throw new Error(result.message);

            const templates = result.data || [];
            templatePagination = result.pagination || null;

            if (loadingEl) loadingEl.style.display = 'none';

            if (templates.length === 0) {
                if (cardsGrid) cardsGrid.style.display = 'none';
                if (emptyEl) {
                    emptyEl.style.display = 'block';
                    const titleEl = document.getElementById('templateEmptyTitle');
                    const searchTermEl = document.getElementById('templateEmptySearchTerm');
                    const textEl = document.getElementById('templateEmptyText');
                    const btnShowAll = document.getElementById('templateBtnShowAll');

                    if (titleEl && searchTermEl && textEl && btnShowAll) {
                        const hasSearch = currentTemplateSearchTerm && originalTemplateSearchTerm;
                        if (hasSearch) {
                            titleEl.textContent = 'Template Tidak Ditemukan';
                            searchTermEl.innerHTML = '<div class="template-search-term-label">Pencarian:</div><div class="template-search-term-value">"' + escapeHtml(originalTemplateSearchTerm) + '"</div>';
                            searchTermEl.style.display = 'block';
                            textEl.textContent = 'Silakan coba kata kunci lain atau tampilkan semua template.';
                            btnShowAll.style.display = 'inline-flex';
                        } else {
                            titleEl.textContent = 'Belum Ada Template';
                            searchTermEl.style.display = 'none';
                            textEl.textContent = 'Template surat dan formulir akan segera diunggah. Silakan kunjungi kembali nanti untuk update terbaru.';
                            btnShowAll.style.display = 'none';
                        }
                    }
                }
                if (paginationWrapper) paginationWrapper.classList.add('hidden');
                return;
            }

            // Render cards
            if (cardsGrid) {
                cardsGrid.innerHTML = templates.map(function(template) {
                    const fileUrl = template.file_url || (window.location.origin + '/storage/' + template.file_path);
                    const originalFileName = template.file_name || 'document';

                    let format = 'PDF';
                    if (originalFileName) {
                        const ext = originalFileName.split('.').pop().toUpperCase();
                        if (['PDF', 'DOC', 'DOCX', 'XLS', 'XLSX', 'PPT', 'PPTX', 'TXT'].indexOf(ext) !== -1) {
                            format = ext;
                        }
                    }

                    const iconClass = getFileIconClass(format);
                    const iconColor = getFileIconColor(format);

                    // Simpan data ke data attribute agar aman dari karakter khusus
                    const cardData = {
                        fileUrl: fileUrl,
                        title: template.name,
                        originalFileName: originalFileName,
                        format: format
                    };
                    const encodedData = btoa(unescape(encodeURIComponent(JSON.stringify(cardData))));

                    return '<div class="template-card">' +
                        '<div class="template-card-header" style="background: ' + iconColor + '">' +
                            '<div class="template-card-icon">' +
                                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">' + iconClass + '</svg>' +
                            '</div>' +
                            '<span class="template-card-format">' + format + '</span>' +
                        '</div>' +
                        '<div class="template-card-body">' +
                            '<h3 class="template-card-title" title="' + escapeHtml(template.name) + '">' + escapeHtml(template.name) + '</h3>' +
                            // File name dengan styling yang lebih baik
                            '<div class="template-card-filename" title="' + escapeHtml(originalFileName) + '">' +
                                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">' +
                                    '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>' +
                                    '<polyline points="14 2 14 8 20 8"/>' +
                                '</svg>' +
                                '<span>' + escapeHtml(originalFileName) + '</span>' +
                            '</div>' +
                            '<div class="template-card-meta">' +
                                '<span class="template-card-date">' +
                                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg>' +
                                    formatTanggalIndonesia(template.upload_date) +
                                '</span>' +
                                '<span class="template-card-size">' +
                                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>' +
                                    (template.file_size || '-') +
                                '</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="template-card-actions">' +
                            '<button class="template-card-btn template-card-btn-preview" data-card="' + encodedData + '" onclick="openPreviewFromCard(this)">' +
                                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' +
                                'Preview' +
                            '</button>' +
                            '<a href="' + fileUrl + '" download="' + escapeHtml(originalFileName) + '" class="template-card-btn template-card-btn-download">' +
                                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>' +
                                'Download' +
                            '</a>' +
                        '</div>' +
                    '</div>';
                }).join('');

                cardsGrid.style.display = 'grid';
            }

            // Render pagination
            if (templatePagination && templatePagination.last_page > 1) {
                renderTemplatePagination();
                if (paginationWrapper) paginationWrapper.classList.remove('hidden');
            } else {
                if (paginationWrapper) paginationWrapper.classList.add('hidden');
            }

            console.log('Template cards rendered:', templates.length);
        })
        .catch(function(err) {
            console.error('Error loading templates:', err);
            if (loadingEl) {
                loadingEl.innerHTML = '<div style="text-align:center;padding:2rem">' +
                    '<div style="color:var(--text-dark);font-size:1.1rem;font-weight:600;margin-bottom:0.5rem">Gagal Memuat Template</div>' +
                    '<div style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem">' + err.message + '</div>' +
                    '<button onclick="loadTemplateDocuments(' + page + ')" style="padding:0.75rem 2rem;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;border:none;border-radius:10px;cursor:pointer;font-weight:600">Coba Lagi</button>' +
                '</div>';
            }
        });
}

// ==========================================
// OPEN PREVIEW FROM CARD (MENGAMBIL DATA DARI DATA ATTRIBUTE)
// ==========================================
function openPreviewFromCard(button) {
    const encodedData = button.getAttribute('data-card');
    if (!encodedData) return;

    try {
        const cardData = JSON.parse(decodeURIComponent(escape(atob(encodedData))));
        openTemplatePreview(cardData.fileUrl, cardData.title, cardData.originalFileName, cardData.format);
    } catch (e) {
        console.error('Error parsing card data:', e);
    }
}

// ==========================================
// FILE ICON HELPERS
// ==========================================
function getFileIconClass(format) {
    const f = (format || '').toUpperCase().replace('.', '');
    const icons = {
        'PDF': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'DOC': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'DOCX': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'XLS': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'XLSX': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'PPT': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'PPTX': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'TXT': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'
    };
    return icons[f] || icons['PDF'];
}

function getFileIconColor(format) {
    const f = (format || '').toUpperCase().replace('.', '');
    const colors = {
        'PDF': 'linear-gradient(135deg, #ef4444, #dc2626)',
        'DOC': 'linear-gradient(135deg, #3b82f6, #2563eb)',
        'DOCX': 'linear-gradient(135deg, #3b82f6, #2563eb)',
        'XLS': 'linear-gradient(135deg, #10b981, #059669)',
        'XLSX': 'linear-gradient(135deg, #10b981, #059669)',
        'PPT': 'linear-gradient(135deg, #f59e0b, #d97706)',
        'PPTX': 'linear-gradient(135deg, #f59e0b, #d97706)',
        'TXT': 'linear-gradient(135deg, #6b7280, #4b5563)'
    };
    return colors[f] || colors['PDF'];
}

// ==========================================
// PREVIEW MODAL
// ==========================================
function openTemplatePreview(fileUrl, title, originalFileName, format) {
    const modal = document.getElementById('templatePreviewModal');
    const titleEl = document.getElementById('templatePreviewTitle');
    const bodyEl = document.getElementById('templatePreviewBody');
    const downloadBtn = document.getElementById('templatePreviewDownloadBtn');
    const openBtn = document.getElementById('templatePreviewOpenBtn');

    if (!modal || !titleEl || !bodyEl) return;

    // Simpan data global
    currentTemplateFileUrl = fileUrl;
    currentTemplateOriginalFileName = originalFileName || title;

    // Bersihkan nama file dari ekstensi agar rapi di judul modal
    const cleanTitle = (originalFileName || title).replace(/\.[^/.]+$/, "");
    titleEl.textContent = cleanTitle;

    // Set download button dengan nama file ASLI
    downloadBtn.href = fileUrl;
    downloadBtn.setAttribute('download', currentTemplateOriginalFileName);

    if (openBtn) openBtn.href = fileUrl;

    let normalizedFormat = (format || '').toUpperCase().replace('.', '');
    if (!normalizedFormat || normalizedFormat === 'UNKNOWN') {
        normalizedFormat = (originalFileName || title).split('.').pop().toUpperCase();
    }

    console.log('Preview opened:', {
        title: title,
        originalFileName: originalFileName,
        downloadAs: currentTemplateOriginalFileName,
        format: normalizedFormat
    });

    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    if (normalizedFormat === 'PDF') {
        showPDFPreview(fileUrl);
    } else if (['DOC', 'DOCX', 'XLS', 'XLSX', 'PPT', 'PPTX'].indexOf(normalizedFormat) !== -1) {
        showOfficePreview(fileUrl, title, normalizedFormat);
    } else if (normalizedFormat === 'TXT') {
        showTextPreview(fileUrl);
    } else if (['JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'SVG'].indexOf(normalizedFormat) !== -1) {
        showImagePreview(fileUrl, title);
    } else {
        showUnsupportedFormat(title, fileUrl, normalizedFormat);
    }
}

// ==========================================
// OFFICE FILES PREVIEW (UPDATED)
// ==========================================
function showOfficePreview(fileUrl, title, format) {
    const bodyEl = document.getElementById('templatePreviewBody');
    const originalName = currentTemplateOriginalFileName;
    const modalBody = document.querySelector('.template-preview-modal-body');

    // Pastikan URL adalah URL absolut dan menggunakan HTTPS
    let publicUrl = fileUrl;
    if (publicUrl.startsWith('http://')) {
        publicUrl = publicUrl.replace('http://', 'https://');
    } else if (publicUrl.startsWith('/')) {
        publicUrl = window.location.origin + publicUrl;
    }

    // Encode URL untuk Microsoft Office Viewer
    const encodedUrl = encodeURIComponent(publicUrl);
    const officeViewerUrl = `https://view.officeapps.live.com/op/embed.aspx?src=${encodedUrl}&wdEmbed=0`;

    // PENTING: Disable scroll pada modal body
    if (modalBody) {
        modalBody.style.overflow = 'hidden';
        modalBody.style.padding = '0';
    }

    // Tampilkan loading
    bodyEl.innerHTML = '<div style="padding:3rem;text-align:center;">' +
        '<div style="width:50px;height:50px;border:4px solid rgba(15,107,99,0.1);border-top-color:var(--primary);border-radius:50%;margin:0 auto 1rem;animation:spin 0.8s linear infinite;"></div>' +
        '<p style="color:var(--text-muted);">Memuat preview dokumen...</p>' +
    '</div>';

    // Update footer dengan tombol Print dan Download
    updateTemplatePreviewFooter(publicUrl, originalName, officeViewerUrl);

    // Test apakah URL bisa diakses
    fetch(publicUrl, { method: 'HEAD' })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('File tidak dapat diakses (HTTP ' + response.status + ')');
            }

            // Jika berhasil, tampilkan iframe yang mengisi penuh container
            setTimeout(function() {
                // Container dengan overflow hidden untuk mencegah double scroll
                bodyEl.innerHTML = '<div style="width:100%;height:100%;position:relative;overflow:hidden;">' +
                    '<iframe id="officePreviewIframe" src="' + officeViewerUrl + '" width="100%" height="100%" style="border:none;display:block;" frameborder="0" onload="this.style.opacity=1">' +
                    '</iframe>' +
                '</div>';
            }, 1000);
        })
        .catch(function(error) {
            console.error('Error accessing file:', error);
            showOfficePreviewError(title, publicUrl, format, error.message);
        });
}

function updateTemplatePreviewFooter(fileUrl, originalName, officeViewerUrl) {
    const footerEl = document.querySelector('.template-preview-modal-footer');
    if (!footerEl) return;

    // Escape single quotes agar tidak error di onclick
    const safeFileUrl = fileUrl.replace(/'/g, "\\'");
    const safeOriginalName = originalName.replace(/'/g, "\\'");

    footerEl.innerHTML =
        '<button onclick="printOfficeDocument(\'' + safeFileUrl + '\', \'' + safeOriginalName + '\')" class="template-preview-print-btn" style="display:inline-flex;align-items:center;gap:0.75rem;padding:0.875rem 1.5rem;background:#f1f5f9;color:var(--text-dark);border:1px solid #e2e8f0;border-radius:10px;font-weight:600;cursor:pointer;transition:all 0.3s;" onmouseover="this.style.background=\'#e2e8f0\';this.style.borderColor=\'#cbd5e1\'" onmouseout="this.style.background=\'#f1f5f9\';this.style.borderColor=\'#e2e8f0\'">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">' +
                '<polyline points="6 9 6 2 18 2 18 9"/>' +
                '<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>' +
                '<rect x="6" y="14" width="12" height="8"/>' +
            '</svg>' +
            'Print' +
        '</button>' +
        '<a href="' + fileUrl + '" download="' + originalName + '" class="template-preview-download-btn" style="display:inline-flex;align-items:center;gap:0.75rem;padding:0.875rem 1.5rem;background:linear-gradient(135deg, var(--primary), var(--primary-light));color:#fff;border-radius:10px;font-weight:600;text-decoration:none;transition:all 0.3s;box-shadow:0 4px 12px rgba(15,107,99,0.3);">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">' +
                '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>' +
                '<polyline points="7 10 12 15 17 10"/>' +
                '<line x1="12" y1="15" x2="12" y2="3"/>' +
            '</svg>' +
            'Download' +
        '</a>' +
        '<div style="width:100%;text-align:center;margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid #e2e8f0;">' +
            '<p style="margin:0;color:var(--text-muted);font-size:0.8rem;">' +
                'Jika preview tidak muncul, <a href="' + officeViewerUrl + '" target="_blank" style="color:var(--primary);font-weight:600;text-decoration:underline;">klik di sini</a> untuk membuka di Microsoft Office Online.' +
            '</p>' +
        '</div>';
}

// Fungsi untuk print dokumen Office
function printOfficeDocument(fileUrl, originalName) {
    // 1. Trigger download file secara otomatis
    const link = document.createElement('a');
    link.href = fileUrl;
    link.download = originalName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    // 2. Berikan instruksi yang jelas kepada pengguna
    setTimeout(function() {
        alert('File "' + originalName + '" sedang diunduh.\n\nSetelah unduhan selesai, silakan buka file tersebut di komputer Anda dan cetak menggunakan tombol:\n• Windows: Ctrl + P\n• Mac: Cmd + P');
    }, 500);
}

function showOfficePreviewError(title, fileUrl, format, errorMsg) {
    const bodyEl = document.getElementById('templatePreviewBody');
    const originalName = currentTemplateOriginalFileName;

    bodyEl.innerHTML = '<div style="padding:3rem 2rem;text-align:center;">' +
        '<div style="width:80px;height:80px;margin:0 auto 1.5rem;background:linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.1));border-radius:50%;display:flex;align-items:center;justify-content:center;">' +
            '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">' +
                '<circle cx="12" cy="12" r="10"/>' +
                '<line x1="15" y1="9" x2="9" y2="15"/>' +
                '<line x1="9" y1="9" x2="15" y2="15"/>' +
            '</svg>' +
        '</div>' +
        '<h4 style="margin:0 0 0.5rem 0;color:var(--text-dark);">Preview Tidak Tersedia</h4>' +
        '<p style="margin:0 0 1rem 0;color:var(--text-muted);">File <strong>' + escapeHtml(title) + '</strong> tidak dapat ditampilkan.</p>' +
        '<div style="background:#fef2f2;border:2px solid #fecaca;border-radius:12px;padding:1.5rem;margin:2rem 0;text-align:left;max-width:600px;margin-left:auto;margin-right:auto;">' +
            '<p style="margin:0 0 1rem 0;font-size:0.95rem;color:#991b1b;font-weight:600;">Kemungkinan penyebab:</p>' +
            '<ul style="margin:0 0 1rem 0;padding-left:1.5rem;font-size:0.9rem;color:#991b1b;line-height:1.8;">' +
                '<li>File tidak dapat diakses dari internet (perlu URL publik)</li>' +
                '<li>Server memblokir akses dari Microsoft Viewer</li>' +
                '<li>Ukuran file terlalu besar</li>' +
                '<li>Format file tidak didukung</li>' +
            '</ul>' +
            '<p style="margin:0;font-size:0.85rem;color:#991b1b;">' +
                '<strong>Solusi:</strong> Download file untuk melihat isinya.' +
            '</p>' +
        '</div>' +
        '<div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-top:2rem;">' +
            '<a href="' + fileUrl + '" download="' + escapeHtml(originalName) + '" style="display:inline-flex;align-items:center;gap:0.75rem;padding:1rem 2rem;background:linear-gradient(135deg, var(--primary), var(--primary-light));color:#fff;border-radius:12px;font-weight:600;text-decoration:none;font-size:1rem;box-shadow:0 4px 12px rgba(15,107,99,0.3);transition:all 0.3s;">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">' +
                    '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>' +
                    '<polyline points="7 10 12 15 17 10"/>' +
                    '<line x1="12" y1="15" x2="12" y2="3"/>' +
                '</svg>' +
                'Download File ' + format +
            '</a>' +
            '<a href="' + fileUrl + '" target="_blank" style="display:inline-flex;align-items:center;gap:0.75rem;padding:1rem 2rem;background:#f1f5f9;color:var(--text-dark);border-radius:12px;font-weight:600;text-decoration:none;font-size:1rem;transition:all 0.3s;">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">' +
                    '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>' +
                    '<polyline points="15 3 21 3 21 9"/>' +
                    '<line x1="10" y1="14" x2="21" y2="3"/>' +
                '</svg>' +
                'Buka di Tab Baru' +
            '</a>' +
        '</div>' +
    '</div>';

    // Update footer untuk error state juga
    updateTemplatePreviewFooter(fileUrl, originalName, fileUrl);
}

// ==========================================
// PDF PREVIEW
// ==========================================
function showPDFPreview(fileUrl) {
    const bodyEl = document.getElementById('templatePreviewBody');
    bodyEl.innerHTML = '<div style="width:100%;height:650px;">' +
        '<embed src="' + fileUrl + '" type="application/pdf" width="100%" height="100%" style="border:none;">' +
    '</div>' +
    '<div style="padding:1rem;text-align:center;background:#f8fafc;border-top:1px solid #e2e8f0;">' +
        '<p style="margin:0;color:var(--text-muted);font-size:0.85rem;">' +
            'Jika preview tidak muncul, <a href="' + fileUrl + '" target="_blank" style="color:var(--primary);font-weight:600;">klik di sini</a> untuk membuka PDF di tab baru.' +
        '</p>' +
    '</div>';
}

// ==========================================
// TEXT PREVIEW
// ==========================================
function showTextPreview(fileUrl) {
    const bodyEl = document.getElementById('templatePreviewBody');
    const originalName = currentTemplateOriginalFileName;

    bodyEl.innerHTML = '<div style="padding:2rem;text-align:center;">' +
        '<div style="width:40px;height:40px;border:3px solid rgba(15,107,99,0.1);border-top-color:var(--primary);border-radius:50%;margin:0 auto 1rem;animation:spin 0.8s linear infinite;"></div>' +
        '<p style="color:var(--text-muted);">Memuat preview...</p>' +
    '</div>';

    fetch(fileUrl)
        .then(function(res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.text();
        })
        .then(function(text) {
            bodyEl.innerHTML = '<div style="padding:2rem;max-height:650px;overflow:auto;">' +
                '<pre style="white-space:pre-wrap;font-family:\'Courier New\',monospace;font-size:0.9rem;line-height:1.6;margin:0;padding:1.5rem;background:#f8fafc;border-radius:8px;border:1px solid #e2e8f0;">' + escapeHtml(text) + '</pre>' +
            '</div>';
        })
        .catch(function() {
            bodyEl.innerHTML = '<div style="padding:3rem;text-align:center;color:var(--text-muted);">' +
                '<p>Tidak dapat memuat preview. Silakan download file.</p>' +
                '<a href="' + fileUrl + '" download="' + escapeHtml(originalName) + '" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;background:linear-gradient(135deg, var(--primary), var(--primary-light));color:#fff;border-radius:10px;font-weight:600;text-decoration:none;margin-top:1rem;">Download File</a>' +
            '</div>';
        });
}

// ==========================================
// IMAGE PREVIEW
// ==========================================
function showImagePreview(fileUrl, title) {
    const bodyEl = document.getElementById('templatePreviewBody');
    bodyEl.innerHTML = '<div style="padding:2rem;text-align:center;background:#f8fafc;max-height:650px;overflow:auto;">' +
        '<img src="' + fileUrl + '" alt="' + escapeHtml(title) + '" style="max-width:100%;max-height:600px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);">' +
    '</div>';
}

// ==========================================
// UNSUPPORTED FORMAT
// ==========================================
function showUnsupportedFormat(title, fileUrl, format) {
    const bodyEl = document.getElementById('templatePreviewBody');
    const originalName = currentTemplateOriginalFileName;

    bodyEl.innerHTML = '<div style="padding:3rem 2rem;text-align:center;">' +
        '<div style="width:80px;height:80px;margin:0 auto 1.5rem;background:linear-gradient(135deg, rgba(107,114,128,0.1), rgba(75,85,99,0.1));border-radius:50%;display:flex;align-items:center;justify-content:center;">' +
            '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.5">' +
                '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>' +
                '<polyline points="14 2 14 8 20 8"/>' +
            '</svg>' +
        '</div>' +
        '<h4 style="margin:0 0 0.5rem 0;color:var(--text-dark);">Preview Tidak Tersedia</h4>' +
        '<p style="margin:0 0 1.5rem 0;color:var(--text-muted);">Format <strong>' + format + '</strong> tidak dapat di-preview.</p>' +
        '<a href="' + fileUrl + '" download="' + escapeHtml(originalName) + '" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;background:linear-gradient(135deg, var(--primary), var(--primary-light));color:#fff;border-radius:10px;font-weight:600;text-decoration:none;">' +
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">' +
                '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>' +
                '<polyline points="7 10 12 15 17 10"/>' +
                '<line x1="12" y1="15" x2="12" y2="3"/>' +
            '</svg>' +
            'Download File' +
        '</a>' +
    '</div>';
}

// ==========================================
// HELPERS
// ==========================================
function formatTanggalIndonesia(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        // Cek apakah tanggal valid
        if (isNaN(date.getTime())) return dateString;

        // Format ke bahasa Indonesia dengan nama bulan panjang (long)
        return date.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long', // 'long' = Januari, Februari, dst.
            year: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function closeTemplatePreview() {
    const modal = document.getElementById('templatePreviewModal');
    const bodyEl = document.getElementById('templatePreviewBody');
    const modalBody = document.querySelector('.template-preview-modal-body');

    if (modal) modal.style.display = 'none';
    if (bodyEl) bodyEl.innerHTML = '';

    // Reset overflow modal body ke default
    if (modalBody) {
        modalBody.style.overflow = '';
        modalBody.style.padding = '';
    }

    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeTemplatePreview();
});

// ==========================================
// PAGINATION
// ==========================================
function renderTemplatePagination() {
    if (!templatePagination) return;

    const pageNumbersEl = document.getElementById('templatePageNumbers');
    const prevBtn = document.getElementById('templatePrevBtn');
    const nextBtn = document.getElementById('templateNextBtn');
    const infoEl = document.getElementById('templatePaginationInfo');
    const paginationWrapper = document.getElementById('templatePaginationWrapper');

    if (!pageNumbersEl || !prevBtn || !nextBtn) return;

    const current_page = templatePagination.current_page;
    const last_page = templatePagination.last_page;
    const from = templatePagination.from;
    const to = templatePagination.to;
    const total = templatePagination.total;

    if (last_page > 1) {
        paginationWrapper.classList.remove('hidden');
    } else {
        paginationWrapper.classList.add('hidden');
        return;
    }

    prevBtn.disabled = current_page === 1;
    nextBtn.disabled = current_page === last_page || last_page === 0;

    pageNumbersEl.innerHTML = '';

    let pages = [];
    if (last_page <= 7) {
        for (let i = 1; i <= last_page; i++) pages.push(i);
    } else {
        if (current_page <= 3) {
            pages = [1, 2, 3, 4, '...', last_page];
        } else if (current_page >= last_page - 2) {
            pages = [1, '...', last_page - 3, last_page - 2, last_page - 1, last_page];
        } else {
            pages = [1, '...', current_page - 1, current_page, current_page + 1, '...', last_page];
        }
    }

    pages.forEach(function(page) {
        if (page === '...') {
            const dots = document.createElement('span');
            dots.className = 'template-pagination-dots';
            dots.textContent = '...';
            pageNumbersEl.appendChild(dots);
        } else {
            const btn = document.createElement('button');
            btn.className = 'template-pagination-btn';
            btn.textContent = page;
            if (page === current_page) btn.classList.add('active');
            btn.onclick = function() { changeTemplatePage(page); };
            pageNumbersEl.appendChild(btn);
        }
    });

    if (infoEl) {
        infoEl.innerHTML = 'Menampilkan <strong>' + (from || 0) + '</strong> - <strong>' + (to || 0) + '</strong> dari <strong>' + (total || 0) + '</strong> template';
    }
}

function changeTemplatePage(page) {
    if (page === 'prev') page = currentTemplatePage - 1;
    else if (page === 'next') page = currentTemplatePage + 1;

    if (!templatePagination || page < 1 || page > templatePagination.last_page) return;

    // AUTO SCROLL KE JUDUL "Daftar Template" DENGAN OFFSET
    const sectionTitle = document.querySelector('.template-section-title');
    if (sectionTitle) {
        const navbarHeight = 165; // Tinggi navbar
        const elementPosition = sectionTitle.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - navbarHeight;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    loadTemplateDocuments(page);
}
</script>
