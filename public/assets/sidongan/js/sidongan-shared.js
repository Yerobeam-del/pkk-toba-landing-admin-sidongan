/**
 * SIDONGAN Shared JavaScript
 * Dikembangkan oleh Institut Teknologi Del
 * 
 * Fungsi-fungsi umum yang digunakan di semua halaman SIDONGAN.
 * Loaded otomatis via layout — tidak perlu include manual.
 */

document.addEventListener('DOMContentLoaded', function () {
    initFormLoading();
    initKeyboardShortcuts();
});

/* ==========================================
   FORM LOADING STATE
   Auto-detect semua form POST dan disable submit
   ========================================== */
function initFormLoading() {
    document.querySelectorAll('form').forEach(function (form) {
        var method = (form.querySelector('input[name="_method"]') || {}).value || form.method;
        if (!method || method.toUpperCase() === 'GET') return;
        if (form.hasAttribute('data-no-loading')) return;

        form.addEventListener('submit', function () {
            var btns = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            btns.forEach(function (btn) {
                btn.disabled = true;
                btn.setAttribute('data-original-html', btn.innerHTML);
                btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right:0.5rem;"></i> Menyimpan...';
                btn.style.opacity = '0.7';
                btn.style.pointerEvents = 'none';
            });
        });
    });
}

/* ==========================================
   KEYBOARD SHORTCUTS
   ========================================== */
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function (e) {
        // Ctrl+K → Focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            var searchInput = document.querySelector('input[name="search"], #searchInput');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }

        // Escape → Close modal / notification popup
        if (e.key === 'Escape') {
            // Close notification popup
            var popup = document.getElementById('notificationPopup');
            if (popup && popup.classList.contains('show')) {
                popup.classList.remove('show');
                return;
            }
            // Close user menu
            var userMenu = document.querySelector('.user-menu.show');
            if (userMenu) {
                userMenu.classList.remove('show');
                return;
            }
            // Close any open modal
            var modal = document.querySelector('.modal-overlay.show, .crop-modal-overlay.show');
            if (modal) {
                modal.classList.remove('show');
            }
        }
    });
}
