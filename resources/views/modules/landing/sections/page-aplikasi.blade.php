<div class="page" id="page-aplikasi" style="display: none;">
    <div class="page-header" style="background: linear-gradient(135deg, var(--primary), var(--primary-light));">
        <div class="page-header-content">
            <h1>Aplikasi & Sistem</h1>
            <p>Sistem informasi digital PKK Kabupaten Toba</p>
            <div class="breadcrumb">
                <a onclick="navigateTo('beranda')">Beranda</a><span>/</span><span class="current">Aplikasi</span>
            </div>
        </div>
    </div>
    
    <div id="aplikasi-loading" style="text-align: center; padding: 4rem 2rem;">
        <div style="font-size: 1.2rem; color: var(--text-muted);">Memuat data aplikasi...</div>
    </div>
    
    <section class="apps-full-section" id="aplikasi-content" style="display: none;">
        
        {{-- SECTION: APLIKASI AKTIF --}}
        <div class="section-header" id="active-section-header">
            <div class="section-label">Aplikasi Aktif</div>
            <h2 class="section-title">Sistem yang Tersedia</h2>
        </div>
        <div class="apps-full-grid" id="active-apps-grid">
            <div style="grid-column: 1/-1; text-align: center; padding: 2rem; color: var(--text-muted);">Memuat aplikasi aktif...</div>
        </div>
    </section>
</div>

<script>
let aplikasiDataLoaded = false;

// ==========================================
// HELPER FUNCTIONS
// ==========================================

function getIconHtml(app, size = 40) {
    return `
    <svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" 
         stroke="rgba(255,255,255,0.95)" stroke-width="2" 
         stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
        <line x1="8" y1="21" x2="16" y2="21"/>
        <line x1="12" y1="17" x2="12" y2="21"/>
    </svg>
    `;
}

function buildImageUrl(iconPath) {
    if (!iconPath) return null;
    const cleanPath = iconPath.replace(/^(storage\/|public\/|app\/public\/)/i, '');
    return '/storage/' + cleanPath;
}

// Empty state untuk APLIKASI AKTIF
function renderEmptyActiveState() {
    return `
    <div style="grid-column: 1 / -1; width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 6rem 2rem; min-height: 50vh; margin: 0 auto;">
        <div style="width: 120px; height: 120px; margin: 0 auto 2rem; background: linear-gradient(135deg, rgba(15,107,99,0.1), rgba(20,184,166,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#0f6b63" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.8;">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                <line x1="8" y1="21" x2="16" y2="21"/>
                <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
        </div>
        <h3 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin: 0 0 0.75rem 0;">Belum Ada Aplikasi Aktif</h3>
        <p style="color: #64748b; font-size: 1.05rem; line-height: 1.7; margin: 0 auto 2rem; max-width: 500px;">Tim kami sedang mempersiapkan sistem digital untuk meningkatkan pelayanan PKK Kabupaten Toba. Silakan kunjungi kembali nanti untuk update terbaru.</p>
        <a onclick="navigateTo('beranda')" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, #0f6b63, #14b8a6); color: #fff; border-radius: 12px; font-weight: 600; text-decoration: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(15,107,99,0.3);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(15,107,99,0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(15,107,99,0.3)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            Kembali ke Beranda
        </a>
    </div>
    `;
}

// ==========================================
// ✅ RENDER APLIKASI AKTIF (FIXED VERSION)
// ==========================================
function renderActiveApps(apps) {
    const container = document.getElementById('active-apps-grid');
    if (!container) {
        console.error('Container active-apps-grid not found!');
        return;
    }
    
    console.log('📊 Rendering apps:', apps);
    
    if (!apps || apps.length === 0) { 
        console.log('No apps to render, showing empty state');
        container.innerHTML = renderEmptyActiveState();
        const activeHeader = document.getElementById('active-section-header');
        if (activeHeader) activeHeader.style.display = 'none';
        return; 
    }
    
    const activeCardTemplate = (app, index) => {
        // CEK STATUS MAINTENANCE - PASTIKAN BENAR
        const isMaintenance = app.status === 'maintenance';
        console.log(`App ${index}: ${app.short_name} - Status: ${app.status} - Is Maintenance: ${isMaintenance}`);
                
        // ARRAY 10 WARNA UNTUK APLIKASI
        const appColors = [
            { primary: '#2563eb', bg: 'linear-gradient(135deg, #dbeafe, #eff6ff)', circle: '#bfdbfe', btn: '#2563eb' },      // Biru (SIEDA)
            { primary: '#dc2626', bg: 'linear-gradient(135deg, #fee2e2, #fef2f2)', circle: '#fecaca', btn: '#dc2626' },      // Merah (SIDONGAN)
            { primary: '#7c3aed', bg: 'linear-gradient(135deg, #ede9fe, #f5f3ff)', circle: '#ddd6fe', btn: '#7c3aed' },      // Ungu
            { primary: '#059669', bg: 'linear-gradient(135deg, #d1fae5, #ecfdf5)', circle: '#a7f3d0', btn: '#059669' },      // Hijau
            { primary: '#d97706', bg: 'linear-gradient(135deg, #fef3c7, #fffbeb)', circle: '#fde68a', btn: '#d97706' },      // Kuning/Orange
            { primary: '#db2777', bg: 'linear-gradient(135deg, #fce7f3, #fdf2f8)', circle: '#fbcfe8', btn: '#db2777' },      // Pink
            { primary: '#0891b2', bg: 'linear-gradient(135deg, #cffafe, #ecfeff)', circle: '#a5f3fc', btn: '#0891b2' },      // Cyan
            { primary: '#7c2d12', bg: 'linear-gradient(135deg, #ffedd5, #fff7ed)', circle: '#fed7aa', btn: '#7c2d12' },      // Brown/Orange gelap
            { primary: '#4338ca', bg: 'linear-gradient(135deg, #e0e7ff, #eef2ff)', circle: '#c7d2fe', btn: '#4338ca' },      // Indigo
            { primary: '#be185d', bg: 'linear-gradient(135deg, #fce7f3, #fdf2f8)', circle: '#f9a8d4', btn: '#be185d' }       // Rose/Pink gelap
        ];

        // Tentukan class dan warna berdasarkan short_name
        const appName = (app.short_name || app.name || '').toLowerCase().trim();
        let colorIndex = 0;
        let cardClass = '';

        if (appName.includes('sieda') || appName.includes('e-dasawisma')) {
            cardClass = 'sieda';
            colorIndex = 0; // Biru
        } else if (appName.includes('sidongan')) {
            cardClass = 'SIDONGAN';
            colorIndex = 1; // Merah
        } else {
            // Gunakan index aplikasi untuk warna (loop 10 warna)
            colorIndex = (index % 10);
            cardClass = `app-color-${colorIndex}`;
        }

        // Ambil warna yang sesuai
        const colors = appColors[colorIndex];
        
        // Build image URL
        let imgUrl = null;
        if (app.icon) {
            const cleanPath = app.icon.replace(/^(storage\/|public\/|app\/public\/)/i, '');
            imgUrl = '/storage/' + cleanPath;
        }
        
        // Features list
        const features = Array.isArray(app.features) ? app.features.slice(0, 5) : [];
        const featuresHtml = features.length > 0 
            ? features.map(f => `
                <li style="display: flex; align-items: center; gap: 8px; padding: 6px 0; font-size: 0.9rem; color: ${isMaintenance ? '#94a3b8' : '#4a5568'};">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${isMaintenance ? '#94a3b8' : colors.primary}" stroke-width="2.5" style="flex-shrink: 0;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    ${f}
                </li>
            `).join('')
            : `
                <li style="display: flex; align-items: center; gap: 8px; padding: 6px 0; font-size: 0.9rem; color: ${isMaintenance ? '#94a3b8' : '#4a5568'};">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${isMaintenance ? '#94a3b8' : colors.primary}" stroke-width="2.5" style="flex-shrink: 0;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Fitur unggulan aplikasi
                </li>
            `;
        
        // Icon HTML dengan fallback
        let iconHtml = '';
        if (imgUrl) {
            iconHtml = `
                <img src="${imgUrl}" 
                    alt="${app.short_name || app.name}" 
                    style="width:100%;height:100%;object-fit:contain;display:block;padding:10px;${isMaintenance ? 'filter: grayscale(100%) brightness(1.3) !important;' : ''}"
                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="placeholder-icon" style="display:none;width:50px;height:50px;align-items:center;justify-content:center;">
                    ${getIconHtml(app, 40)}
                </div>
            `;
        } else {
            iconHtml = `<div class="placeholder-icon" style="width:50px;height:50px;display:flex;align-items:center;justify-content:center;${isMaintenance ? 'filter: grayscale(100%) brightness(1.5) !important;' : ''}">${getIconHtml(app, 40)}</div>`;
        }
        
        // RENDER CARD DENGAN MAINTENANCE MODE
        // URL APLIKASI
        const appUrl = app.url || '#';

        return `
        <div class="app-card-home ${cardClass} ${isMaintenance ? 'maintenance-mode' : ''}" 
            style="
                ${isMaintenance ? 'filter: grayscale(80%) !important; opacity: 0.85 !important; pointer-events: none !important; cursor: not-allowed !important;' : 'cursor: pointer;'}
                background: #fff;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0,0,0,0.08);
                transition: all 0.3s ease;
            "
            ${!isMaintenance ? `onclick="window.location.href='${appUrl}'"
            onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 60px rgba(0,0,0,0.12)'"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"` : ''}>
            
            <div class="app-card-header" style="position: relative; overflow: hidden; background: ${isMaintenance ? '#f1f5f9' : colors.bg};">
                <div style="position: absolute; top: -50%; right: -30%; width: 200px; height: 200px; border-radius: 50%; background: ${isMaintenance ? '#cbd5e1' : colors.circle}; opacity: ${isMaintenance ? '0.2' : '0.4'};"></div>
                <div class="app-icon-wrapper" style="${isMaintenance ? 'filter: grayscale(100%) brightness(1.2) !important;' : ''}">
                    ${iconHtml}
                </div>
                ${isMaintenance ? `
                <div style="position:absolute;top:1rem;right:1rem;background:rgba(245,158,11,0.9);color:#fff;padding:0.4rem 0.8rem;border-radius:20px;font-size:0.7rem;font-weight:700;display:flex;align-items:center;gap:0.4rem;z-index:10;box-shadow:0 2px 8px rgba(245,158,11,0.3);">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                    </svg>
                    MAINTENANCE
                </div>` : ''}
            </div>
            
            <div class="app-card-body" style="padding-top: 2rem;">
                <h3 class="app-name" style="${isMaintenance ? 'color: #64748b;' : ''}">${app.short_name || app.name || 'Aplikasi'}</h3>
                <p class="app-fullname" style="color: #64748b;">${app.name || ''}</p>
                <p class="app-description" style="color: #64748b;">${app.description || 'Sistem informasi digital terpadu PKK Kabupaten Toba.'}</p>
                <ul class="app-features">${featuresHtml}</ul>
            </div>
            
            <div class="app-card-footer" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;">
                <span class="app-btn" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1.25rem;
                    background: ${isMaintenance ? '#94a3b8' : colors.btn};
                    color: #fff;
                    border: none;
                    border-radius: 10px;
                    font-weight: 600;
                    cursor: ${isMaintenance ? 'not-allowed' : 'pointer'};
                    transition: all 0.3s;
                ">
                    ${isMaintenance ? 'Sedang Dalam Perbaikan' : `Akses ${app.short_name || 'Aplikasi'}`}
                    ${!isMaintenance ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>` : ''}
                </span>
                <div class="app-status" style="
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 6px 12px;
                    background: ${isMaintenance ? 'rgba(245,158,11,0.1)' : 'rgba(34,197,94,0.1)'};
                    border-radius: 20px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    color: ${isMaintenance ? '#f59e0b' : '#22c55e'};
                ">
                    <div class="app-status-dot" style="
                        width: 7px;
                        height: 7px;
                        background: ${isMaintenance ? '#f59e0b' : '#22c55e'};
                        border-radius: 50%;
                        ${isMaintenance ? '' : 'animation: pulse 2s infinite;'}
                    "></div>
                    ${isMaintenance ? 'Dalam Perbaikan' : 'Aktif'}
                </div>
            </div>
        </div>`;
    };
    
    const html = apps.map((app, i) => activeCardTemplate(app, i)).join('');
    console.log('Generated HTML length:', html.length);
    container.innerHTML = html;
}

// ==========================================
// LOAD DATA & INIT
// ==========================================
async function loadAplikasiData() {
    if (aplikasiDataLoaded) return;
    
    const loadingEl = document.getElementById('aplikasi-loading');
    const contentEl = document.getElementById('aplikasi-content');
    const activeGrid = document.getElementById('active-apps-grid');
    const activeHeader = document.getElementById('active-section-header');
    
    try {
        const response = await fetch('/api/v1/applications');
        const result = await response.json();
        
        if (!result.success) throw new Error(result.message);
        
        // GABUNGKAN: active + maintenance
        const activeApps = result.data.active || [];
        const maintenanceApps = result.data.maintenance || [];
        const allApps = [...activeApps, ...maintenanceApps];
        
        console.log('📊 Total apps:', allApps.length);
        console.log('Active:', activeApps.length, 'Maintenance:', maintenanceApps.length);
        
        if (allApps && allApps.length > 0) {
            renderActiveApps(allApps);
        } else {
            if (activeGrid) activeGrid.innerHTML = renderEmptyActiveState();
            if (activeHeader) activeHeader.style.display = 'none';
        }
        
        if (loadingEl) loadingEl.style.display = 'none';
        if (contentEl) contentEl.style.display = 'block';
        
        aplikasiDataLoaded = true;
        
    } catch (error) {
        console.error('Error loading aplikasi:', error);
        
        if (activeGrid) activeGrid.innerHTML = renderEmptyActiveState();
        if (activeHeader) activeHeader.style.display = 'none';
        
        if (loadingEl) loadingEl.style.display = 'none';
        if (contentEl) contentEl.style.display = 'block';
        
        aplikasiDataLoaded = true;
    }
}

// Auto-load when page becomes active
document.addEventListener('DOMContentLoaded', () => {
    const observer = new MutationObserver(() => {
        const page = document.getElementById('page-aplikasi');
        if (page && page.classList.contains('active') && !aplikasiDataLoaded) {
            setTimeout(() => loadAplikasiData(), 100);
            observer.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
    
    const page = document.getElementById('page-aplikasi');
    if (page && page.classList.contains('active')) {
        setTimeout(() => loadAplikasiData(), 100);
    }
});
</script>