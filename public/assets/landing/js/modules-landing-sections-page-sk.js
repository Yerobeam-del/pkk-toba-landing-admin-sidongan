/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/page-sk.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// ==========================================
// INISIALISASI PER PAGE & SEARCH
// ==========================================
let searchTimeout;

document.addEventListener('DOMContentLoaded', function() {
    // Load per_page dari localStorage
    const savedPerPage = localStorage.getItem('sk_per_page') || '5';
    currentPerPage = parseInt(savedPerPage);

    const perPageSelect = document.getElementById('perPageSelect');
    if (perPageSelect) {
        perPageSelect.value = currentPerPage;
    }
});

// Change per page
function changePerPage(value) {
    currentPerPage = parseInt(value);
    localStorage.setItem('sk_per_page', currentPerPage);
    currentSearchTerm = '';
    originalSearchTerm = ''; // ← Reset juga (variable dari navigation.js)

    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';

    if (typeof loadSKDocuments === 'function') {
        loadSKDocuments(1);
    }
}

// Search functionality dengan debounce
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function(e) {
        const term = e.target.value; // ← Preserve original case
        originalSearchTerm = term;   // ← Gunakan variable dari navigation.js

        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            currentSearchTerm = term.toLowerCase().trim(); // ← Untuk API (case-insensitive)
            if (typeof loadSKDocuments === 'function') {
                loadSKDocuments(1);
            }
        }, 300);
    });
}


