/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Layout Admin Panel — dipisah dari HTML (admin/layouts/app.blade.php)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

document.addEventListener('DOMContentLoaded', () => {
    const layout = document.getElementById('adminLayout');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const navItems = document.querySelectorAll('.nav-item');

    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    const isMobile = window.innerWidth <= 1024;

    if (!isMobile && isCollapsed) {
        layout.classList.add('collapsed');
    }

    toggleBtn.addEventListener('click', () => {
        if (window.innerWidth <= 1024) {
            layout.classList.toggle('mobile-open');
        } else {
            layout.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', layout.classList.contains('collapsed'));
        }
    });

    navItems.forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                layout.classList.remove('mobile-open');
            }
        });
    });

    overlay.addEventListener('click', () => {
        layout.classList.remove('mobile-open');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024) {
            layout.classList.remove('mobile-open');
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                layout.classList.add('collapsed');
            } else {
                layout.classList.remove('collapsed');
            }
        } else {
            layout.classList.remove('collapsed');
        }
    });

    // ===== Tooltip nama menu saat sidebar collapsed (desktop) =====
    // Pakai elemen position:fixed agar tidak terpotong overflow sidebar
    const tipEl = document.createElement('div');
    tipEl.id = 'sidebarTooltip';
    tipEl.style.cssText = 'position:fixed;z-index:3000;background:#1e293b;color:#e2e8f0;font-size:0.75rem;font-weight:600;padding:0.4rem 0.75rem;border-radius:6px;border:1px solid rgba(255,255,255,0.1);box-shadow:0 6px 16px rgba(0,0,0,0.35);pointer-events:none;opacity:0;transition:opacity 0.15s;white-space:nowrap;display:none';
    document.body.appendChild(tipEl);

    document.querySelectorAll('.nav-item[data-tip]').forEach(item => {
        item.addEventListener('mouseenter', () => {
            if (!layout.classList.contains('collapsed') || window.innerWidth <= 1024) return;
            const r = item.getBoundingClientRect();
            tipEl.textContent = item.dataset.tip;
            tipEl.style.display = 'block';
            tipEl.style.left = (r.right + 12) + 'px';
            tipEl.style.top = (r.top + r.height / 2) + 'px';
            tipEl.style.transform = 'translateY(-50%)';
            requestAnimationFrame(() => { tipEl.style.opacity = '1'; });
        });
        item.addEventListener('mouseleave', () => {
            tipEl.style.opacity = '0';
            tipEl.style.display = 'none';
        });
    });
});

/* ============================================================
 * User Profile Dropdown — event binding terpusat (tanpa onclick)
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    function closeUserMenu() {
        const menu = document.getElementById('userMenu');
        const arrow = document.getElementById('userMenuArrow');
        if (!menu) return;
        menu.classList.remove('open');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }

    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        const arrow = document.getElementById('userMenuArrow');
        if (!menu) return;
        if (menu.classList.contains('open')) {
            closeUserMenu();
        } else {
            menu.classList.add('open');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
    }

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.user-profile-btn');
        const menu = document.getElementById('userMenu');
        if (btn) {
            toggleUserMenu();
            return;
        }
        if (menu && menu.classList.contains('open') && !e.target.closest('#userMenu')) {
            closeUserMenu();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeUserMenu();
        }
    });
})();

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION GLOBAL ADMIN (menggantikan onclick/onsubmit inline)
// ============================================================
(function () {
    'use strict';

    // Modal
    function closeModal() {
        const overlay = document.getElementById('modalOverlay');
        if (overlay) overlay.classList.remove('open');
    }

    // Hapus Aplikasi (data-delete-app-id)
    function confirmDeleteApp(id, name) {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            Toast.confirm(
                'Data <strong>"' + name + '"</strong> akan dihapus secara permanen.',
                { title: 'Hapus Data?', confirmText: 'Ya, Hapus', cancelText: 'Batal', type: 'danger' }
            ).then(function (confirmed) {
                if (confirmed) {
                    const form = document.getElementById('delete-app-' + id);
                    if (form) form.submit();
                }
            });
        }
    }

    document.addEventListener('click', function (event) {
        const target = event.target;
        const closeBtn = target.closest('[data-action="close-modal"]');
        if (closeBtn) {
            closeModal();
            return;
        }
        const reloadBtn = target.closest('[data-action="reload-page"]');
        if (reloadBtn) {
            window.location.reload();
            return;
        }
        const delApp = target.closest('[data-delete-app-id]');
        if (delApp) {
            confirmDeleteApp(delApp.getAttribute('data-delete-app-id'), delApp.getAttribute('data-delete-app-name') || '');
        }
    });

    // Hapus Laporan Kegiatan (sidongan-data/show)
    const deleteReportForm = document.getElementById('deleteReportForm');
    if (deleteReportForm) {
        deleteReportForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (typeof Toast === 'undefined' || typeof Toast.confirm !== 'function') {
                deleteReportForm.submit();
                return;
            }
            Toast.confirm(
                'Laporan kegiatan ini akan dihapus permanen.',
                { title: 'Hapus Laporan?', confirmText: 'Ya, Hapus', cancelText: 'Batal', type: 'danger' }
            ).then(function (setuju) {
                if (setuju) deleteReportForm.submit();
            });
        });
    }

    // Hapus permanen SIEDA (sieda-data/show)
    const forceDeleteForm = document.getElementById('forceDeleteForm');
    if (forceDeleteForm) {
        forceDeleteForm.addEventListener('submit', function (e) {
            if (!window.confirm('HAPUS PERMANEN: Data akan hilang dari database SIEDA dan TIDAK bisa dikembalikan. Lanjutkan?')) {
                e.preventDefault();
            }
        });
    }
})();
