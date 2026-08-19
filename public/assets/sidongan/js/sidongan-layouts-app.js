/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/sidongan/layouts/app.blade.php
 * Mengarahkan pengguna yang belum login ke halaman login.
 * URL login diambil dari atribut data-login-url pada <script>.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    var script = document.currentScript;
    var loginUrl = script ? script.getAttribute('data-login-url') : null;
    window.location.href = loginUrl || '/sidongan/login';
})();
