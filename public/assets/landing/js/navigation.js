/**
 * Navigation & SPA Router
 */

// Global variables for SK pagination
let currentSKPage = 1;
let skPagination = null;
let currentPerPage = 5;
let currentSearchTerm = '';
let originalSearchTerm = ''; // ← TAMBAHKAN INI - preserve case untuk display

function navigateTo(pageId) {
    console.log('🔄 navigateTo:', pageId);
    
    document.querySelectorAll('.page').forEach(p => {
        p.classList.remove('active');
        p.style.display = 'none';
    });
    
    const targetPage = document.getElementById('page-' + pageId);
    if (targetPage) {
        targetPage.classList.add('active');
        targetPage.style.display = 'block';
        void targetPage.offsetHeight;
        console.log('✅ Page shown:', pageId);
    } else {
        console.error('❌ Page not found:', pageId);
        return;
    }

    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active-link');
        if (link.getAttribute('data-page') === pageId) {
            link.classList.add('active-link');
        }
    });
    
    const navLinks = document.getElementById('navLinks');
    const hamburger = document.getElementById('hamburger');
    if (navLinks) navLinks.classList.remove('active');
    if (hamburger) hamburger.classList.remove('active');
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    const loadPageData = () => {
        if (pageId === 'struktur' && typeof loadStrukturData === 'function') loadStrukturData();
        if (pageId === 'aplikasi' && typeof loadAplikasiData === 'function') loadAplikasiData();
        if (pageId === 'berita' && typeof populateNewsFull === 'function') populateNewsFull();
        if (pageId === 'desa' && typeof populateDesa === 'function') populateDesa();
        if (pageId === 'sk' && typeof loadSKDocuments === 'function') {
            // Reset search saat pindah halaman
            currentSearchTerm = '';
            originalSearchTerm = ''; // ← Reset juga
            const searchInput = document.getElementById('searchInput');
            if (searchInput) searchInput.value = '';
            loadSKDocuments(1);
        }
        if (pageId === 'template' && typeof populateTemplates === 'function') populateTemplates();
        if (pageId === 'tentang' && typeof loadTentangKami === 'function') loadTentangKami();
    };

    const baseUrl = window.location.href.split('#')[0];
    if (pageId === 'beranda') {
        history.replaceState(null, null, baseUrl);
    } else {
        history.replaceState(null, null, baseUrl + '#' + pageId);
    }
    
    console.log('🔗 URL updated to:', window.location.href);

    requestAnimationFrame(() => {
        requestAnimationFrame(loadPageData);
    });
    
    closeFloatingMenu();
}

function toggleMenu() {
    const navLinks = document.getElementById('navLinks');
    const hamburger = document.getElementById('hamburger');
    if (navLinks) navLinks.classList.toggle('active');
    if (hamburger) hamburger.classList.toggle('active');
}

function closeFloatingMenu() {
    const menu = document.getElementById('floatingMenu');
    const trigger = document.getElementById('floatingTrigger');
    if (menu) menu.classList.remove('open');
    if (trigger) trigger.classList.remove('open');
}

function toggleFloatingMenu() {
    const menu = document.getElementById('floatingMenu');
    const trigger = document.getElementById('floatingTrigger');
    if (menu) menu.classList.toggle('open');
    if (trigger) trigger.classList.toggle('open');
}

document.addEventListener('click', function(e) {
    const floatingBtn = document.getElementById('floatingAppBtn');
    if (floatingBtn && !floatingBtn.contains(e.target)) {
        closeFloatingMenu();
    }
});

// ==========================================
// CLEAR SEARCH AND SHOW ALL DOCUMENTS
// ==========================================
function clearSearchAndShowAll() {
    currentSearchTerm = '';
    originalSearchTerm = '';
    
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    if (typeof loadSKDocuments === 'function') {
        loadSKDocuments(1);
    }
}

// ==========================================
// LOAD SK DOCUMENTS WITH PAGINATION & SEARCH
// ==========================================
function loadSKDocuments(page = 1) {
    console.log('📄 loadSKDocuments() called, page:', page, 'search:', currentSearchTerm, 'original:', originalSearchTerm);
    currentSKPage = page;
    
    const loadingEl = document.getElementById('loadingState');
    const tableEl = document.getElementById('documentsTable');
    const mobileListEl = document.getElementById('mobileDocumentsList');
    const emptyEl = document.getElementById('emptyState');
    const tbodyEl = document.getElementById('documentsBody');
    const paginationWrapper = document.getElementById('paginationWrapper');
    
    if (!tbodyEl) {
        console.error('❌ documentsBody not found!');
        return;
    }
    
    if (loadingEl) loadingEl.style.display = 'block';
    if (tableEl) tableEl.style.display = 'none';
    if (mobileListEl) mobileListEl.style.display = 'none';
    if (emptyEl) emptyEl.style.display = 'none';
    if (paginationWrapper) {
        paginationWrapper.classList.remove('visible');
        paginationWrapper.style.display = 'none';
    }
    
    // Build API URL
    let apiUrl = `/api/v1/dokumens?page=${page}&per_page=${currentPerPage}`;
    if (currentSearchTerm) {
        apiUrl += `&search=${encodeURIComponent(currentSearchTerm)}`;
    }
    
    fetch(apiUrl)
        .then(res => {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(result => {
            if (!result.success) throw new Error(result.message);
            
            const docs = result.data || [];
            skPagination = result.pagination || null;
            
            if (loadingEl) loadingEl.style.display = 'none';
            
            if (docs.length === 0) {
                // Sembunyikan table dan mobile list
                if (tableEl) tableEl.style.display = 'none';
                if (mobileListEl) {
                    mobileListEl.style.display = 'none';
                    mobileListEl.innerHTML = ''; // ← CLEAR mobile cards
                }
                
                if (emptyEl) {
                    emptyEl.style.display = 'block';
                    
                    // Ambil semua elemen yang dibutuhkan
                    const titleEl = document.getElementById('emptyStateTitle');
                    const searchTermEl = document.getElementById('emptyStateSearchTerm');
                    const textEl = document.getElementById('emptyStateText');
                    const btnHome = document.getElementById('btnBackToHome');
                    const btnShowAll = document.getElementById('btnShowAllDocs');
                    
                    if (titleEl && searchTermEl && textEl && btnHome && btnShowAll) {
                        // Cek apakah ada pencarian aktif
                        const hasSearch = currentSearchTerm && originalSearchTerm;
                        
                        if (hasSearch) {
                            // Empty State 2: Search tidak menemukan hasil
                            titleEl.textContent = 'Dokumen Tidak Ditemukan';
                            
                            // Tampilkan search term dalam box terpisah
                            searchTermEl.innerHTML = `
                                <div class="sk-search-term-label">Pencarian:</div>
                                <div class="sk-search-term-value">"${originalSearchTerm}"</div>
                            `;
                            searchTermEl.style.display = 'block';
                            
                            textEl.textContent = 'Silakan coba kata kunci lain atau tampilkan semua dokumen.';
                            
                            // Show tombol "Tampilkan Semua Dokumen", hide "Kembali ke Beranda"
                            btnHome.style.display = 'none';
                            btnShowAll.style.display = 'inline-flex';
                            
                        } else {
                            // Empty State 1: Memang tidak ada dokumen
                            titleEl.textContent = 'Belum Ada Dokumen';
                            
                            // Hide search term box
                            searchTermEl.style.display = 'none';
                            
                            textEl.textContent = 'Dokumen SK dan surat resmi akan segera diunggah. Silakan kunjungi kembali nanti untuk update terbaru.';
                            
                            // Show tombol "Kembali ke Beranda", hide "Tampilkan Semua Dokumen"
                            btnHome.style.display = 'inline-flex';
                            btnShowAll.style.display = 'none';
                        }
                    }
                }
                if (paginationWrapper) paginationWrapper.style.display = 'none';
                return;
            }
            
            // Reset empty state jika ada dokumen
            if (emptyEl) {
                emptyEl.style.display = 'none';
                const searchTermEl = document.getElementById('emptyStateSearchTerm');
                if (searchTermEl) searchTermEl.style.display = 'none';
            }
            
            // === RENDER DESKTOP TABLE ===
            tbodyEl.innerHTML = docs.map((doc, i) => {
                const docName = doc.name || 'Tanpa Judul';
                const fileName = doc.file_name || '';
                const docDate = doc.formatted_date || '-';
                const docSize = doc.file_size || '-';
                const fileUrl = doc.file_url || '#';
                const startNumber = (page - 1) * currentPerPage + i + 1;
                
                return `
                <tr class="sk-table-row">
                    <td class="sk-cell sk-cell-no">${startNumber}</td>
                    <td class="sk-cell sk-cell-name">
                        <div class="sk-doc-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        <div class="sk-doc-info">
                            <div class="sk-doc-name" title="${docName}">${docName}</div>
                            ${fileName ? `<div class="sk-doc-file" title="${fileName}">${fileName}</div>` : ''}
                        </div>
                    </td>
                    <td class="sk-cell sk-cell-date">
                        <svg class="sk-cell-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span>${docDate}</span>
                    </td>
                    <td class="sk-cell sk-cell-size">
                        <svg class="sk-cell-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="17 8 12 3 7 8"/>
                            <line x1="12" y1="3" x2="12" y2="15"/>
                        </svg>
                        <span>${docSize}</span>
                    </td>
                    <td class="sk-cell sk-cell-action">
                        <div class="sk-action-buttons">
                            <a href="${fileUrl}" target="_blank" class="sk-action-btn sk-view-btn" title="Lihat Dokumen">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            <a href="${fileUrl}" download="${fileName}" class="sk-action-btn sk-download-btn" title="Unduh Dokumen">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>`;
            }).join('');
            
            // === RENDER MOBILE CARDS ===
            if (mobileListEl) {
                mobileListEl.innerHTML = docs.map((doc, i) => {
                    const docName = doc.name || 'Tanpa Judul';
                    const docDate = doc.formatted_date || '-';
                    const docSize = doc.file_size || '-';
                    const fileUrl = doc.file_url || '#';
                    const fileName = doc.file_name || 'document.pdf';
                    
                    return `
                    <div class="sk-mobile-card">
                        <div class="sk-mobile-card-header">
                            <div class="sk-mobile-doc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </div>
                            <div class="sk-mobile-doc-info">
                                <div class="sk-mobile-doc-name">${docName}</div>
                                <div class="sk-mobile-doc-meta">
                                    <span class="sk-mobile-doc-date">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                            <line x1="16" y1="2" x2="16" y2="6"/>
                                            <line x1="8" y1="2" x2="8" y2="6"/>
                                        </svg>
                                        ${docDate}
                                    </span>
                                    <span class="sk-mobile-doc-size">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="17 8 12 3 7 8"/>
                                        </svg>
                                        ${docSize}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="sk-mobile-card-actions">
                            <a href="${fileUrl}" target="_blank" class="sk-mobile-action-btn sk-view-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span>Lihat</span>
                            </a>
                            <a href="${fileUrl}" download="${fileName}" class="sk-mobile-action-btn sk-download-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <span>Unduh</span>
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
            
            if (tableEl) tableEl.style.display = 'block';
            if (mobileListEl) mobileListEl.style.display = 'flex';
            
            // Render Pagination
            if (skPagination && skPagination.last_page > 1) {
                renderSKPagination();
                if (paginationWrapper) {
                    paginationWrapper.classList.add('visible');
                    paginationWrapper.style.display = 'block';
                }
            } else {
                if (paginationWrapper) {
                    paginationWrapper.classList.remove('visible');
                    paginationWrapper.style.display = 'none';
                }
            }
            
            console.log('✅ Table rendered:', docs.length, 'documents, page:', page);
        })
        .catch(err => {
            console.error('❌ Error:', err);
            if (loadingEl) {
                loadingEl.innerHTML = `
                    <div style="text-align:center;padding:2rem">
                        <div style="width:80px;height:80px;margin:0 auto 1.5rem;background:rgba(239,68,68,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        <div style="color:var(--text-dark);font-size:1.1rem;font-weight:600;margin-bottom:0.5rem">Gagal Memuat Dokumen</div>
                        <div style="color:var(--text-muted);font-size:0.9rem;margin-bottom:1.5rem">${err.message}</div>
                        <button onclick="loadSKDocuments(${page})" style="padding:0.75rem 2rem;background:linear-gradient(135deg,var(--primary),var(--primary-light));color:#fff;border:none;border-radius:10px;cursor:pointer;font-weight:600;font-size:0.9rem;box-shadow:0 4px 12px rgba(15,107,99,0.3)">
                            Coba Lagi
                        </button>
                    </div>
                `;
            }
        });
}

// ==========================================
// RENDER SK PAGINATION
// ==========================================
function renderSKPagination() {
    if (!skPagination) {
        console.error('❌ No pagination data');
        return;
    }
    
    const pageNumbersEl = document.getElementById('pageNumbers');
    const prevBtn = document.getElementById('prevPageBtn');
    const nextBtn = document.getElementById('nextPageBtn');
    const infoEl = document.getElementById('paginationInfo');
    
    if (!pageNumbersEl || !prevBtn || !nextBtn) {
        console.error('❌ Pagination elements not found!');
        return;
    }
    
    const { current_page, last_page, from, to, total } = skPagination;
    
    console.log('📊 Pagination Data:', { current_page, last_page, from, to, total });
    
    prevBtn.disabled = current_page === 1;
    nextBtn.disabled = current_page === last_page || last_page === 0;
    
    pageNumbersEl.innerHTML = '';
    
    if (last_page <= 1) {
        console.log('ℹ️ Only 1 page, hiding page numbers');
        return;
    }
    
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
    
    pages.forEach(page => {
        if (page === '...') {
            const dots = document.createElement('span');
            dots.className = 'sk-pagination-dots';
            dots.textContent = '...';
            pageNumbersEl.appendChild(dots);
        } else {
            const btn = document.createElement('button');
            btn.className = 'sk-pagination-btn';
            btn.textContent = page;
            if (page === current_page) btn.classList.add('active');
            btn.onclick = () => changeSKPage(page);
            pageNumbersEl.appendChild(btn);
        }
    });
    
    if (infoEl) {
        if (total === 0) {
            infoEl.innerHTML = 'Tidak ada dokumen';
        } else {
            infoEl.innerHTML = `Menampilkan <strong>${from || 0}</strong> - <strong>${to || 0}</strong> dari <strong>${total || 0}</strong> dokumen`;
        }
    }
    
    console.log('✅ Pagination rendered successfully');
}

// ==========================================
// CHANGE SK PAGE
// ==========================================
function changeSKPage(page) {
    if (page === 'prev') {
        page = currentSKPage - 1;
    } else if (page === 'next') {
        page = currentSKPage + 1;
    }
    
    if (!skPagination || page < 1 || page > skPagination.last_page) return;
    
    // Scroll dengan offset (100px di atas section)
    const skSection = document.querySelector('.sk-section');
    if (skSection) {
        const elementPosition = skSection.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - 100; // Offset 100px
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
    
    loadSKDocuments(page);
}

// ==========================================
// SPA INITIALIZATION
// ==========================================
window.showPage = function(pageName) {
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    const targetPage = document.getElementById(pageName + 'Page');
    if (targetPage) {
        targetPage.classList.add('active');
        window.scrollTo(0, 0);
    }
    document.querySelectorAll('.navbar-links a').forEach(link => {
        link.classList.remove('active-link');
    });
    const activeLink = document.querySelector(`.navbar-links a[data-page="${pageName}"]`);
    if (activeLink) activeLink.classList.add('active-link');
};

document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 DOMContentLoaded | SPA Router Starting...');
    
    let targetPage = window.location.hash.replace('#', '');
    console.log('🔍 Hash detected:', targetPage || '(none)');
    
    const isValidPage = targetPage && document.getElementById('page-' + targetPage);
    if (!isValidPage) {
        targetPage = 'beranda';
        console.log('️ Invalid/Empty hash. Defaulting to: beranda');
    }
    
    setTimeout(() => {
        if (typeof navigateTo === 'function') {
            console.log(' Triggering SPA navigation to:', targetPage);
            navigateTo(targetPage);
            if (typeof updateActiveNav === 'function') updateActiveNav(targetPage);
        } else {
            console.error('❌ navigateTo function not found!');
        }
    }, 50);
});