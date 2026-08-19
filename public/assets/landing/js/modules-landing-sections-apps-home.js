/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/modules/landing/sections/apps-home.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


let appsLoaded = false;

const DEFAULT_COLOR = '#0f6b63'; // Hijau PKK, dipakai bila admin belum memilih warna

// Rumus turunan warna — HARUS sama dengan page-aplikasi.blade.php dan
// pratinjau di admin/aplikasi/partials/color-picker.blade.php.
function mixWhite(hex, ratio) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    const m = (v) => Math.round(v + (255 - v) * ratio);
    return `rgb(${m(r)}, ${m(g)}, ${m(b)})`;
}

async function loadApps() {
    if (appsLoaded) return;

    const loadingEl = document.getElementById('apps-loading');
    const gridEl = document.getElementById('apps-grid');
    const emptyEl = document.getElementById('apps-empty');

    try {
        const response = await fetch('/api/v1/applications');
        const result = await response.json();

        loadingEl.style.display = 'none';

        // Beranda hanya menampung 2 kartu (grid repeat(2, 1fr)), sesuai batas di
        // Admin Panel > Manajemen Aplikasi. Validasi admin hanya menghitung aplikasi
        // berstatus 'active', jadi batas ini ditegakkan lagi di sini agar aplikasi
        // 'maintenance' tidak menambah kartu melebihi 2.
        const MAKS_KARTU_BERANDA = 2;

        // API mengelompokkan data per kategori: { aplikasi: {active, maintenance, development}, layanan: {...} }
        // Beranda menggabungkan semua kategori, lalu disaring lewat toggle show_in_quick_access dari admin.
        const groups = result.data || {};
        const collect = (status) => Object.values(groups).flatMap(group => group?.[status] || []);

        const activeApps = collect('active').filter(app => app.show_in_quick_access);
        const maintenanceApps = collect('maintenance').filter(app => app.show_in_quick_access);

        // Aktif diprioritaskan, maintenance hanya mengisi sisa slot
        const allApps = [...activeApps, ...maintenanceApps].slice(0, MAKS_KARTU_BERANDA);

        if (result.success && allApps.length > 0) {
            gridEl.style.display = 'grid';

            gridEl.innerHTML = allApps.map((app, index) => {
                const isMaintenance = app.status === 'maintenance';

                // Warna berasal dari Admin Panel > Manajemen Aplikasi (kolom `color`).
                // Turunannya dihitung dengan rumus yang sama seperti halaman Aplikasi
                // dan pratinjau di form admin, supaya ketiganya selalu cocok.
                const warnaUtama = /^#[0-9a-fA-F]{6}$/.test(app.color || '') ? app.color : DEFAULT_COLOR;
                const colors = {
                    primary: warnaUtama,
                    btnBg: warnaUtama,
                    bg: `linear-gradient(135deg, ${mixWhite(warnaUtama, 0.88)}, ${mixWhite(warnaUtama, 0.96)})`,
                    circle: mixWhite(warnaUtama, 0.7)
                };

                const iconUrl = app.icon ? '/storage/' + app.icon.replace(/^(storage\/|public\/)/i, '') : null;

                const features = Array.isArray(app.features) ? app.features.slice(0, 5) : [];
                const featuresHtml = features.map(f => `
                    <li style="display: flex; align-items: center; gap: 8px; padding: 6px 0; font-size: 0.9rem; color: ${isMaintenance ? '#94a3b8' : '#4a5568'};">
                        <svg class="u-shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${isMaintenance ? '#94a3b8' : colors.primary}" stroke-width="2.5">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        ${f}
                    </li>
                `).join('');

                const iconHtml = iconUrl
                    ? `<img src="${iconUrl}" alt="${app.short_name}" style="width: 100%; height: 100%; object-fit: contain; padding: 10px; ${isMaintenance ? 'filter: grayscale(100%) brightness(1.3);' : 'filter: brightness(0) invert(1);'}">`
                    : `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 2rem;">${(app.short_name || 'A').charAt(0)}</div>`;

                const appUrl = app.url || '#';

                return `
                <a href="${isMaintenance ? '#' : appUrl}" class="app-card-home ${isMaintenance ? 'maintenance-mode' : ''}" style="
                        background: #fff;
                        border-radius: 20px;
                        overflow: hidden;
                        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                        border: 1px solid rgba(0,0,0,0.04);
                        transition: all 0.4s ease;
                        display: flex;
                        flex-direction: column;
                        height: 100%;
                        ${isMaintenance ? 'filter: grayscale(80%); opacity: 0.85; pointer-events: none; cursor: not-allowed;' : 'cursor: pointer;'}
                        position: relative;
                    " ${!isMaintenance ? `onclick="window.location.href='${appUrl}'"` : ''}>

                        ${isMaintenance ? `
                        <div style="position:absolute;top:1rem;right:1rem;background:rgba(245,158,11,0.9);color:#fff;padding:0.4rem 0.8rem;border-radius:20px;font-size:0.7rem;font-weight:700;display:flex;align-items:center;gap:0.4rem;z-index:10;box-shadow:0 2px 8px rgba(245,158,11,0.3);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                            MAINTENANCE
                        </div>` : ''}

                        <div style="position: relative; padding: 2.5rem 2.5rem 1.5rem; overflow: hidden; background: ${isMaintenance ? '#f1f5f9' : colors.bg}; pointer-events: none;">
                            <div style="position: absolute; top: -50%; right: -30%; width: 200px; height: 200px; border-radius: 50%; background: ${isMaintenance ? '#cbd5e1' : colors.circle}; opacity: ${isMaintenance ? '0.2' : '0.4'};"></div>

                            <div style="width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 8px 25px rgba(0,0,0,0.1); background: ${isMaintenance ? '#94a3b8' : colors.primary}; position: relative; z-index: 2;">
                                ${iconHtml}
                            </div>
                        </div>

                        <div style="padding: 2rem 2.5rem 2rem; flex: 1; display: flex; flex-direction: column; pointer-events: none;">
                            <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.3rem; color: ${isMaintenance ? '#64748b' : colors.primary};">${app.short_name || app.name}</h3>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1rem; font-weight: 500;">${app.name || ''}</p>
                            <p style="color: ${isMaintenance ? '#94a3b8' : '#4a5568'}; line-height: 1.7; margin-bottom: 1.5rem; font-size: 0.95rem;">${app.description || ''}</p>
                            ${features.length ? `<ul style="list-style: none; margin: 0; padding: 0; margin-bottom: 2rem;">${featuresHtml}</ul>` : ''}
                        </div>

                        <div style="padding: 1.5rem 2.5rem; border-top: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between; pointer-events: none;">
                            <span style="
                                display: inline-flex;
                                align-items: center;
                                gap: 8px;
                                padding: 12px 28px;
                                border-radius: 14px;
                                font-weight: 600;
                                font-size: 0.9rem;
                                background: ${isMaintenance ? '#94a3b8' : colors.btnBg};
                                color: #fff;
                                text-decoration: none;
                                cursor: ${isMaintenance ? 'not-allowed' : 'pointer'};
                            ">
                                ${isMaintenance ? 'Sedang Dalam Perbaikan' : `Akses ${app.short_name || 'Aplikasi'}`}
                                ${!isMaintenance ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="transition: transform 0.3s;">
                                    <path d="M5 12h14M12 5l7 7-7 7"></path>
                                </svg>` : ''}
                            </span>
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: ${isMaintenance ? '#f59e0b' : '#48bb78'}; font-weight: 500;">
                                <div style="width: 7px; height: 7px; background: ${isMaintenance ? '#f59e0b' : '#48bb78'}; border-radius: 50%; animation: ${isMaintenance ? 'pulse-orange' : 'pulse'} 2s infinite;"></div>
                                ${isMaintenance ? 'Dalam Perbaikan' : 'Aktif'}
                            </div>
                        </div>
                </a>
                `;
            }).join('');
        } else {
            emptyEl.style.display = 'block';
        }

        appsLoaded = true;
    } catch (error) {
        console.error('Error:', error);
        loadingEl.innerHTML = '<p class="u-text-danger">Gagal memuat aplikasi.</p>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const section = document.getElementById('aplikasiSection');
    if (section && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !appsLoaded) {
                    setTimeout(() => loadApps(), 100);
                    observer.disconnect();
                }
            });
        }, { threshold: 0.1 });
        observer.observe(section);
    } else {
        loadApps();
    }
});


