/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Detail Surat — galeri foto dokumen & laporan kegiatan.
 * Data (foto dokumen, foto laporan, base URL storage) dibaca
 * dari atribut data-* pada #galleryOverlay yang diisi Blade.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    const overlay = document.getElementById('galleryOverlay');

    function readJsonAttr(el, attr) {
        if (!el) return [];
        try {
            return JSON.parse(el.getAttribute(attr) || '[]');
        } catch (e) {
            return [];
        }
    }

    const documentFoto = readJsonAttr(overlay, 'data-fotos');
    const reportFotosData = readJsonAttr(overlay, 'data-report-fotos');
    const storageUrl = overlay ? (overlay.getAttribute('data-storage') || '/storage') : '/storage';

    let currentGallery = {
        fotos: [],
        currentIndex: 0,
        isAnimating: false
    };

    function fullFotoUrl(fotoPath) {
        if (fotoPath && fotoPath.startsWith('http')) {
            return fotoPath;
        }
        return storageUrl + '/' + fotoPath;
    }

    function openGallery(index) {
        if (documentFoto.length === 0) return;
        currentGallery.fotos = documentFoto;
        currentGallery.currentIndex = index;
        showGalleryModal();
    }

    function openReportGallery(reportId, index) {
        if (!reportFotosData[reportId] || reportFotosData[reportId].length === 0) return;
        currentGallery.fotos = reportFotosData[reportId];
        currentGallery.currentIndex = index;
        showGalleryModal();
    }

    function showGalleryModal() {
        if (!overlay) return;
        updateGalleryImage('zoom-in');
        updateGalleryUI();
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeGallery(event) {
        if (!overlay) return;
        if (event && event.target !== overlay) return;
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.classList.remove('active');
            overlay.style.opacity = '';
        }, 300);
        document.body.style.overflow = '';
    }

    function navigateGallery(direction) {
        if (currentGallery.isAnimating || currentGallery.fotos.length <= 1) return;
        currentGallery.isAnimating = true;

        const animClass = direction > 0 ? 'slide-left' : 'slide-right';
        const nextIndex = (currentGallery.currentIndex + direction + currentGallery.fotos.length) % currentGallery.fotos.length;

        const img = document.getElementById('galleryImage');
        if (!img) return;
        img.style.opacity = '0';
        img.style.transform = direction > 0 ? 'translateX(-40px) scale(0.95)' : 'translateX(40px) scale(0.95)';

        setTimeout(() => {
            currentGallery.currentIndex = nextIndex;
            img.src = fullFotoUrl(currentGallery.fotos[currentGallery.currentIndex]);

            img.className = 'dl-gallery-image ' + animClass;

            setTimeout(() => {
                img.style.opacity = '1';
                img.style.transform = 'translateX(0) scale(1)';
            }, 50);

            updateGalleryUI();

            setTimeout(() => {
                currentGallery.isAnimating = false;
                img.className = 'dl-gallery-image';
            }, 400);
        }, 200);
    }

    function updateGalleryImage(animClass) {
        const img = document.getElementById('galleryImage');
        if (!img) return;
        img.src = fullFotoUrl(currentGallery.fotos[currentGallery.currentIndex]);
        img.className = 'dl-gallery-image ' + (animClass || 'fade-in');
        updateGalleryUI();
    }

    function updateGalleryUI() {
        const counter = document.getElementById('galleryCounter');
        const downloadBtn = document.getElementById('galleryDownload');
        const fotoPath = currentGallery.fotos[currentGallery.currentIndex];
        const fullUrl = fullFotoUrl(fotoPath);

        if (counter) {
            counter.textContent = (currentGallery.currentIndex + 1) + ' / ' + currentGallery.fotos.length;
        }
        if (downloadBtn) {
            downloadBtn.href = fullUrl;
        }

        const prevBtn = document.querySelector('.dl-gallery-nav.prev');
        const nextBtn = document.querySelector('.dl-gallery-nav.next');

        if (currentGallery.fotos.length <= 1) {
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
        } else {
            if (prevBtn) prevBtn.style.display = 'flex';
            if (nextBtn) nextBtn.style.display = 'flex';
        }

        // Tandai galeri berfoto tunggal; CSS mobile yang menyembunyikan penanda & thumbnail
        const wadahGaleri = document.querySelector('.dl-gallery-container');
        if (wadahGaleri) {
            wadahGaleri.classList.toggle('sd-lightbox-tunggal', currentGallery.fotos.length <= 1);
        }

        updateThumbnails();
    }

    function updateThumbnails() {
        const container = document.getElementById('galleryThumbnails');
        if (!container) return;
        container.innerHTML = '';

        currentGallery.fotos.forEach((foto, index) => {
            const thumb = document.createElement('div');
            thumb.className = 'dl-gallery-thumb' + (index === currentGallery.currentIndex ? ' active' : '');
            const img = document.createElement('img');
            img.src = fullFotoUrl(foto);
            img.alt = 'Thumb';
            thumb.appendChild(img);
            thumb.addEventListener('click', function () {
                if (index !== currentGallery.currentIndex) {
                    navigateGallery(index - currentGallery.currentIndex);
                }
            });
            container.appendChild(thumb);
        });
    }

    // ===============================
    // Event Delegation (menggantikan onclick inline)
    // ===============================
    document.addEventListener('click', function (event) {
        const item = event.target.closest('[data-report-id]');
        if (item) {
            openReportGallery(
                item.getAttribute('data-report-id'),
                parseInt(item.getAttribute('data-index') || '0', 10)
            );
            return;
        }
        if (!overlay || !overlay.classList.contains('active')) return;

        if (event.target.closest('.dl-gallery-close')) {
            closeGallery();
            return;
        }
        if (event.target.closest('.dl-gallery-container')) {
            event.stopPropagation();
            return;
        }
        if (event.target.closest('.dl-gallery-nav.prev')) {
            navigateGallery(-1);
            return;
        }
        if (event.target.closest('.dl-gallery-nav.next')) {
            navigateGallery(1);
            return;
        }
        if (event.target === overlay) {
            closeGallery(event);
        }
    });

    document.addEventListener('keydown', (e) => {
        if (!overlay || !overlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowLeft') navigateGallery(-1);
        if (e.key === 'ArrowRight') navigateGallery(1);
    });
})();

// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// ARSIPKAN SURAT — konfirmasi (menggantikan onsubmit inline)
// ============================================================
(function () {
    'use strict';
    const archiveForm = document.getElementById('archiveConfirmForm');
    if (!archiveForm) return;
    archiveForm.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Toast === 'undefined') {
            archiveForm.submit();
            return;
        }
        Toast.confirm(
            'Surat yang diarsipkan akan dipindahkan ke arsip dan tidak lagi muncul di daftar surat aktif.',
            { title: 'Arsipkan Surat?', confirmText: 'Ya, Arsipkan', cancelText: 'Batal', type: 'warning' }
        ).then(function (setuju) {
            if (setuju) archiveForm.submit();
        });
    });
})();
