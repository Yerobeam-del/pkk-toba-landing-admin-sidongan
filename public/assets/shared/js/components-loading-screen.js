/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/components/loading-screen.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


    // Fungsi global untuk menampilkan loading screen
    window.showLoading = function() {
        const loader = document.getElementById('global-loading-screen');
        if (loader) {
            loader.classList.remove('hidden');
        }
    };

    // Fungsi global untuk menyembunyikan loading screen
    window.hideLoading = function() {
        const loader = document.getElementById('global-loading-screen');
        if (loader) {
            loader.classList.add('hidden');
        }
    };

    // Otomatis sembunyikan loading saat halaman selesai dimuat
    window.addEventListener('load', function() {
        // Delay 500ms agar transisi terlihat halus
        setTimeout(function() {
            window.hideLoading();
        }, 500);
    });

    // Fallback: sembunyikan loading maksimal setelah 5 detik
    setTimeout(function() {
        window.hideLoading();
    }, 5000);


