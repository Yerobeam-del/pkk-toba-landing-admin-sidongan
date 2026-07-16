<section class="apps-home-section" id="aplikasiSection" style="padding: 4rem 2rem; background: #f8fafc;">
    <style>
        .apps-home-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 2.5rem !important;
            max-width: 1200px !important;
            margin: 0 auto !important;
        }
        
        .apps-section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .apps-section-label {
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #0f766e;
            background: rgba(15, 118, 110, 0.08);
            padding: 6px 16px;
            border-radius: 20px;
            margin-bottom: 1rem;
        }
        
        .apps-section-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0f766e;
            margin-bottom: 0.8rem;
        }
        
        .apps-section-desc {
            font-size: 1rem;
            color: #718096;
            max-width: 550px;
            margin: 0 auto;
            line-height: 1.7;
        }
        
        @media (max-width: 768px) {
            .apps-home-grid {
                grid-template-columns: 1fr !important;
                gap: 2rem !important;
            }
            .apps-section-title {
                font-size: 1.8rem;
            }
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(72, 187, 120, 0.5); }
            50% { box-shadow: 0 0 0 8px rgba(72, 187, 120, 0); }
        }
        
        @keyframes pulse-orange {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.5); }
            50% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        }
    </style>

    {{-- HEADER SECTION --}}
    <div class="apps-section-header">
        <div class="apps-section-label">APLIKASI AKTIF</div>
        <h2 class="apps-section-title">Sistem yang Tersedia</h2>
        <p class="apps-section-desc">Akses layanan digital PKK Kabupaten Toba melalui aplikasi-aplikasi yang telah kami sediakan.</p>
    </div>

    {{-- Loading State --}}
    <div id="apps-loading" style="text-align: center; padding: 5rem 2rem;">
        <div style="font-size: 1.2rem; color: #64748b; font-weight: 500;">Memuat data aplikasi...</div>
    </div>

    {{-- Content Grid --}}
    <div class="apps-home-grid" id="apps-grid" style="display: none;"></div>

    {{-- Empty State --}}
    <div id="apps-empty" style="display: none;">
        <div style="text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, rgba(15,118,110,0.05), rgba(56,161,105,0.05)); border-radius: 20px; max-width: 1200px; margin: 0 auto;">
            <div style="width: 100px; height: 100px; margin: 0 auto 2rem; background: linear-gradient(135deg, rgba(15,118,110,0.1), rgba(56,161,105,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="1.5">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                    <line x1="8" y1="21" x2="16" y2="21"/>
                    <line x1="12" y1="17" x2="12" y2="21"/>
                </svg>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">Belum Ada Aplikasi</h3>
            <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Sistem aplikasi sedang dalam tahap pengembangan.</p>
        </div>
    </div>
</section>

<script>
let appsLoaded = false;

async function loadApps() {
    if (appsLoaded) return;
    
    const loadingEl = document.getElementById('apps-loading');
    const gridEl = document.getElementById('apps-grid');
    const emptyEl = document.getElementById('apps-empty');
    
    try {
        const response = await fetch('/api/v1/applications');
        const result = await response.json();
        
        loadingEl.style.display = 'none';
        
        // GABUNGKAN: active + maintenance + FILTER show_in_quick_access
        const activeApps = (result.data.active || []).filter(app => app.show_in_quick_access == true);
        const maintenanceApps = (result.data.maintenance || []).filter(app => app.show_in_quick_access == true);
        const allApps = [...activeApps, ...maintenanceApps];
        
        console.log('📊 Total apps:', allApps.length);
        console.log('Active:', activeApps.length, 'Maintenance:', maintenanceApps.length);
        
        if (result.success && allApps.length > 0) {
            gridEl.style.display = 'grid';
            
            gridEl.innerHTML = allApps.map((app, index) => {
                // CEK STATUS MAINTENANCE
                const isMaintenance = app.status === 'maintenance';
                
                // ✅ ARRAY 10 WARNA UNTUK APLIKASI
                const appColors = [
                    { primary: '#2563eb', bg: 'linear-gradient(135deg, #dbeafe, #eff6ff)', btnBg: '#2563eb', circle: '#bfdbfe' },      // Biru (SIEDA)
                    { primary: '#dc2626', bg: 'linear-gradient(135deg, #fee2e2, #fef2f2)', btnBg: '#dc2626', circle: '#fecaca' },      // Merah (SIDONGAN)
                    { primary: '#7c3aed', bg: 'linear-gradient(135deg, #ede9fe, #f5f3ff)', btnBg: '#7c3aed', circle: '#ddd6fe' },      // Ungu
                    { primary: '#059669', bg: 'linear-gradient(135deg, #d1fae5, #ecfdf5)', btnBg: '#059669', circle: '#a7f3d0' },      // Hijau
                    { primary: '#d97706', bg: 'linear-gradient(135deg, #fef3c7, #fffbeb)', btnBg: '#d97706', circle: '#fde68a' },      // Kuning/Orange
                    { primary: '#db2777', bg: 'linear-gradient(135deg, #fce7f3, #fdf2f8)', btnBg: '#db2777', circle: '#fbcfe8' },      // Pink
                    { primary: '#0891b2', bg: 'linear-gradient(135deg, #cffafe, #ecfeff)', btnBg: '#0891b2', circle: '#a5f3fc' },      // Cyan
                    { primary: '#7c2d12', bg: 'linear-gradient(135deg, #ffedd5, #fff7ed)', btnBg: '#7c2d12', circle: '#fed7aa' },      // Brown
                    { primary: '#4338ca', bg: 'linear-gradient(135deg, #e0e7ff, #eef2ff)', btnBg: '#4338ca', circle: '#c7d2fe' },      // Indigo
                    { primary: '#be185d', bg: 'linear-gradient(135deg, #fce7f3, #fdf2f8)', btnBg: '#be185d', circle: '#f9a8d4' }       // Rose
                ];
                
                // Tentukan warna berdasarkan short_name atau index
                const appName = (app.short_name || app.name || '').toLowerCase();
                let colorIndex = 0;
                
                if (appName.includes('sieda') || appName.includes('e-dasawisma')) {
                    colorIndex = 0; // Biru
                } else if (appName.includes('sidongan')) {
                    colorIndex = 1; // Merah
                } else {
                    // Gunakan index aplikasi untuk warna (loop 10 warna)
                    colorIndex = (index % 10);
                }
                
                const colors = appColors[colorIndex];
                
                const iconUrl = app.icon ? '/storage/' + app.icon.replace(/^(storage\/|public\/)/i, '') : null;
                
                const features = Array.isArray(app.features) ? app.features.slice(0, 5) : [];
                const featuresHtml = features.map(f => `
                    <li style="display: flex; align-items: center; gap: 8px; padding: 6px 0; font-size: 0.9rem; color: ${isMaintenance ? '#94a3b8' : '#4a5568'};">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="${isMaintenance ? '#94a3b8' : colors.primary}" stroke-width="2.5" style="flex-shrink: 0;">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                        ${f}
                    </li>
                `).join('');
                
                const iconHtml = iconUrl 
                    ? `<img src="${iconUrl}" alt="${app.short_name}" style="width: 100%; height: 100%; object-fit: contain; padding: 10px; ${isMaintenance ? 'filter: grayscale(100%) brightness(1.3);' : 'filter: brightness(0) invert(1);'}">`
                    : `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 2rem;">${(app.short_name || 'A').charAt(0)}</div>`;
                
                // URL APLIKASI
                const appUrl = app.url || '#';
                
                return `
                <div style="${isMaintenance ? 'pointer-events: none; cursor: not-allowed;' : ''}">
                    <article style="
                        background: #fff; 
                        border-radius: 20px; 
                        overflow: hidden; 
                        box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
                        border: 1px solid rgba(0,0,0,0.04); 
                        transition: all 0.4s ease; 
                        display: flex; 
                        flex-direction: column; 
                        height: 100%;
                        ${isMaintenance ? 'filter: grayscale(80%); opacity: 0.85;' : 'cursor: pointer;'}
                        position: relative;
                    "
                    ${!isMaintenance ? `onclick="window.location.href='${appUrl}'"
                    onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 20px 60px rgba(0,0,0,0.12)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 20px rgba(0,0,0,0.08)'"` : ''}>
                        
                        ${isMaintenance ? `
                        <div style="position:absolute;top:1rem;right:1rem;background:rgba(245,158,11,0.9);color:#fff;padding:0.4rem 0.8rem;border-radius:20px;font-size:0.7rem;font-weight:700;display:flex;align-items:center;gap:0.4rem;z-index:10;box-shadow:0 2px 8px rgba(245,158,11,0.3);">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                            MAINTENANCE
                        </div>` : ''}
                        
                        <div style="position: relative; padding: 2.5rem 2.5rem 1.5rem; overflow: hidden; background: ${isMaintenance ? '#f1f5f9' : colors.bg};">
                            <div style="position: absolute; top: -50%; right: -30%; width: 200px; height: 200px; border-radius: 50%; background: ${isMaintenance ? '#cbd5e1' : colors.circle}; opacity: ${isMaintenance ? '0.2' : '0.4'};"></div>
                            
                            <div style="width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 8px 25px rgba(0,0,0,0.1); background: ${isMaintenance ? '#94a3b8' : colors.primary}; position: relative; z-index: 2;">
                                ${iconHtml}
                            </div>
                        </div>
                        
                        <div style="padding: 2rem 2.5rem 2rem; flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.3rem; color: ${isMaintenance ? '#64748b' : colors.primary};">${app.short_name || app.name}</h3>
                            <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 1rem; font-weight: 500;">${app.name || ''}</p>
                            <p style="color: ${isMaintenance ? '#94a3b8' : '#4a5568'}; line-height: 1.7; margin-bottom: 1.5rem; font-size: 0.95rem;">${app.description || ''}</p>
                            ${features.length ? `<ul style="list-style: none; margin: 0; padding: 0; margin-bottom: 2rem;">${featuresHtml}</ul>` : ''}
                        </div>
                        
                        <div style="padding: 1.5rem 2.5rem; border-top: 1px solid rgba(0,0,0,0.05); display: flex; align-items: center; justify-content: space-between;">
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
                    </article>
                </div>
                `;
            }).join('');
        } else {
            emptyEl.style.display = 'block';
        }
        
        appsLoaded = true;
    } catch (error) {
        console.error('Error:', error);
        loadingEl.innerHTML = '<p style="color: #ef4444;">Gagal memuat aplikasi.</p>';
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
</script>