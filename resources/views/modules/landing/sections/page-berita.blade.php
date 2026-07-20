<div class="page" id="page-berita">
    <div class="page-header">
        <div class="page-header-content">
            <h1>Berita & Kegiatan _TEST</h1>
            <p>Informasi terbaru seputar kegiatan dan program PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a onclick="navigateTo('beranda')">Beranda</a>
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
                    <select id="newsSortSelect" class="news-sort-select" onchange="changeNewsSort(this.value)" title="Pilih cara pengurutan berita">
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
                        <select id="newsPerPageSelect" class="news-perpage-select" onchange="changeNewsPerPage(this.value)">
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
                    <button id="newsPrevBtn" class="news-pagination-btn" onclick="changeNewsPage('prev')" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                        <span class="desktop-only">Sebelumnya</span>
                    </button>
                    <div id="newsPageNumbers" class="news-page-numbers"></div>
                    <button id="newsNextBtn" class="news-pagination-btn" onclick="changeNewsPage('next')">
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
                <a onclick="navigateTo('beranda')" class="news-back-btn">
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

<script>
// ==========================================
// PAGINATION & SORT STATE
// ==========================================
if (typeof window.newsCurrentPage === 'undefined') {
    window.newsCurrentPage = 1;
    window.newsPerPage = 6;
    window.newsTotalPages = 0;
    window.newsTotalItems = 0;
}

let newsSort = 'latest';
let isBeritaLoading = false;
let beritaLoaded = false;

let newsCurrentPage = window.newsCurrentPage;
let newsPerPage = window.newsPerPage;
let newsTotalPages = window.newsTotalPages;
let newsTotalItems = window.newsTotalItems;

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    // 1. Ambil preferensi dari localStorage
    const savedPerPage = localStorage.getItem('news_per_page') || '6';
    const savedSort = localStorage.getItem('news_sort') || 'latest';

    // 2. Update variabel state
    newsPerPage = parseInt(savedPerPage);
    newsSort = savedSort;
    window.newsPerPage = newsPerPage; // Sinkronisasi dengan window object

    // 3. Set nilai dropdown di HTML
    const perPageSelect = document.getElementById('newsPerPageSelect');
    const sortSelect = document.getElementById('newsSortSelect');

    if (perPageSelect) perPageSelect.value = newsPerPage;
    if (sortSelect) sortSelect.value = newsSort;

    // 4. Update teks deskripsi sorting sesuai nilai awal
    updateSortDescription(newsSort);

    // 5. Cek dan muat data jika halaman berita sudah aktif
    checkAndLoadBerita();
});

// ==========================================
// CHANGE SORT
// ==========================================
function changeNewsSort(value) {
    newsSort = value;
    localStorage.setItem('news_sort', newsSort);
    newsCurrentPage = 1;
    loadBeritaData();
}

// ==========================================
// CHANGE PER PAGE
// ==========================================
function changeNewsPerPage(value) {
    newsPerPage = parseInt(value);
    window.newsPerPage = newsPerPage;
    localStorage.setItem('news_per_page', newsPerPage);
    newsCurrentPage = 1;
    window.newsCurrentPage = 1;

    // AUTO SCROLL KE JUDUL "Daftar Berita" DENGAN OFFSET
    const sectionTitle = document.querySelector('.news-section-title');
    if (sectionTitle) {
        const navbarHeight = 165; // Tinggi navbar
        const elementPosition = sectionTitle.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - navbarHeight;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    loadBeritaData();
}

// ==========================================
// CHANGE PAGE
// ==========================================
function changeNewsPage(direction) {
    if (isBeritaLoading) return;

    if (direction === 'prev') {
        if (newsCurrentPage > 1) newsCurrentPage--;
        else return;
    } else if (direction === 'next') {
        if (newsCurrentPage < newsTotalPages) newsCurrentPage++;
        else return;
    } else {
        newsCurrentPage = parseInt(direction);
    }

    window.newsCurrentPage = newsCurrentPage;

    // AUTO SCROLL KE JUDUL "Daftar Berita" DENGAN OFFSET
    const sectionTitle = document.querySelector('.news-section-title');
    if (sectionTitle) {
        const navbarHeight = 165; // Tinggi navbar
        const elementPosition = sectionTitle.getBoundingClientRect().top + window.pageYOffset;
        const offsetPosition = elementPosition - navbarHeight;

        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }

    loadBeritaData();
}

// ==========================================
// LOAD BERITA DATA
// ==========================================
async function loadBeritaData() {
    if (isBeritaLoading) return;
    isBeritaLoading = true;

    const loadingEl = document.getElementById('news-loading');
    const gridEl = document.getElementById('newsFullGrid');
    const emptyEl = document.getElementById('news-empty-state');
    const paginationWrapper = document.getElementById('newsPaginationWrapper');

    if (!loadingEl || !gridEl) {
        isBeritaLoading = false;
        return;
    }

    // Show loading
    loadingEl.style.display = 'block';
    loadingEl.innerHTML = '<div class="news-loading-text">Memuat berita terbaru...</div>';
    gridEl.style.display = 'none';
    gridEl.innerHTML = '';
    if (emptyEl) emptyEl.style.display = 'none';
    if (paginationWrapper) paginationWrapper.classList.add('hidden');

    try {
        // Build query params
        const params = new URLSearchParams({
            page: newsCurrentPage,
            limit: newsPerPage,
            sort: newsSort
        });

        const response = await fetch(`/api/v1/news?${params.toString()}`);
        const result = await response.json();

        loadingEl.style.display = 'none';

        if (result.success && result.data && result.data.length > 0) {
            newsTotalItems = result.total || result.data.length;
            newsTotalPages = result.last_page || Math.ceil(newsTotalItems / newsPerPage);

            gridEl.style.display = 'grid';

            gridEl.innerHTML = result.data.map(news => {
                const imgUrl = news.image_path
                    ? '/storage/' + news.image_path
                    : '/assets/landing/images/berita/default.jpg';

                const date = news.published_at || news.created_at;
                const formattedDate = date ? new Date(date).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }) : '-';

                const newsUrl = news.slug ? '/berita/' + news.slug : '/berita/' + news.id;

                return `
                <a href="${newsUrl}" class="news-card-link">
                    <article class="news-card-full">
                        <div class="news-card-image-wrapper">
                            <img src="${imgUrl}" alt="${news.title}" class="news-card-image"
                                 onerror="this.src='/assets/landing/images/berita/default.jpg'">
                        </div>
                        <div class="news-card-content">
                            <div class="news-card-meta">
                                <span class="news-card-category">${news.category || 'Umum'}</span>
                                <span class="news-card-date">${formattedDate}</span>
                            </div>
                            <h3 class="news-card-title">${news.title}</h3>
                            <p class="news-card-excerpt">${news.excerpt || news.content || ''}</p>
                            <span class="news-card-readmore">
                                Baca Selengkapnya
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </span>
                        </div>
                    </article>
                </a>
                `;
            }).join('');

            renderNewsPagination();
        } else {
            if (emptyEl) emptyEl.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading berita:', error);
        loadingEl.innerHTML = '<p class="news-error-text">Gagal memuat berita. Silakan refresh halaman.</p>';
    } finally {
        isBeritaLoading = false;
    }
}

// ==========================================
// RENDER PAGINATION
// ==========================================
function renderNewsPagination() {
    const paginationWrapper = document.getElementById('newsPaginationWrapper');
    const pageNumbersEl = document.getElementById('newsPageNumbers');
    const prevBtn = document.getElementById('newsPrevBtn');
    const nextBtn = document.getElementById('newsNextBtn');
    const infoEl = document.getElementById('newsPaginationInfo');

    if (!paginationWrapper || !pageNumbersEl) return;

    if (newsTotalPages <= 1) {
        paginationWrapper.classList.add('hidden');
        return;
    }

    paginationWrapper.classList.remove('hidden');

    if (prevBtn) prevBtn.disabled = newsCurrentPage === 1;
    if (nextBtn) nextBtn.disabled = newsCurrentPage === newsTotalPages;

    pageNumbersEl.innerHTML = '';

    let pages = [];

    if (newsTotalPages <= 7) {
        for (let i = 1; i <= newsTotalPages; i++) pages.push(i);
    } else {
        if (newsCurrentPage <= 3) {
            pages = [1, 2, 3, 4, '...', newsTotalPages];
        } else if (newsCurrentPage >= newsTotalPages - 2) {
            pages = [1, '...', newsTotalPages - 3, newsTotalPages - 2, newsTotalPages - 1, newsTotalPages];
        } else {
            pages = [1, '...', newsCurrentPage - 1, newsCurrentPage, newsCurrentPage + 1, '...', newsTotalPages];
        }
    }

    pages.forEach(function(page) {
        if (page === '...') {
            const dots = document.createElement('span');
            dots.className = 'news-pagination-dots';
            dots.textContent = '...';
            pageNumbersEl.appendChild(dots);
        } else {
            const btn = document.createElement('button');
            btn.className = 'news-pagination-btn';
            if (page === newsCurrentPage) btn.classList.add('active');
            btn.textContent = page;
            btn.onclick = function() { changeNewsPage(page); };
            pageNumbersEl.appendChild(btn);
        }
    });

    if (infoEl) {
        const from = (newsCurrentPage - 1) * newsPerPage + 1;
        const to = Math.min(newsCurrentPage * newsPerPage, newsTotalItems);
        infoEl.innerHTML = `Menampilkan <strong>${from}</strong> - <strong>${to}</strong> dari <strong>${newsTotalItems}</strong> berita`;
    }
}

// ==========================================
// SAFE PAGE ACTIVATION
// ==========================================
function checkAndLoadBerita() {
    const page = document.getElementById('page-berita');
    if (page && page.classList.contains('active') && !beritaLoaded) {
        beritaLoaded = true;
        loadBeritaData();
    }
}

document.addEventListener('DOMContentLoaded', checkAndLoadBerita);

window.addEventListener('hashchange', function() {
    if (window.location.hash === '#berita') {
        beritaLoaded = false;
        setTimeout(checkAndLoadBerita, 100);
    }
});

// Update description based on selected sort
function updateSortDescription(value) {
    const descriptions = {
        'latest': 'Menampilkan berita dari yang paling baru dipublikasikan',
        'oldest': 'Menampilkan berita dari yang paling lama dipublikasikan',
        'title_asc': 'Menampilkan berita berdasarkan judul dari A sampai Z',
        'title_desc': 'Menampilkan berita berdasarkan judul dari Z sampai A'
    };

    const descEl = document.getElementById('sortDescription');
    if (descEl) {
        descEl.textContent = descriptions[value] || descriptions['latest'];
    }
}

// Update function changeNewsSort
function changeNewsSort(value) {
    newsSort = value;
    localStorage.setItem('news_sort', newsSort);
    newsCurrentPage = 1;
    updateSortDescription(value); // Add this line
    loadBeritaData();
}


// Safe observer
const beritaPage = document.getElementById('page-berita');
if (beritaPage) {
    const beritaObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                if (beritaPage.classList.contains('active') && !beritaLoaded) {
                    beritaLoaded = true;
                    loadBeritaData();
                } else if (!beritaPage.classList.contains('active')) {
                    beritaLoaded = false;
                }
            }
        });
    });

    beritaObserver.observe(beritaPage, {
        attributes: true,
        attributeFilter: ['class']
    });
}
</script>
