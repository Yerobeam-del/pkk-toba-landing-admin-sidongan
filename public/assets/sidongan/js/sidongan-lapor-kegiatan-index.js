/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/sidongan/lapor-kegiatan/index.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    searchInput?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// AUTO-SUBMIT FILTER (menggantikan onchange inline)
// ============================================================
const filterFormLk = document.getElementById('filterForm');
if (filterFormLk) {
    filterFormLk.addEventListener('change', function (event) {
        const name = event.target && event.target.name;
        if (name && ['status', 'per_page'].indexOf(name) !== -1) {
            filterFormLk.submit();
        }
    });
}
