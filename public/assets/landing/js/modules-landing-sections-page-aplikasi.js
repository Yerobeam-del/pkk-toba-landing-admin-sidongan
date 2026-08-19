/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/page-aplikasi.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


document.addEventListener('DOMContentLoaded', function() {
    let appsData = { aplikasi: [], layanan: [] };
    let autoScrollIntervals = {};

    // 1. Fetch Data dari API
    fetch('/api/v1/applications')
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                appsData.aplikasi = [
                    ...(result.data.aplikasi.active || []),
                    ...(result.data.aplikasi.maintenance || []),
                    ...(result.data.aplikasi.development || [])
                ];

                appsData.layanan = [
                    ...(result.data.layanan.active || []),
                    ...(result.data.layanan.maintenance || []),
                    ...(result.data.layanan.development || [])
                ];

                renderSection('aplikasi', appsData.aplikasi);
                renderSection('layanan', appsData.layanan);

                startAutoScroll('track-aplikasi');
                startAutoScroll('track-layanan');
            }
        })
        .catch(err => console.error('Error loading apps:', err));

    // 2. Fungsi Render (DIPERBAIKI)
    function renderSection(type, data) {
        const track = document.getElementById(`track-${type}`);
        const grid = document.getElementById(`grid-${type}`);
        const sectionBlock = document.getElementById(`block-${type}`); // Ambil elemen block section
        const isLayanan = type === 'layanan';

        if (data.length === 0) {
            // Jika data kosong, sembunyikan seluruh section block (Aplikasi atau Layanan)
            if (sectionBlock) {
                sectionBlock.style.display = 'none';
            }
            return;
        }

        // Jika ada data, pastikan section block ditampilkan (untuk jaga-jaga jika sebelumnya di-hide)
        if (sectionBlock) {
            sectionBlock.style.display = 'block';
        }

        // Render Carousel
        track.innerHTML = data.map(app => createCardHTML(app, isLayanan)).join('');

        // Render Modal Grid
        grid.innerHTML = data.map(app => createModalCardHTML(app, isLayanan)).join('');
    }

    const DEFAULT_COLOR = '#0f6b63'; // Hijau PKK, dipakai bila admin belum memilih warna

    // Rumus turunan warna
    function mixWhite(hex, ratio) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        const m = (v) => Math.round(v + (255 - v) * ratio);
        return `rgb(${m(r)}, ${m(g)}, ${m(b)})`;
    }

    function colorVars(app) {
        const c = /^#[0-9a-fA-F]{6}$/.test(app.color || '') ? app.color : DEFAULT_COLOR;
        return [
            `--app-c:${c}`,
            `--app-c-header:linear-gradient(135deg, ${mixWhite(c, 0.88)}, ${mixWhite(c, 0.96)})`,
            `--app-c-icon:linear-gradient(135deg, ${c}, ${mixWhite(c, 0.28)})`,
            `--app-c-circle:${mixWhite(c, 0.7)}`
        ].join(';');
    }

    const STATUS_META = {
        maintenance: {
            label: 'Dalam Maintenance',
            note: 'Sedang Diperbaiki',
            icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>`
        },
        development: {
            label: 'Dalam Pengembangan',
            note: 'Segera Hadir',
            icon: `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3m0 12v3M5.6 5.6l2.1 2.1m8.6 8.6 2.1 2.1M3 12h3m12 0h3M5.6 18.4l2.1-2.1m8.6-8.6 2.1-2.1"/></svg>`
        }
    };

    function createCardHTML(app, isLayanan) {
        const iconHtml = app.icon
            ? `<img src="/storage/${app.icon}" alt="${app.short_name}">`
            : `<span>${app.short_name.substring(0, 2)}</span>`;

        const bgClass = isLayanan ? 'layanan-bg' : '';
        const iconClass = isLayanan ? 'lay-icon' : '';
        const nameClass = isLayanan ? 'lay-name' : '';
        const linkClass = isLayanan ? 'lay-link' : '';

        const status = app.status || 'active';
        const meta = STATUS_META[status];
        const stateClass = meta ? `is-${status}` : '';

        const featuresHtml = app.features && app.features.length > 0
            ? `<ul class="app-features">${app.features.slice(0, 4).map(f => `
                <li>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    ${f}
                </li>
            `).join('')}</ul>`
            : '';

        const badgeHtml = meta
            ? `<span class="slide-status-badge status-${status}">${meta.icon}${meta.label}</span>`
            : '';

        const actionHtml = meta
            ? `<span class="slide-link is-disabled" aria-disabled="true">${meta.icon}${meta.note}</span>`
            : `<a href="${app.url && app.url !== '#' ? app.url : '#'}" target="_blank" class="slide-link ${linkClass}">Akses ${app.short_name}</a>`;

        return `
        <div class="app-card-slide has-app-color ${stateClass}" style="${colorVars(app)}" data-name="${app.name.toLowerCase()}" data-short="${app.short_name.toLowerCase()}" data-status="${status}">
            <div class="slide-header ${bgClass}">
                ${badgeHtml}
                <div class="slide-icon ${iconClass}">${iconHtml}</div>
            </div>
            <div class="slide-body">
                <h3 class="slide-name ${nameClass}">${app.short_name}</h3>
                <p class="slide-fullname">${app.name}</p>
                <p class="slide-desc">${app.description || 'Tidak ada deskripsi.'}</p>
                ${featuresHtml}
                ${actionHtml}
            </div>
        </div>`;
    }

    function createModalCardHTML(app, isLayanan) {
        const iconHtml = app.icon
            ? `<img src="/storage/${app.icon}" alt="${app.short_name}">`
            : `<span>${app.short_name.substring(0, 2)}</span>`;
        const iconClass = isLayanan ? 'lay-icon' : '';
        const cardClass = isLayanan ? 'lay-card' : '';
        const styleVars = colorVars(app);

        const status = app.status || 'active';
        const meta = STATUS_META[status];

        const inner = `
            <div class="modal-icon ${iconClass}">${iconHtml}</div>
            <div class="modal-info">
                <h4>${app.short_name}</h4>
                <p>${app.name}</p>
                ${meta ? `<span class="modal-status status-${status}">${meta.icon}${meta.note}</span>` : ''}
            </div>`;

        return meta
            ? `<div class="modal-card has-app-color ${cardClass} is-${status}" style="${styleVars}" aria-disabled="true">${inner}</div>`
            : `<a href="${app.url && app.url !== '#' ? app.url : '#'}" target="_blank" class="modal-card has-app-color ${cardClass}" style="${styleVars}">${inner}</a>`;
    }

    // 3. Carousel Logic
    window.scrollCarousel = function(trackId, direction) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const card = track.querySelector('.app-card-slide');
        if (!card) return;

        const scrollAmount = card.offsetWidth + 24;
        resetAutoScroll(trackId);
        track.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
    };

    function startAutoScroll(trackId) {
        const track = document.getElementById(trackId);
        if (!track) return;

        const cards = track.querySelectorAll('.app-card-slide');
        if (cards.length <= 1) return;

        if (autoScrollIntervals[trackId]) {
            clearInterval(autoScrollIntervals[trackId]);
        }

        autoScrollIntervals[trackId] = setInterval(() => {
            const card = track.querySelector('.app-card-slide');
            if (!card) return;

            const scrollAmount = card.offsetWidth + 24;
            const maxScroll = track.scrollWidth - track.clientWidth;

            if (track.scrollLeft >= maxScroll - 10) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
        }, 4000);
    }

    function resetAutoScroll(trackId) {
        if (autoScrollIntervals[trackId]) {
            clearInterval(autoScrollIntervals[trackId]);
        }
        setTimeout(() => {
            startAutoScroll(trackId);
        }, 5000);
    }

    ['track-aplikasi', 'track-layanan'].forEach(trackId => {
        const track = document.getElementById(trackId);
        if (!track) return;

        track.addEventListener('mouseenter', () => {
            if (autoScrollIntervals[trackId]) {
                clearInterval(autoScrollIntervals[trackId]);
            }
        });

        track.addEventListener('mouseleave', () => {
            startAutoScroll(trackId);
        });
    });

    // 4. Search Logic
    document.getElementById('globalSearch').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        const allCards = document.querySelectorAll('.app-card-slide');

        allCards.forEach(card => {
            const name = card.getAttribute('data-name');
            const short = card.getAttribute('data-short');
            if (name.includes(term) || short.includes(term)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    });

    // 5. Modal Logic
    window.openModal = function(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeModal = function(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = '';
    };

    window.closeModalOutside = function(event, modalId) {
        if (event.target.id === modalId) {
            window.closeModal(modalId);
        }
    };
});


