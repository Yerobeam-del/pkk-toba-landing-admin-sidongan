/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/page-struktur.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


console.log('Struktur script loaded (Static Layout Mode)');
let strukturDataLoaded = false;

function forceRedrawConnectors() {
    window.dispatchEvent(new Event('resize'));
    if (typeof drawTreeConnectors === 'function') {
        setTimeout(() => drawTreeConnectors(), 100);
    }
}

async function loadStrukturData() {
    if (strukturDataLoaded) return;
    console.log('Fetching struktur data...');

    try {
        const response = await fetch('/api/v1/struktur');
        if (!response.ok) throw new Error('HTTP ' + response.status);

        const result = await response.json();
        if (!result.success) throw new Error(result.message);

        const { pengurus_inti, pokja } = result.data;
        populateStrukturDOM(pengurus_inti || [], pokja || []);

        setTimeout(() => {
            forceRedrawConnectors();
        }, 150);

        strukturDataLoaded = true;
        console.log('Struktur populated successfully');
    } catch (error) {
        console.error('Failed to load struktur:', error);
    }
}

function populateStrukturDOM(pengurus, pokjaList) {
    const setCard = (nameId, imgId, placeholderId, data) => {
        const nameEl = document.getElementById(nameId);
        const imgEl = document.getElementById(imgId);
        const phEl = document.getElementById(placeholderId);

        // Jika ada data dan nama
        if (data && data.name) {
            nameEl.textContent = data.name;

            if (data.photo) {
                // Ada foto → tampilkan foto, sembunyikan placeholder
                imgEl.src = data.photo;
                imgEl.style.display = 'block';
                phEl.style.display = 'none';

                // Jika foto gagal load, tampilkan placeholder
                imgEl.onerror = function() {
                    this.style.display = 'none';
                    phEl.style.display = 'flex';
                };
            } else {
                // Tidak ada foto → tampilkan placeholder
                imgEl.style.display = 'none';
                phEl.style.display = 'flex';
            }
        } else {
            // Tidak ada data sama sekali → tampilkan placeholder default
            if (imgEl) imgEl.style.display = 'none';
            if (phEl) phEl.style.display = 'flex';
        }
    };

    const findPos = (arr, pos) => arr.find(p => p.position === pos);

    // Pengurus Inti (Row 1 & 2)
    setCard('name-ketua-pembina', 'img-ketua-pembina', 'placeholder-ketua-pembina', findPos(pengurus, 'Ketua Pembina'));
    setCard('name-ketua-pkk', 'img-ketua-pkk', 'placeholder-ketua-pkk', findPos(pengurus, 'Ketua TP PKK'));
    setCard('name-sekretaris', 'img-sekretaris', 'placeholder-sekretaris', findPos(pengurus, 'Sekretaris'));
    setCard('name-bendahara', 'img-bendahara', 'placeholder-bendahara', findPos(pengurus, 'Bendahara'));

    // Staf Ahli 1 & 2
    const stafAhli1 = pengurus.find(p => p.position === 'Staf Ahli 1');
    const stafAhli2 = pengurus.find(p => p.position === 'Staf Ahli 2');
    setCard('name-staf-1', 'img-staf-1', 'placeholder-staf-1', stafAhli1 || null);
    setCard('name-staf-2', 'img-staf-2', 'placeholder-staf-2', stafAhli2 || null);

    // Ketua I, II, III, IV
    setCard('name-pokja-1', 'img-pokja-1', 'placeholder-pokja-1', findPos(pengurus, 'Ketua I'));
    setCard('name-pokja-2', 'img-pokja-2', 'placeholder-pokja-2', findPos(pengurus, 'Ketua II'));
    setCard('name-pokja-3', 'img-pokja-3', 'placeholder-pokja-3', findPos(pengurus, 'Ketua III'));
    setCard('name-pokja-4', 'img-pokja-4', 'placeholder-pokja-4', findPos(pengurus, 'Ketua IV'));

    // Anggota Pokja Sections
    const pokjaIds = ['pokja1', 'pokja2', 'pokja3', 'pokja4'];
    const positionHierarchy = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Sekretaris Pokja', 'Anggota'];

    pokjaIds.forEach((id, idx) => {
        const container = document.getElementById('members-' + id);
        if (!container) return;

        const members = pokjaList[idx]?.members || [];

        members.sort((a, b) => {
            const orderA = positionHierarchy.indexOf(a.position);
            const orderB = positionHierarchy.indexOf(b.position);
            return (orderA === -1 ? 99 : orderA) - (orderB === -1 ? 99 : orderB);
        });

        if (members.length === 0) return;

        container.innerHTML = members.map(m => {
            const pos = m.position.toLowerCase();
            const isStruktural = pos.includes('ketua') || pos.includes('wakil') || pos.includes('sekretaris');
            const roleClass = isStruktural ? (pos.includes('ketua') ? 'pos-ketua' : (pos.includes('wakil') ? 'pos-wakil' : 'pos-sekretaris')) : 'pos-anggota';

            const initials = m.name ? m.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '';

            let avatarContent = '';
            if (m.photo) {
                avatarContent = '<img class="u-cover" src="' + m.photo + '" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">';
                avatarContent += '<div class="avatar-placeholder" style="display:none; width:100%;height:100%; background:linear-gradient(135deg,#cbd5e1,#94a3b8); display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">' + initials + '</div>';
            } else {
                avatarContent = '<div class="avatar-placeholder" style="width:100%;height:100%; background:linear-gradient(135deg,#cbd5e1,#94a3b8); display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;">' + (initials || '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>') + '</div>';
            }

            return '<div class="member-card ' + roleClass + '">' +
                '<div class="org-avatar">' + avatarContent + '</div>' +
                '<div class="org-position">' + m.position + '</div>' +
                '<div class="org-name">' + m.name + '</div>' +
            '</div>';
        }).join('');
    });
}

function togglePokja(pokjaId) {
    const content = document.getElementById(pokjaId);
    const icon = document.getElementById('icon-' + pokjaId);
    if (content && icon) {
        const isHidden = content.style.display === 'none' || !content.style.display;
        content.style.display = isHidden ? 'block' : 'none';
        icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';

        // Redraw garis penghubung setelah toggle
        setTimeout(() => {
            if (typeof drawMobileConnectors === 'function') {
                drawMobileConnectors();
            }
            window.dispatchEvent(new Event('resize'));
        }, 400);
    }
}

// Auto-load when struktur tab becomes active
document.addEventListener('DOMContentLoaded', () => {
    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-struktur');
        if (page && page.classList.contains('active') && !strukturDataLoaded) {
            setTimeout(() => loadStrukturData(), 100);
            observer.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
});

// ==========================================
// DRAW MOBILE TREE CONNECTORS - FIXED
// ==========================================
function drawMobileConnectors() {
    // 1. Hapus SVG lama agar tidak menumpuk (double lines)
    const oldSvg = document.getElementById('mobileTreeSvg');
    if (oldSvg) oldSvg.remove();

    // Hanya jalankan di mobile
    if (window.innerWidth > 768) return;

    const wrapper = document.querySelector('.struktur-wrapper');
    if (!wrapper) return;

    // 2. Hitung tinggi HANYA untuk struktur utama (TANPA Pokja sections)
    const strukturTree = document.querySelector('.struktur-tree');
    const pokjaSections = document.querySelector('.pokja-sections-wrapper');

    let wrapperHeight = wrapper.scrollHeight;

    // Jika ada Pokja sections, kurangi tingginya dari perhitungan
    if (pokjaSections) {
        const strukturHeight = strukturTree ? strukturTree.scrollHeight : 0;
        const gapHeight = 50; // Gap antara struktur dan Pokja
        wrapperHeight = strukturHeight + gapHeight;
    }

    const wrapperWidth = wrapper.offsetWidth;

    // 3. Buat SVG baru
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.id = 'mobileTreeSvg';
    svg.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:' + wrapperHeight + 'px;pointer-events:none;z-index:1;overflow:visible;';
    svg.setAttribute('viewBox', '0 0 ' + wrapperWidth + ' ' + wrapperHeight);

    wrapper.style.position = 'relative';
    wrapper.insertBefore(svg, wrapper.firstChild);

    // 4. Helper: Dapatkan koordinat VISUAL CARD, bukan wrapper
    function getCardPos(wrapperId) {
        const wrapperEl = document.getElementById(wrapperId);
        if (!wrapperEl) return null;

        // Cari elemen kartu visual di dalam wrapper
        // Prioritas: .org-card (umum), atau .staff-combined-card (khusus staf ahli)
        const cardEl = wrapperEl.querySelector('.staff-combined-card') ||
                       wrapperEl.querySelector('.org-card') ||
                       wrapperEl;

        const r = cardEl.getBoundingClientRect();
        const wrapperRect = wrapper.getBoundingClientRect();

        return {
            cx: r.left + r.width / 2 - wrapperRect.left,
            top: r.top - wrapperRect.top,
            bottom: r.bottom - wrapperRect.top,
            left: r.left - wrapperRect.left,
            right: r.right - wrapperRect.left,
            width: r.width,
            height: r.height
        };
    }

    // Helper gambar garis
    function drawLine(x1, y1, x2, y2, color = '#0f6b63', width = 2.5) {
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', x1);
        line.setAttribute('y1', y1);
        line.setAttribute('x2', x2);
        line.setAttribute('y2', y2);
        line.setAttribute('stroke', color);
        line.setAttribute('stroke-width', width);
        line.setAttribute('stroke-linecap', 'round');
        svg.appendChild(line);
    }

    // Helper gambar titik persimpangan
    function drawCircle(cx, cy, r = 4, color = '#0f6b63') {
        const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('cx', cx);
        circle.setAttribute('cy', cy);
        circle.setAttribute('r', r);
        circle.setAttribute('fill', color);
        svg.appendChild(circle);
    }

    // 5. Ambil posisi semua kartu visual
    const ketuaPembina = getCardPos('wrapper-ketua-pembina');
    const ketuaPkk = getCardPos('wrapper-ketua-pkk');
    const stafAhli = getCardPos('wrapper-staf-ahli');

    // Row Sekretaris & Bendahara
    const sekbenWrappers = document.querySelectorAll('#row-sekben .org-card-wrapper');
    const sekben = [];
    sekbenWrappers.forEach(function(w) {
        const card = w.querySelector('.org-card') || w;
        const r = card.getBoundingClientRect();
        const wrapperRect = wrapper.getBoundingClientRect();
        sekben.push({
            cx: r.left + r.width / 2 - wrapperRect.left,
            top: r.top - wrapperRect.top,
            bottom: r.bottom - wrapperRect.top,
            left: r.left - wrapperRect.left,
            width: r.width
        });
    });

    // Row Ketua Pokja
    const pokjaWrappers = document.querySelectorAll('#row-pokja .org-card-wrapper');
    const pokja = [];
    pokjaWrappers.forEach(function(w) {
        const card = w.querySelector('.org-card') || w;
        const r = card.getBoundingClientRect();
        const wrapperRect = wrapper.getBoundingClientRect();
        pokja.push({
            cx: r.left + r.width / 2 - wrapperRect.left,
            top: r.top - wrapperRect.top,
            bottom: r.bottom - wrapperRect.top,
            left: r.left - wrapperRect.left,
            width: r.width
        });
    });

    // 6. Tentukan posisi Backbone (Garis Utama Vertikal)
    // Hitung jarak paling kiri dari semua kartu visual
    const allLefts = [];
    if (ketuaPkk) allLefts.push(ketuaPkk.left);
    if (stafAhli) allLefts.push(stafAhli.left);
    sekben.forEach(function(s) { allLefts.push(s.left); });
    pokja.forEach(function(p) { allLefts.push(p.left); });

    const minLeft = Math.min.apply(null, allLefts);
    const backboneX = minLeft - 20; // Jarak 20px di sebelah kiri kartu

    // ===== 1. Garis Ketua Pembina → Ketua TP PKK =====
    if (ketuaPembina && ketuaPkk) {
        // Menghubungkan BOTTOM kartu atas ke TOP kartu bawah (Presisi)
        drawLine(ketuaPembina.cx, ketuaPembina.bottom, ketuaPkk.cx, ketuaPkk.top);
    }

    // ===== 2. Koneksi Ketua TP PKK ke Backbone =====
    if (ketuaPkk) {
        const pkkMidY = ketuaPkk.top + ketuaPkk.height / 2; // Tengah vertikal kartu

        // Garis horizontal dari SISI KIRI kartu ke backbone
        drawLine(ketuaPkk.left, pkkMidY, backboneX, pkkMidY);

        // Titik persimpangan di backbone
        drawCircle(backboneX, pkkMidY, 5);
    }

    // ===== 3. Garis Backbone Vertikal =====
    if (ketuaPkk) {
        // Cari titik cabang paling bawah (di atas Pokja baris terakhir)
        const allBranchPoints = [];
        if (stafAhli) allBranchPoints.push(stafAhli.top - 15);
        sekben.forEach(function(s) { allBranchPoints.push(s.top - 15); });
        pokja.forEach(function(p) { allBranchPoints.push(p.top - 15); });

        const lastBranchY = Math.max.apply(null, allBranchPoints);

        // Garis vertikal dari titik sambung Ketua TP PKK ke titik cabang terakhir
        drawLine(backboneX, ketuaPkk.top + ketuaPkk.height / 2, backboneX, lastBranchY);
    }

    // ===== 4. Cabang ke Staf Ahli =====
    if (stafAhli) {
        const branchY = stafAhli.top - 15; // 15px di atas kartu

        // Garis horizontal dari backbone ke TENGAH kartu Staf Ahli
        drawLine(backboneX, branchY, stafAhli.cx, branchY);

        // Titik persimpangan
        drawCircle(backboneX, branchY, 4);

        // Titik di atas kartu
        drawCircle(stafAhli.cx, branchY, 3);

        // Garis vertikal pendek turun ke ATAS kartu
        drawLine(stafAhli.cx, branchY, stafAhli.cx, stafAhli.top);
    }

    // ===== 5. Cabang ke Sekretaris & Bendahara =====
    if (sekben.length >= 2) {
        const branchY = sekben[0].top - 15;

        // Garis horizontal dari backbone ke TENGAH Bendahara (kartu terakhir)
        drawLine(backboneX, branchY, sekben[1].cx, branchY);

        // Titik persimpangan
        drawCircle(backboneX, branchY, 4);

        // Titik di atas masing-masing kartu
        drawCircle(sekben[0].cx, branchY, 3);
        drawCircle(sekben[1].cx, branchY, 3);

        // Garis vertikal turun ke kartu
        drawLine(sekben[0].cx, branchY, sekben[0].cx, sekben[0].top);
        drawLine(sekben[1].cx, branchY, sekben[1].cx, sekben[1].top);
    }

    // ===== 6. Cabang ke Pokja I-IV =====
    if (pokja.length >= 2) {
        // Kelompokkan berdasarkan baris
        const rows = {};
        pokja.forEach(function(p) {
            const rowKey = Math.round(p.top / 50) * 50;
            if (!rows[rowKey]) rows[rowKey] = [];
            rows[rowKey].push(p);
        });

        const rowKeys = Object.keys(rows).sort(function(a, b) { return parseInt(a) - parseInt(b); });

        rowKeys.forEach(function(key) {
            const rowPokjas = rows[key];
            const lastPokja = rowPokjas[rowPokjas.length - 1];
            const branchY = rowPokjas[0].top - 15;

            // Garis horizontal dari backbone ke TENGAH kartu terakhir di baris ini
            drawLine(backboneX, branchY, lastPokja.cx, branchY);

            // Titik persimpangan
            drawCircle(backboneX, branchY, 4);

            // Garis vertikal ke masing-masing kartu di baris ini
            rowPokjas.forEach(function(p) {
                drawCircle(p.cx, branchY, 3);
                drawLine(p.cx, branchY, p.cx, p.top);
            });
        });
    }
}

// Event Listeners untuk Redraw
window.addEventListener('resize', function() {
    clearTimeout(window._resizeConnectorTimer);
    window._resizeConnectorTimer = setTimeout(drawMobileConnectors, 200);
});

const originalForceRedraw = window.forceRedrawConnectors;
window.forceRedrawConnectors = function() {
    if (originalForceRedraw) originalForceRedraw();
    setTimeout(drawMobileConnectors, 200);
};

// Observer untuk mendeteksi halaman aktif
const strukturMobileObserver = new MutationObserver(function() {
    const page = document.getElementById('page-struktur');
    if (page && page.classList.contains('active')) {
        setTimeout(drawMobileConnectors, 500);
    }
});

const strukturPage = document.getElementById('page-struktur');
if (strukturPage) {
    strukturMobileObserver.observe(strukturPage, { attributes: true, attributeFilter: ['class'] });
}

// Override fungsi load data agar garis digambar ulang setelah data masuk
const originalPopulateStrukturDOM = window.populateStrukturDOM;
window.populateStrukturDOM = function(pengurus, pokjaList) {
    originalPopulateStrukturDOM(pengurus, pokjaList);
    setTimeout(drawMobileConnectors, 300);
};


