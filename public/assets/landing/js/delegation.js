/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Delegation global landing — menggantikan onclick/onchange
 * inline di seluruh section SPA. Fungsi dipanggil tetap yang
 * sama (navigateTo, scrollCarousel, openModal, dll).
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    function call(fn, args) {
        if (typeof window[fn] === 'function') {
            window[fn].apply(null, args);
            return true;
        }
        return false;
    }

    // ===============================
    // KLIK
    // ===============================
    document.addEventListener('click', function (event) {
        const target = event.target;

        // Navigasi SPA
        const nav = target.closest('[data-nav]');
        if (nav) {
            event.preventDefault();
            call('navigateTo', [nav.getAttribute('data-nav')]);
            return;
        }

        // Carousel
        const carousel = target.closest('[data-carousel]');
        if (carousel) {
            call('scrollCarousel', [
                carousel.getAttribute('data-carousel'),
                parseInt(carousel.getAttribute('data-dir') || '1', 10)
            ]);
            return;
        }

        // Modal
        const openModalBtn = target.closest('[data-open-modal]');
        if (openModalBtn) {
            call('openModal', [openModalBtn.getAttribute('data-open-modal')]);
            return;
        }
        const closeModalBtn = target.closest('[data-close-modal]');
        if (closeModalBtn) {
            call('closeModal', [closeModalBtn.getAttribute('data-close-modal')]);
            return;
        }
        const outside = target.closest('[data-close-outside]');
        if (outside) {
            call('closeModalOutside', [event, outside.getAttribute('data-close-outside')]);
            return;
        }

        // Berita
        const newsPage = target.closest('[data-news-page]');
        if (newsPage) {
            call('changeNewsPage', [newsPage.getAttribute('data-news-page')]);
            return;
        }

        // SK & Dokumen
        const skPage = target.closest('[data-sk-page]');
        if (skPage) {
            call('changeSKPage', [skPage.getAttribute('data-sk-page')]);
            return;
        }
        if (target.closest('#btnShowAllDocs')) {
            call('clearSearchAndShowAll', []);
            return;
        }

        // Struktur
        const pokja = target.closest('[data-pokja]');
        if (pokja) {
            call('togglePokja', [pokja.getAttribute('data-pokja')]);
            return;
        }

        // Template
        const tplPage = target.closest('[data-template-page]');
        if (tplPage) {
            call('changeTemplatePage', [tplPage.getAttribute('data-template-page')]);
            return;
        }
        if (target.closest('#templateBtnShowAll')) {
            call('clearTemplateSearchAndShowAll', []);
            return;
        }
        if (target.closest('[data-close-template-preview]')) {
            call('closeTemplatePreview', []);
            return;
        }

        // Aksi umum
        const actionEl = target.closest('[data-action]');
        if (actionEl) {
            const action = actionEl.getAttribute('data-action');
            if (action === 'scroll-quick-access') {
                event.preventDefault();
                call('scrollToQuickAccess', []);
            } else if (action === 'toggle-floating') {
                call('toggleFloatingMenu', [event]);
            } else if (action === 'history-back') {
                window.history.back();
            } else if (action === 'reload-page') {
                window.location.reload();
            }
        }
    });

    // ===============================
    // PERUBAHAN (select)
    // ===============================
    document.addEventListener('change', function (event) {
        const target = event.target;
        if (!target) return;
        if (target.id === 'newsSortSelect') {
            call('changeNewsSort', [target.value]);
        } else if (target.id === 'newsPerPageSelect') {
            call('changeNewsPerPage', [target.value]);
        } else if (target.id === 'perPageSelect') {
            call('changePerPage', [target.value]);
        } else if (target.id === 'templatePerPageSelect') {
            call('changeTemplatePerPage', [target.value]);
        }
    });
})();
