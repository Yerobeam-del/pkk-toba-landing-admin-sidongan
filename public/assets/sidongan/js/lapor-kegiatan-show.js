/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Detail Laporan Kegiatan — galeri foto dokumentasi.
 * Data (daftar foto & base URL storage) dibaca dari atribut
 * data-* pada #galleryOverlay yang diisi oleh Blade.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    const overlay = document.getElementById('galleryOverlay');

    let galleryFotos = [];
    if (overlay) {
        try {
            galleryFotos = JSON.parse(overlay.getAttribute('data-fotos') || '[]');
        } catch (e) {
            galleryFotos = [];
        }
    }
    const storageUrl = overlay ? (overlay.getAttribute('data-storage') || '/storage') : '/storage';

    let currentIndex = 0;
    let isAnimating = false;

    function openGallery(index) {
        currentIndex = index;
        updateGalleryImage('zoom-in');
        updateGalleryUI();
        if (overlay) overlay.classList.add('active');
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
        if (isAnimating || galleryFotos.length <= 1) return;
        isAnimating = true;

        const animClass = direction > 0 ? 'slide-left' : 'slide-right';
        const nextIndex = (currentIndex + direction + galleryFotos.length) % galleryFotos.length;

        const img = document.getElementById('galleryImage');
        if (!img) return;
        img.style.opacity = '0';

        setTimeout(() => {
            currentIndex = nextIndex;
            img.src = storageUrl + '/' + galleryFotos[currentIndex];
            img.className = 'dl-gallery-image ' + animClass;

            setTimeout(() => {
                img.style.opacity = '1';
            }, 50);

            updateGalleryUI();

            setTimeout(() => {
                isAnimating = false;
                img.className = 'dl-gallery-image';
            }, 400);
        }, 200);
    }

    function updateGalleryImage(animClass) {
        animClass = animClass || 'zoom-in';
        const img = document.getElementById('galleryImage');
        if (!img) return;
        img.src = storageUrl + '/' + galleryFotos[currentIndex];
        img.className = 'dl-gallery-image ' + animClass;
        updateGalleryUI();
    }

    function updateGalleryUI() {
        const counter = document.getElementById('galleryCounter');
        const download = document.getElementById('galleryDownload');
        if (counter) counter.textContent = (currentIndex + 1) + ' / ' + galleryFotos.length;
        if (download) download.href = storageUrl + '/' + galleryFotos[currentIndex];

        const prevBtn = document.querySelector('.dl-gallery-nav.prev');
        const nextBtn = document.querySelector('.dl-gallery-nav.next');

        if (galleryFotos.length <= 1) {
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
        } else {
            if (prevBtn) prevBtn.style.display = 'flex';
            if (nextBtn) nextBtn.style.display = 'flex';
        }

        updateThumbnails();
    }

    function updateThumbnails() {
        const container = document.getElementById('galleryThumbnails');
        if (!container) return;
        container.innerHTML = '';
        galleryFotos.forEach((foto, index) => {
            const thumb = document.createElement('div');
            thumb.className = 'dl-gallery-thumb' + (index === currentIndex ? ' active' : '');
            const img = document.createElement('img');
            img.src = storageUrl + '/' + foto;
            img.alt = 'Thumb';
            thumb.appendChild(img);
            thumb.addEventListener('click', function () {
                if (index !== currentIndex) {
                    navigateGallery(index - currentIndex);
                }
            });
            container.appendChild(thumb);
        });
    }

    // ===============================
    // Event Delegation (menggantikan onclick inline)
    // ===============================
    document.addEventListener('click', function (event) {
        const item = event.target.closest('.dl-foto-item[data-index]');
        if (item && overlay) {
            openGallery(parseInt(item.getAttribute('data-index') || '0', 10));
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
