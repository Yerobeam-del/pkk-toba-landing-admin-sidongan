/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
/**
 * Desa Handler Module - DYNAMIC VERSION
 * Fetches village data from Laravel API instead of hardcoded array.
 * Menampilkan data dengan pagination klien (selaras dengan halaman
 * Berita serta SK & Dokumen): search, filter kecamatan, dan
 * selector "Tampilkan" per halaman.
 */

let desaData = [];
let filteredDesaData = [];
let isDesaLoaded = false;
let desaSearchTerm = '';
let currentDesaFilter = 'all';

// Pagination state (selaras dengan news-handler.js)
window.desaPerPage = 6;
window.desaCurrentPage = 1;
window.desaTotalPages = 1;
window.desaTotalItems = 0;

let desaPerPage = window.desaPerPage;
let desaCurrentPage = window.desaCurrentPage;
let desaTotalPages = window.desaTotalPages;
let desaTotalItems = window.desaTotalItems;

/**
 * Placeholder SVG (bukan emoji) untuk desa tanpa gambar
 */
const DESA_PLACEHOLDER_ICON = '<svg viewBox="0 0 24 24" fill="none" stroke="#166534" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:64px;height:64px;opacity:0.35"><path d="M3 21h18M5 21V7l8-4 8 4v14M8 21v-4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v4"/><rect x="9" y="9" width="6" height="6"/></svg>';

/**
 * Fetch data from Laravel API
 */
async function loadDesaDataFromAPI() {
    if (isDesaLoaded) return;

    try {
        const response = await fetch('/api/v1/desas');
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message || 'API error');
        }

        desaData = [];

        result.data.forEach(kec => {
            if (kec.desas && Array.isArray(kec.desas) && kec.desas.length > 0) {
                const filterSlug = slugify(kec.name);

                kec.desas.forEach(desa => {
                    desaData.push({
                        name: desa.name,
                        kecamatan: kec.name,
                        image: desa.image || null,
                        population: desa.population ? Number(desa.population).toLocaleString('id-ID') : '—',
                        households: desa.households ? Number(desa.households).toLocaleString('id-ID') : '—',
                        filter: filterSlug,
                        id: desa.id,
                        kode_wilayah: desa.kode_wilayah
                    });
                });
            }
        });

        isDesaLoaded = true;

        if (document.getElementById('desaGrid')) {
            applyDesaFilters();
            renderDesaPage(1);
        }

    } catch (error) {
        console.error('Failed to load desa data:', error);
        desaData = [];
    }
}

/**
 * Helper: Create slug from text
 */
function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .trim();
}

/**
 * Helper: Escape HTML (selaras dengan news-handler.js)
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Fallback gambar rusak -> placeholder SVG (dipasang sekali, fase capture
 * karena event "error" tidak bubble). Pengganti atribut onerror inline.
 */
document.addEventListener('error', function (e) {
    const img = e.target;
    if (img && img.classList && img.classList.contains('desa-card-image')) {
        const wrap = img.parentElement;
        if (wrap) {
            wrap.innerHTML = `<div class="desa-card-image-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">${DESA_PLACEHOLDER_ICON}</div>`;
        }
    }
}, true);

/**
 * Render single desa card - CLEAN & CONSISTENT (NO DESCRIPTION)
 */
function renderDesaCard(desa) {
    const hasImage = desa.image && desa.image.trim() !== '';

    // Image section: SAME HEIGHT for all cards (200px)
    const imageSection = hasImage
        ? `<div class="desa-card-image-wrapper" style="width:100%;height:200px;overflow:hidden;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
            <img src="${desa.image}"
                 alt="${escapeHtml(desa.name)}"
                 class="desa-card-image"
                 style="width:100%;height:100%;object-fit:cover;display:block">
           </div>`
        : `<div class="desa-card-image-placeholder" style="width:100%;height:200px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)">
            ${DESA_PLACEHOLDER_ICON}
           </div>`;

    return `<div class="desa-card" data-filter="${desa.filter}" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);display:flex;flex-direction:column;height:100%">
        ${imageSection}

        <div class="desa-card-body" style="padding:1.25rem;flex:1;display:flex;flex-direction:column;justify-content:space-between">
            <div>
                <h3 style="margin:0 0 0.25rem 0;font-size:1.15rem;font-weight:700;color:#166534;line-height:1.3">${escapeHtml(desa.name)}</h3>
                <p style="margin:0;font-size:0.9rem;color:var(--text-muted);display:flex;align-items:center;gap:0.35rem">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;flex-shrink:0">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">Kec. ${escapeHtml(desa.kecamatan)}</span>
                </p>
            </div>
        </div>

        <div class="desa-card-stats" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;padding:1rem 1.25rem;background:#f8fafc;border-top:2px solid #f0fdf4;flex-shrink:0">
            <div class="desa-stat" style="text-align:center">
                <div class="desa-stat-number" style="font-size:1.25rem;font-weight:700;color:#166534;display:flex;align-items:center;justify-content:center;gap:0.4rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    ${desa.population}
                </div>
                <div class="desa-stat-label" style="font-size:0.8rem;color:var(--text-muted)">Warga</div>
            </div>
            <div class="desa-stat" style="text-align:center">
                <div class="desa-stat-number" style="font-size:1.25rem;font-weight:700;color:#166534;display:flex;align-items:center;justify-content:center;gap:0.4rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/></svg>
                    ${desa.households}
                </div>
                <div class="desa-stat-label" style="font-size:0.8rem;color:var(--text-muted)">KRT</div>
            </div>
        </div>
    </div>`;
}

/**
 * Terapkan filter kecamatan + pencarian, hitung ulang state pagination
 */
function applyDesaFilters() {
    let filtered = currentDesaFilter === 'all'
        ? desaData
        : desaData.filter(d => d.filter === currentDesaFilter);

    if (desaSearchTerm) {
        filtered = filtered.filter(d =>
            d.name.toLowerCase().includes(desaSearchTerm) ||
            d.kecamatan.toLowerCase().includes(desaSearchTerm)
        );
    }

    filteredDesaData = filtered;
    desaTotalItems = filtered.length;
    desaTotalPages = Math.max(1, Math.ceil(desaTotalItems / desaPerPage));
}

/**
 * Populate grid dengan filtered data (supports kecamatan filter + search)
 * Dipanggil oleh navigation.js saat halaman desa diaktifkan.
 * Tanpa argumen: pertahankan filter kecamatan yang sedang aktif.
 */
function populateDesa(filter) {
    if (filter !== undefined && filter !== null) {
        currentDesaFilter = filter;
    }
    desaCurrentPage = 1;

    // Sinkronkan status aktif tombol filter
    document.querySelectorAll('#desaFilter .filter-btn').forEach(b => {
        b.classList.toggle('active', (b.getAttribute('data-filter') || 'all') === currentDesaFilter);
    });

    applyDesaFilters();
    renderDesaPage(1);
}

/**
 * Render satu halaman grid desa (selaras dengan populateNewsFull)
 */
function renderDesaPage(page = 1) {
    const grid = document.getElementById('desaGrid');
    const loadingEl = document.getElementById('desa-loading');
    const emptyEl = document.getElementById('desa-empty-state');
    const paginationWrapper = document.getElementById('desaPaginationWrapper');
    if (!grid) return;

    // Data belum terload -> biarkan loading state tampil, mulai fetch
    if (!isDesaLoaded && desaData.length === 0) {
        loadDesaDataFromAPI();
        return;
    }

    // Hide loading
    if (loadingEl) loadingEl.style.display = 'none';

    // Kondisi kosong: tidak ada data sama sekali / filter+pencarian tanpa hasil
    if (filteredDesaData.length === 0) {
        grid.style.display = 'none';
        grid.innerHTML = '';
        if (paginationWrapper) paginationWrapper.classList.add('hidden', 'u-hidden');
        if (emptyEl) emptyEl.style.display = 'block';
        return;
    }

    if (emptyEl) emptyEl.style.display = 'none';
    grid.style.display = 'grid';

    // Samakan jumlah item per halaman dengan pilihan pengguna
    if (desaCurrentPage > desaTotalPages) desaCurrentPage = desaTotalPages;
    if (desaCurrentPage < 1) desaCurrentPage = 1;
    page = desaCurrentPage;

    const start = (page - 1) * desaPerPage;
    const pageItems = filteredDesaData.slice(start, start + desaPerPage);
    grid.innerHTML = pageItems.map(d => renderDesaCard(d)).join('');

    // Render pagination
    renderDesaPagination();
}

/**
 * Render pagination controls (selaras dengan renderNewsPagination)
 */
function renderDesaPagination() {
    const paginationWrapper = document.getElementById('desaPaginationWrapper');
    const pageNumbersEl = document.getElementById('desaPageNumbers');
    const prevBtn = document.getElementById('desaPrevBtn');
    const nextBtn = document.getElementById('desaNextBtn');
    const infoEl = document.getElementById('desaPaginationInfo');

    if (!paginationWrapper || !pageNumbersEl) return;

    // Jika hanya 1 halaman, sembunyikan pagination
    if (desaTotalPages <= 1) {
        paginationWrapper.classList.add('hidden', 'u-hidden');
        return;
    }

    paginationWrapper.classList.remove('hidden', 'u-hidden');

    // Update prev/next buttons
    if (prevBtn) prevBtn.disabled = desaCurrentPage === 1;
    if (nextBtn) nextBtn.disabled = desaCurrentPage === desaTotalPages;

    // Generate page numbers
    pageNumbersEl.innerHTML = '';

    let pages = [];

    // Logic untuk menampilkan page numbers dengan ellipsis
    if (desaTotalPages <= 7) {
        // Jika total halaman <= 7, tampilkan semua
        for (let i = 1; i <= desaTotalPages; i++) {
            pages.push(i);
        }
    } else {
        // Jika total halaman > 7, gunakan ellipsis
        if (desaCurrentPage <= 3) {
            // Di awal: 1 2 3 4 ... last
            pages = [1, 2, 3, 4, '...', desaTotalPages];
        } else if (desaCurrentPage >= desaTotalPages - 2) {
            // Di akhir: 1 ... (last-3) (last-2) (last-1) last
            pages = [1, '...', desaTotalPages - 3, desaTotalPages - 2, desaTotalPages - 1, desaTotalPages];
        } else {
            // Di tengah: 1 ... (current-1) current (current+1) ... last
            pages = [1, '...', desaCurrentPage - 1, desaCurrentPage, desaCurrentPage + 1, '...', desaTotalPages];
        }
    }

    pages.forEach(function(page) {
        if (page === '...') {
            const dots = document.createElement('span');
            dots.className = 'desa-pagination-dots';
            dots.textContent = '...';
            pageNumbersEl.appendChild(dots);
        } else {
            const btn = document.createElement('button');
            btn.className = 'desa-pagination-btn';
            if (page === desaCurrentPage) {
                btn.classList.add('active');
            }
            btn.textContent = page;
            btn.onclick = function() {
                changeDesaPage(page);
            };
            pageNumbersEl.appendChild(btn);
        }
    });

    // Update info text
    if (infoEl) {
        const from = (desaCurrentPage - 1) * desaPerPage + 1;
        const to = Math.min(desaCurrentPage * desaPerPage, desaTotalItems);
        infoEl.innerHTML = `Menampilkan <strong>${from}</strong> - <strong>${to}</strong> dari <strong>${desaTotalItems}</strong> desa`;
    }
}

/**
 * Change page (selaras dengan changeNewsPage)
 */
function changeDesaPage(direction) {
    if (direction === 'prev') {
        if (desaCurrentPage > 1) {
            desaCurrentPage--;
        } else {
            return;
        }
    } else if (direction === 'next') {
        if (desaCurrentPage < desaTotalPages) {
            desaCurrentPage++;
        } else {
            return;
        }
    } else {
        desaCurrentPage = parseInt(direction);
    }

    // Scroll to top of desa section
    const desaSection = document.querySelector('.desa-section-title') || document.querySelector('.page-header');
    if (desaSection) {
        desaSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    renderDesaPage(desaCurrentPage);
}

/**
 * Change per page (selaras dengan changeNewsPerPage)
 */
function changeDesaPerPage(value) {
    desaPerPage = parseInt(value);
    localStorage.setItem('desa_per_page', desaPerPage);
    desaCurrentPage = 1;
    applyDesaFilters();
    renderDesaPage(1);
}

/**
 * Filter handler (filter kecamatan)
 */
function filterDesa(filter) {
    populateDesa(filter);
}

/**
 * Initialize filters dynamically
 */
function initDesaFilters() {
    const filterContainer = document.getElementById('desaFilter');
    if (!filterContainer) return;

    const uniqueFilters = [...new Set(desaData.map(d => d.filter))].sort();

    let html = `<button class="filter-btn${currentDesaFilter === 'all' ? ' active' : ''}" data-desa-filter="all" data-filter="all">Semua Desa (${desaData.length})</button>`;

    uniqueFilters.forEach(slug => {
        const count = desaData.filter(d => d.filter === slug).length;
        const namaKec = desaData.find(d => d.filter === slug)?.kecamatan || slug;
        const active = currentDesaFilter === slug ? ' active' : '';
        html += `<button class="filter-btn${active}" data-desa-filter="${slug}" data-filter="${slug}">${escapeHtml(namaKec)} (${count})</button>`;
    });

    filterContainer.innerHTML = html;
    filterContainer.classList.remove('u-hidden');
}

// Initialize
let desaFiltersBound = false;

document.addEventListener('DOMContentLoaded', async () => {
    // Load per page preference
    const savedPerPage = localStorage.getItem('desa_per_page') || '6';
    desaPerPage = parseInt(savedPerPage);

    const perPageSelect = document.getElementById('desaPerPageSelect');
    if (perPageSelect && [...perPageSelect.options].some(o => o.value === String(desaPerPage))) {
        perPageSelect.value = desaPerPage;
    }

    await loadDesaDataFromAPI();

    if (document.getElementById('desaFilter')) {
        initDesaFilters();
    }

    if (!desaFiltersBound) {
        desaFiltersBound = true;
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#desaFilter .filter-btn');
            if (btn) filterDesa(btn.getAttribute('data-filter') || 'all', btn);
        });
    }

    if (document.getElementById('desaGrid')) {
        applyDesaFilters();
        renderDesaPage(1);
    }

    // Bind search input
    const searchInput = document.getElementById('desaSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            desaSearchTerm = this.value.trim().toLowerCase();
            desaCurrentPage = 1;
            applyDesaFilters();
            renderDesaPage(1);
        });
    }
});

// Export
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { desaData, renderDesaCard, populateDesa, filterDesa, loadDesaDataFromAPI, changeDesaPage, changeDesaPerPage };
}
/* Dikembangkan oleh Institut Teknologi Del */
