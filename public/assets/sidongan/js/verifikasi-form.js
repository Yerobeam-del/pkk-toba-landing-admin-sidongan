/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Form Verifikasi Laporan — kartu status & galeri foto.
 * Data (daftar foto & base URL storage) dibaca dari atribut
 * data-* pada #galleryOverlay yang diisi oleh Blade.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
(function () {
    'use strict';

    const overlay = document.getElementById('galleryOverlay');

    function readFotos() {
        if (!overlay) return [];
        try {
            return JSON.parse(overlay.getAttribute('data-fotos') || '[]');
        } catch (e) {
            return [];
        }
    }

    const galleryFotos = readFotos();
    const storageUrl = overlay ? (overlay.getAttribute('data-storage') || '/storage') : '/storage';

    let currentIndex = 0;
    let isAnimating = false;

    // ===============================
    // Logic untuk Kartu Status (Warna)
    // ===============================
    function selectOption(value) {
        const approve = document.getElementById('option-approve');
        const reject = document.getElementById('option-reject');
        if (approve) approve.classList.remove('selected-approve');
        if (reject) reject.classList.remove('selected-reject');
        if (value === 'disetujui') {
            if (approve) approve.classList.add('selected-approve');
        } else {
            if (reject) reject.classList.add('selected-reject');
        }
    }

    // ===============================
    // Logic untuk Gallery Popup
    // ===============================
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
        img.style.transform = direction > 0 ? 'translateX(-40px) scale(0.95)' : 'translateX(40px) scale(0.95)';

        setTimeout(() => {
            currentIndex = nextIndex;
            img.src = storageUrl + '/' + galleryFotos[currentIndex];
            img.className = 'gallery-image ' + animClass;

            setTimeout(() => {
                img.style.opacity = '1';
                img.style.transform = 'translateX(0) scale(1)';
            }, 50);

            updateGalleryUI();
            setTimeout(() => { isAnimating = false; img.className = 'gallery-image'; }, 400);
        }, 200);
    }

    function updateGalleryImage(animClass) {
        animClass = animClass || 'fade-in';
        const img = document.getElementById('galleryImage');
        if (!img) return;
        img.src = storageUrl + '/' + galleryFotos[currentIndex];
        img.className = 'gallery-image ' + animClass;
        updateGalleryUI();
    }

    function updateGalleryUI() {
        const counter = document.getElementById('galleryCounter');
        const download = document.getElementById('galleryDownload');
        if (counter) counter.textContent = (currentIndex + 1) + ' / ' + galleryFotos.length;
        if (download) download.href = storageUrl + '/' + galleryFotos[currentIndex];

        const prevBtn = document.querySelector('.gallery-nav.prev');
        const nextBtn = document.querySelector('.gallery-nav.next');
        if (galleryFotos.length <= 1) {
            if (prevBtn) prevBtn.style.display = 'none';
            if (nextBtn) nextBtn.style.display = 'none';
        } else {
            if (prevBtn) prevBtn.style.display = 'flex';
            if (nextBtn) nextBtn.style.display = 'flex';
        }

        const container = document.getElementById('galleryThumbnails');
        if (!container) return;
        container.innerHTML = '';
        galleryFotos.forEach((foto, index) => {
            const thumb = document.createElement('div');
            thumb.style.cssText = 'width: 40px; height: 40px; border-radius: 0.25rem; overflow: hidden; border: 2px solid ' +
                (index === currentIndex ? '#fff' : 'rgba(255,255,255,0.3)') +
                '; cursor: pointer; opacity: ' + (index === currentIndex ? '1' : '0.5') + '; flex-shrink: 0;';
            const img = document.createElement('img');
            img.src = storageUrl + '/' + foto;
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.alt = 'Dokumentasi ' + (index + 1);
            thumb.appendChild(img);
            thumb.addEventListener('click', function () {
                if (index !== currentIndex) navigateGallery(index - currentIndex);
            });
            container.appendChild(thumb);
        });
    }

    // ===============================
    // Event Delegation (menggantikan onclick inline)
    // ===============================
    document.addEventListener('click', function (event) {
        const thumb = event.target.closest('.thumb-item');
        if (thumb && overlay) {
            openGallery(parseInt(thumb.getAttribute('data-index') || '0', 10));
            return;
        }
        if (event.target.closest('#option-approve')) {
            selectOption('disetujui');
            return;
        }
        if (event.target.closest('#option-reject')) {
            selectOption('ditolak');
            return;
        }
        if (!overlay || !overlay.classList.contains('active')) return;

        if (event.target.closest('.gallery-close')) {
            closeGallery();
            return;
        }
        if (event.target.closest('.gallery-container')) {
            event.stopPropagation();
            return;
        }
        if (event.target.closest('.gallery-nav.prev')) {
            navigateGallery(-1);
            return;
        }
        if (event.target.closest('.gallery-nav.next')) {
            navigateGallery(1);
            return;
        }
        if (event.target === overlay) {
            closeGallery(event);
        }
    });

    document.addEventListener('keydown', function (e) {
        if (!overlay || !overlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeGallery();
        if (e.key === 'ArrowLeft') navigateGallery(-1);
        if (e.key === 'ArrowRight') navigateGallery(1);
    });
})();
