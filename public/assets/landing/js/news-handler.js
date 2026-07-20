/**
 * News Handler - Dynamic Content from API
 * Fetches news data from Laravel API endpoint
 */

// Base API URL
const API_BASE = '/api/v1';

if (typeof window.newsCurrentPage === 'undefined') {
    window.newsCurrentPage = 1;
    window.newsPerPage = 6;
    window.newsTotalPages = 0;
    window.newsTotalItems = 0;
}

let newsCurrentPage = window.newsCurrentPage;
let newsPerPage = window.newsPerPage;
let newsTotalPages = window.newsTotalPages;
let newsTotalItems = window.newsTotalItems;

/**
 * Check if news modal exists in DOM
 * @returns {Boolean}
 */
function hasNewsModal() {
    return document.getElementById('newsModal') !== null;
}

/**
 * Fetch news from API
 * @param {Object} params - Query parameters
 * @returns {Promise<Object>}
 */
async function fetchNews(params = {}) {
    try {
        const queryParams = new URLSearchParams({
            page: params.page || 1,
            limit: params.limit || newsPerPage,
            category: params.category || '',
            ...params
        });

        const response = await fetch(`${API_BASE}/news?${queryParams}`);
        const result = await response.json();

        if (result.success) {
            // Store pagination info
            newsTotalItems = result.total || result.data.length;
            newsTotalPages = result.last_page || Math.ceil(newsTotalItems / (params.limit || newsPerPage));
            newsCurrentPage = params.page || 1;

            return result;
        }
        throw new Error('Failed to fetch news');
    } catch (error) {
        console.error('Error fetching news:', error);
        return { data: window.landingNewsData || [], total: 0, last_page: 1 };
    }
}

/**
 * Render news card HTML for homepage (with modal onclick)
 * @param {Object} news - News object
 * @param {Number} index - Index for modal reference
 * @returns {String}
 */
function renderNewsCard(news, index) {
    const imageUrl = news.image_path
        ? `/storage/${news.image_path}`
        : '/assets/landing/images/berita/default.jpg';

    const publishedDate = news.published_at
        ? new Date(news.published_at).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        })
        : new Date(news.created_at).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        });

    const onclickAction = hasNewsModal()
        ? `onclick="openNewsModalBySlug('${news.slug}')"`
        : '';

    const cardTag = hasNewsModal() ? 'div' : 'a';
    const hrefAttr = !hasNewsModal() ? `href="/berita/${news.slug}"` : '';

    return `
    <${cardTag} class="news-card" ${onclickAction} ${hrefAttr}>
        <img src="${imageUrl}"
             alt="${escapeHtml(news.title)}"
             class="news-card-image"
             onerror="this.src='/assets/landing/images/berita/default.jpg'">
        <div class="news-card-body">
            <div class="news-card-date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                ${publishedDate}
            </div>
            <span class="news-card-category">${escapeHtml(news.category || 'Umum')}</span>
            <h3 class="news-card-title">${escapeHtml(news.title)}</h3>
            <p class="news-card-excerpt">${escapeHtml(news.excerpt || '')}</p>
            <span class="news-card-link">Baca Selengkapnya
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </span>
        </div>
    </${cardTag}>`;
}

/**
 * Render news card HTML for full page (direct link to detail)
 * @param {Object} news - News object
 * @returns {String}
 */
function renderNewsCardFull(news) {
    const imageUrl = news.image_path
        ? `/storage/${news.image_path}`
        : '/assets/landing/images/berita/default.jpg';

    const publishedDate = news.published_at
        ? new Date(news.published_at).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        })
        : new Date(news.created_at).toLocaleDateString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric'
        });

    const newsUrl = news.slug ? `/berita/${news.slug}` : `/berita/${news.id}`;

    return `
    <a href="${newsUrl}" class="news-card-link">
        <article class="news-card-full">
            <div class="news-card-image-wrapper">
                <img src="${imageUrl}"
                     alt="${escapeHtml(news.title)}"
                     class="news-card-image"
                     onerror="this.src='/assets/landing/images/berita/default.jpg'">
            </div>
            <div class="news-card-content">
                <div class="news-card-meta">
                    <span class="news-card-category">${escapeHtml(news.category || 'Umum')}</span>
                    <span class="news-card-date">${publishedDate}</span>
                </div>
                <h3 class="news-card-title">${escapeHtml(news.title)}</h3>
                <p class="news-card-excerpt">${escapeHtml(news.excerpt || news.content || '')}</p>
                <span class="news-card-readmore">
                    Baca Selengkapnya
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </span>
            </div>
        </article>
    </a>`;
}

/**
 * Escape HTML to prevent XSS
 * @param {String} text
 * @returns {String}
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Populate news grid (home page - 3 items)
 */
async function populateNewsHome() {
    const grid = document.getElementById('newsHomeGrid');
    if (!grid) return;

    grid.innerHTML = '<div class="col-span-3 text-center py-4"><div class="animate-pulse">Memuat berita...</div></div>';

    try {
        const result = await fetchNews({ limit: 3 });

        if (!result.data || result.data.length === 0) {
            grid.innerHTML = '<div class="col-span-3 text-center py-8 text-muted"><p>Belum ada berita terbaru.</p></div>';
            return;
        }

        grid.innerHTML = result.data.map((n, i) => renderNewsCard(n, i)).join('');
    } catch (error) {
        console.error('Error populating news:', error);
        grid.innerHTML = '<div class="col-span-3 text-center py-8 text-muted"><p>Gagal memuat berita.</p></div>';
    }
}

/**
 * Populate news grid (full page - with pagination)
 */
async function populateNewsFull(page = 1) {
    const grid = document.getElementById('newsFullGrid');
    const loadingEl = document.getElementById('news-loading');
    const emptyEl = document.getElementById('news-empty-state');
    const paginationWrapper = document.getElementById('newsPaginationWrapper');

    if (!grid) return;

    // Show loading
    if (loadingEl) {
        loadingEl.style.display = 'block';
        loadingEl.innerHTML = '<div class="news-loading-text">Memuat berita terbaru...</div>';
    }

    grid.style.display = 'none';
    grid.innerHTML = '';

    if (emptyEl) emptyEl.style.display = 'none';
    if (paginationWrapper) paginationWrapper.classList.add('hidden');

    try {
        const result = await fetchNews({ page: page, limit: newsPerPage });

        if (loadingEl) loadingEl.style.display = 'none';

        if (!result.data || result.data.length === 0) {
            if (emptyEl) emptyEl.style.display = 'block';
            return;
        }

        grid.style.display = 'grid';
        grid.innerHTML = result.data.map(n => renderNewsCardFull(n)).join('');

        // Render pagination
        renderNewsPagination();

    } catch (error) {
        console.error('Error populating full news:', error);
        if (loadingEl) {
            loadingEl.innerHTML = '<p class="news-error-text">Gagal memuat berita. Silakan refresh halaman.</p>';
            loadingEl.style.display = 'block';
        }
    }
}

/**
 * Render pagination controls
 */
function renderNewsPagination() {
    const paginationWrapper = document.getElementById('newsPaginationWrapper');
    const pageNumbersEl = document.getElementById('newsPageNumbers');
    const prevBtn = document.getElementById('newsPrevBtn');
    const nextBtn = document.getElementById('newsNextBtn');
    const infoEl = document.getElementById('newsPaginationInfo');

    if (!paginationWrapper || !pageNumbersEl) return;

    // Jika hanya 1 halaman, sembunyikan pagination
    if (newsTotalPages <= 1) {
        paginationWrapper.classList.add('hidden');
        return;
    }

    paginationWrapper.classList.remove('hidden');

    // Update prev/next buttons
    if (prevBtn) prevBtn.disabled = newsCurrentPage === 1;
    if (nextBtn) nextBtn.disabled = newsCurrentPage === newsTotalPages;

    // Generate page numbers
    pageNumbersEl.innerHTML = '';

    let pages = [];

    // Logic untuk menampilkan page numbers dengan ellipsis
    if (newsTotalPages <= 7) {
        // Jika total halaman <= 7, tampilkan semua
        for (let i = 1; i <= newsTotalPages; i++) {
            pages.push(i);
        }
    } else {
        // Jika total halaman > 7, gunakan ellipsis
        if (newsCurrentPage <= 3) {
            // Di awal: 1 2 3 4 ... last
            pages = [1, 2, 3, 4, '...', newsTotalPages];
        } else if (newsCurrentPage >= newsTotalPages - 2) {
            // Di akhir: 1 ... (last-3) (last-2) (last-1) last
            pages = [1, '...', newsTotalPages - 3, newsTotalPages - 2, newsTotalPages - 1, newsTotalPages];
        } else {
            // Di tengah: 1 ... (current-1) current (current+1) ... last
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
            if (page === newsCurrentPage) {
                btn.classList.add('active');
            }
            btn.textContent = page;
            btn.onclick = function() {
                changeNewsPage(page);
            };
            pageNumbersEl.appendChild(btn);
        }
    });

    // Update info text
    if (infoEl) {
        const from = (newsCurrentPage - 1) * newsPerPage + 1;
        const to = Math.min(newsCurrentPage * newsPerPage, newsTotalItems);
        infoEl.innerHTML = `Menampilkan <strong>${from}</strong> - <strong>${to}</strong> dari <strong>${newsTotalItems}</strong> berita`;
    }
}

/**
 * Change page
 */
function changeNewsPage(direction) {
    if (direction === 'prev') {
        if (newsCurrentPage > 1) {
            newsCurrentPage--;
        } else {
            return;
        }
    } else if (direction === 'next') {
        if (newsCurrentPage < newsTotalPages) {
            newsCurrentPage++;
        } else {
            return;
        }
    } else {
        newsCurrentPage = parseInt(direction);
    }

    // Scroll to top of news section
    const newsSection = document.querySelector('.news-section-title') || document.querySelector('.page-header');
    if (newsSection) {
        newsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    populateNewsFull(newsCurrentPage);
}

/**
 * Change per page
 */
function changeNewsPerPage(value) {
    newsPerPage = parseInt(value);
    localStorage.setItem('news_per_page', newsPerPage);
    newsCurrentPage = 1;
    populateNewsFull(1);
}

/**
 * Open news modal by fetching single news by slug
 * @param {String} slug
 */
async function openNewsModalBySlug(slug) {
    if (!hasNewsModal()) {
        window.location.href = `/berita/${slug}`;
        return;
    }

    try {
        const response = await fetch(`${API_BASE}/news/${slug}`);
        const result = await response.json();

        if (result.success) {
            const news = result.data;
            const imageUrl = news.image_path
                ? `/storage/${news.image_path}`
                : '/assets/landing/images/berita/default.jpg';

            const modalImage = document.getElementById('newsModalImage');
            const modalDate = document.getElementById('newsModalDate');
            const modalCategory = document.getElementById('newsModalCategory');
            const modalTitle = document.getElementById('newsModalTitle');
            const modalContent = document.getElementById('newsModalContent');
            const modal = document.getElementById('newsModal');

            if (!modalImage || !modalDate || !modalCategory || !modalTitle || !modalContent || !modal) {
                console.error('Modal elements not found');
                window.location.href = `/berita/${slug}`;
                return;
            }

            modalImage.src = imageUrl;
            modalDate.innerHTML = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                ${new Date(news.published_at || news.created_at).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric'
                })}
            `;
            modalCategory.textContent = news.category;
            modalTitle.textContent = news.title;
            modalContent.textContent = news.content;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    } catch (error) {
        console.error('Error opening news modal:', error);
        window.location.href = `/berita/${slug}`;
    }
}

/**
 * Close news modal
 */
function closeNewsModal() {
    const modal = document.getElementById('newsModal');
    if (modal) {
        modal.classList.remove('active');
    }
    document.body.style.overflow = '';
}

// Event listeners
document.addEventListener('DOMContentLoaded', () => {
    // Load per page preference
    const savedPerPage = localStorage.getItem('news_per_page') || '6';
    newsPerPage = parseInt(savedPerPage);

    const perPageSelect = document.getElementById('newsPerPageSelect');
    if (perPageSelect) perPageSelect.value = newsPerPage;

    // Populate homepage if grid exists
    if (document.getElementById('newsHomeGrid')) {
        populateNewsHome();
    }

    // Modal close handlers
    const modal = document.getElementById('newsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeNewsModal();
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeNewsModal();
    });
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        fetchNews,
        renderNewsCard,
        renderNewsCardFull,
        populateNewsHome,
        populateNewsFull,
        openNewsModalBySlug,
        closeNewsModal,
        changeNewsPage,
        changeNewsPerPage
    };
}
