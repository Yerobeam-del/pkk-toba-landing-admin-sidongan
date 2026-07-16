@extends('admin.layouts.app')
@section('title', 'Manajemen Desa')
@section('page-title', 'Manajemen Desa')

@section('content')

{{-- Header Section --}}
<div class="desa-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Manajemen Desa</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kabupaten Toba • Data dari API wilayah.id</p>
    </div>
    
    <div class="desa-header-actions" style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
        
        {{-- Custom Select dengan SVG Icon --}}
        <div style="position:relative;width:220px;min-width:200px;display:flex;align-items:center">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                style="position:absolute;left:0.85rem;color:var(--text-muted);pointer-events:none;z-index:1">
                <circle cx="11" cy="11" r="8"/>
                <path d="M21 21l-4.35-4.35"/>
            </svg>
            
            <select id="filterKecamatan" class="form-control" style="width:100%;padding:0.6rem 2.5rem 0.6rem 2.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.75rem center;background-size:18px;cursor:pointer">
                <option value="">Filter Kecamatan</option>
            </select>
        </div>
        
        {{-- Tombol Tambah Desa --}}
        <a id="btnTambahDesa" href="{{ route('admin.desa.create') }}" class="btn btn-primary" style="white-space:nowrap;display:inline-flex;align-items:center;gap:0.5rem">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Desa
        </a>
    </div>
</div>

{{-- Loading State --}}
<div id="loading-state" style="text-align:center;padding:3rem;color:var(--text-muted)">
    <div style="width:48px;height:48px;border:4px solid rgba(20,184,166,0.1);border-top-color:var(--primary);border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 1rem"></div>
    <div style="font-size:1rem;font-weight:600;margin-bottom:0.5rem">Memuat data...</div>
    <div style="font-size:0.85rem">Mengambil data desa per kecamatan</div>
</div>

{{-- Error State --}}
<div id="error-state" style="display:none;text-align:center;padding:3rem">
    <div style="width:64px;height:64px;background:#fef2f2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <h3 style="color:var(--danger);margin-bottom:0.5rem;font-size:1.1rem">Gagal Memuat Data</h3>
    <p id="error-message" style="color:var(--text-muted);margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto"></p>
    <button onclick="location.reload()" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
        Refresh Halaman
    </button>
</div>

{{-- Empty State --}}
<div id="empty-state" style="display:none;text-align:center;padding:3rem">
    <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
    </div>
    <h3 style="margin-bottom:0.5rem;font-size:1.1rem;font-weight:700;color:var(--text-dark)">Belum Ada Data Desa</h3>
    <p style="color:var(--text-muted);margin-bottom:1.5rem;max-width:500px;margin-left:auto;margin-right:auto;font-size:0.9rem">
        Belum ada desa yang diinput. Silakan tambah desa pertama Anda untuk mulai mengelola data.
    </p>
    <a href="{{ route('admin.desa.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Desa Pertama
    </a>
</div>

{{-- Content State --}}
<div id="content-state" style="display:none">
    <div id="no-data-message" style="display:none;text-align:center;padding:2rem;background:#f8fafc;border-radius:10px;margin-bottom:1.5rem;color:var(--text-muted)">
        <p>Tidak ada kecamatan dengan data desa. Silakan tambah desa untuk memulai.</p>
    </div>
    
    <div class="card" style="padding:0;overflow:hidden;border:1px solid rgba(0,0,0,0.06);border-radius:12px">
        <div id="accordion-container"></div>
    </div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .desa-header {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    
    .desa-header h1 {
        font-size: 1.25rem !important;
    }
    
    .desa-header-actions {
        width: 100% !important;
        flex-direction: column !important;
    }
    
    .desa-header-actions > div {
        width: 100% !important;
        min-width: 100% !important;
    }
    
    .desa-header-actions #filterKecamatan {
        width: 100% !important;
    }
    
    .desa-header-actions #btnTambahDesa {
        width: 100% !important;
        justify-content: center !important;
    }
    
    /* Hide desktop table, show mobile cards */
    .desktop-table-view {
        display: none !important;
    }
    
    .mobile-card-view {
        display: block !important;
    }
    
    /* Accordion button responsive */
    .accordion-header-btn {
        padding: 1rem !important;
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.75rem !important;
    }
    
    .accordion-header-btn > div:last-child {
        width: 100%;
        justify-content: space-between;
    }
    
    /* Stats responsive */
    .kec-stats {
        flex-direction: column !important;
        gap: 0.75rem !important;
        padding: 0.75rem !important;
    }
    
    .mobile-card-view .desa-card {
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: #fff;
        border-radius: 10px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    
    .mobile-card-view .desa-card-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .mobile-card-view .desa-card-photo {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        background: #f8fafc;
        flex-shrink: 0;
    }
    
    .mobile-card-view .desa-card-info {
        flex: 1;
        min-width: 0;
    }
    
    .mobile-card-view .desa-card-name {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }
    
    .mobile-card-view .desa-card-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .mobile-card-view .desa-card-actions {
        display: flex;
        gap: 0.5rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(0,0,0,0.06);
    }
    
    .mobile-card-view .desa-card-actions a,
    .mobile-card-view .desa-card-actions button {
        flex: 1;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        color: #94a3b8;
        border-radius: 8px;
        transition: all 0.2s;
        cursor: pointer;
        border: none;
    }
    
    .mobile-card-view .desa-card-actions a:hover {
        background: #eff6ff;
        color: #2563eb;
    }
    
    .mobile-card-view .desa-card-actions button:hover {
        background: #fef2f2;
        color: #ef4444;
    }
}

/* Desktop: show table, hide card */
@media (min-width: 769px) {
    .desktop-table-view {
        display: block !important;
    }
    
    .mobile-card-view {
        display: none !important;
    }
}
</style>

<script>
let allKecamatans = [];
let desasData = {};
let currentFilter = '';

async function initData() {
    document.getElementById('loading-state').style.display = 'block';
    document.getElementById('error-state').style.display = 'none';
    document.getElementById('content-state').style.display = 'none';
    document.getElementById('empty-state').style.display = 'none';

    try {
        // Load Kecamatan
        const kecResponse = await fetch('/api/v1/kecamatans');
        if (!kecResponse.ok) throw new Error('Gagal memuat data kecamatan');
        
        const kecResult = await kecResponse.json();
        if (!kecResult.data || !Array.isArray(kecResult.data)) {
            throw new Error('Format data kecamatan tidak valid');
        }
        
        allKecamatans = kecResult.data;
        console.log(`✅ Loaded ${allKecamatans.length} kecamatan(s)`);

        // Load Desa
        try {
            const desaResponse = await fetch('/api/v1/desas');
            if (desaResponse.ok) {
                const desaResult = await desaResponse.json();
                
                desasData = {};
                let totalDesa = 0;
                
                desaResult.data.forEach(k => {
                    desasData[k.id] = k.desas || [];
                    totalDesa += (k.desas || []).length;
                });
                
                console.log(`✅ Loaded ${totalDesa} desa(s) in ${Object.keys(desasData).length} kecamatan(s)`);
            }
        } catch (error) {
            console.warn('⚠️ Failed to load desa:', error);
            desasData = {};
        }

        // Populate Filter Dropdown
        const filterSelect = document.getElementById('filterKecamatan');
        filterSelect.innerHTML = '<option value="">Filter Kecamatan</option>';
        
        const kecamatansWithDesa = allKecamatans.filter(k => (desasData[k.id] || []).length > 0);
        
        kecamatansWithDesa.forEach(k => {
            filterSelect.innerHTML += `<option value="${k.id}">${k.name} (${(desasData[k.id] || []).length} desa)</option>`;
        });

        // Check if there's any data
        const totalDesa = Object.values(desasData).reduce((sum, arr) => sum + arr.length, 0);
        
        if (totalDesa === 0) {
            document.getElementById('loading-state').style.display = 'none';
            document.getElementById('empty-state').style.display = 'block';
            return;
        }

        // Render & Show
        renderAccordion();
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('content-state').style.display = 'block';

    } catch (err) {
        console.error('💥 Fatal error:', err);
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('error-state').style.display = 'block';
        document.getElementById('error-message').textContent = err.message;
    }
}

document.getElementById('filterKecamatan').addEventListener('change', function(e) {
    currentFilter = e.target.value;
    const btn = document.getElementById('btnTambahDesa');
    const baseUrl = "{{ route('admin.desa.create') }}";
    btn.href = currentFilter ? `${baseUrl}?kecamatan=${currentFilter}` : baseUrl;
    renderAccordion();
});

function renderAccordion() {
    const container = document.getElementById('accordion-container');
    const noDataMsg = document.getElementById('no-data-message');
    
    let filteredKec = allKecamatans.filter(k => (desasData[k.id] || []).length > 0);
    
    if (currentFilter) {
        filteredKec = filteredKec.filter(k => k.id == currentFilter);
    }

    if (filteredKec.length === 0) {
        noDataMsg.style.display = 'block';
        container.innerHTML = '';
        return;
    } else {
        noDataMsg.style.display = 'none';
    }

    container.innerHTML = filteredKec.map((kec) => {
        const desas = desasData[kec.id] || [];
        const count = desas.length;
        const totalPenduduk = desas.reduce((s, d) => s + (parseInt(d.population) || 0), 0);
        const totalKK = desas.reduce((s, d) => s + (parseInt(d.households) || 0), 0);

        // Generate desktop table rows
        const desktopRows = desas.map(d => `
            <tr style="border-bottom:1px solid rgba(0,0,0,0.06);transition:background 0.2s" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                <td style="padding:0.875rem 1rem">
                    ${d.image 
                        ? `<img src="${d.image}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;background:#f8fafc" onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">`
                        : `<div style="width:40px;height:40px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#94a3b8">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                           </div>`
                    }
                </td>
                <td style="padding:0.875rem 1rem;font-weight:600;color:var(--text-dark)">${d.name}</td>
                <td style="padding:0.875rem 1rem;color:var(--text-muted)">${(d.population||0).toLocaleString('id-ID')}</td>
                <td style="padding:0.875rem 1rem;color:var(--text-muted)">${(d.households||0).toLocaleString('id-ID')}</td>
                <td style="padding:0.875rem 1rem">
                    ${d.is_active 
                        ? `<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            Aktif
                           </span>`
                        : `<span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(148,163,184,0.1);color:#475569">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
                            Nonaktif
                           </span>`
                    }
                </td>
                <td style="padding:0.875rem 1rem;text-align:right">
                    <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex">
                        <a href="/admin/desa/${d.id}/edit" class="btn-edit" title="Edit" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <button onclick="deleteDesa('${d.id}', '${d.name.replace(/'/g, "\\'")}')" class="btn-del" title="Hapus" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;border:none;cursor:pointer" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        // Generate mobile cards
        const mobileCards = desas.map(d => `
            <div class="desa-card">
                <div class="desa-card-header">
                    ${d.image 
                        ? `<img src="${d.image}" class="desa-card-photo" onerror="this.src='https://via.placeholder.com/50x50?text=No+Image'">`
                        : `<div class="desa-card-photo" style="display:flex;align-items:center;justify-content:center;color:#94a3b8">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                           </div>`
                    }
                    <div class="desa-card-info">
                        <div class="desa-card-name">${d.name}</div>
                        ${d.is_active 
                            ? `<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                Aktif
                               </span>`
                            : `<span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:20px;font-size:0.7rem;font-weight:600;background:rgba(148,163,184,0.1);color:#475569">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="12" y1="12" x2="16" y2="12"/></svg>
                                Nonaktif
                               </span>`
                        }
                    </div>
                </div>
                <div class="desa-card-meta">
                    <div>
                        <span style="color:var(--text-muted);font-size:0.75rem">Penduduk</span>
                        <div style="font-weight:600;color:var(--text-dark)">${(d.population||0).toLocaleString('id-ID')}</div>
                    </div>
                    <div>
                        <span style="color:var(--text-muted);font-size:0.75rem">KK</span>
                        <div style="font-weight:600;color:var(--text-dark)">${(d.households||0).toLocaleString('id-ID')}</div>
                    </div>
                </div>
                <div class="desa-card-actions">
                    <a href="/admin/desa/${d.id}/edit" title="Edit" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button onclick="deleteDesa('${d.id}', '${d.name.replace(/'/g, "\\'")}')" title="Hapus" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
            </div>
        `).join('');

        return `
        <div style="border-bottom:1px solid rgba(0,0,0,0.06)">
            <button class="accordion-header-btn" onclick="toggleKecamatan('${kec.id}')" 
                    style="width:100%;padding:1.25rem 1.5rem;display:flex;justify-content:space-between;align-items:center;background:#f8fafc;border:none;cursor:pointer;text-align:left;font-weight:600;color:var(--text-dark);font-size:1rem;transition:all 0.2s"
                    onmouseover="this.style.background='#f1f5f9'"
                    onmouseout="this.style.background='#f8fafc'">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>${kec.name}</span>
                </div>
                <div style="display:flex;align-items:center;gap:1rem">
                    <span style="background:rgba(20,184,166,0.1);color:var(--primary);padding:0.35rem 0.85rem;border-radius:20px;font-size:0.8rem;font-weight:600">
                        ${count} Desa
                    </span>
                    <svg id="icon-${kec.id}" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition:transform 0.2s"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </button>
            
            <div id="content-${kec.id}" style="display:none;background:#fff">
                <div class="kec-stats" style="display:flex;gap:2rem;margin:0;padding:1rem 1.5rem;background:#f8fafc;border-bottom:1px solid rgba(0,0,0,0.06);font-size:0.9rem;flex-wrap:wrap">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Total Penduduk: <strong style="color:var(--primary)">${totalPenduduk.toLocaleString('id-ID')}</strong></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Total KK: <strong style="color:var(--primary)">${totalKK.toLocaleString('id-ID')}</strong></span>
                    </div>
                </div>
                
                {{-- Desktop Table View --}}
                <div class="desktop-table-view" style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:700px">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Foto</th>
                                <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Nama Desa</th>
                                <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Penduduk</th>
                                <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">KK</th>
                                <th style="padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Status</th>
                                <th style="padding:0.875rem 1rem;text-align:right;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${desktopRows}
                        </tbody>
                    </table>
                </div>
                
                {{-- Mobile Card View --}}
                <div class="mobile-card-view" style="padding:1rem">
                    ${mobileCards}
                </div>
            </div>
        </div>`;
    }).join('');
}

function toggleKecamatan(id) {
    const content = document.getElementById(`content-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    if (!content || !icon) return;
    const isOpen = content.style.display === 'block';
    content.style.display = isOpen ? 'none' : 'block';
    icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}

// Delete dengan Toast.confirm
async function deleteDesa(id, name) {
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Desa <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                {
                    title: 'Hapus Desa?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
            
            if (!confirmed) return;
        } else {
            if (!confirm(`Hapus desa "${name}"?`)) return;
        }
        
        console.log('🗑️ Deleting desa:', id);
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/desa/${id}`;
        form.style.display = 'none';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken || '';
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
        
    } catch (err) { 
        console.error('💥 Delete error:', err);
        const errorMsg = err.message || 'Terjadi kesalahan saat menghapus desa.';
        
        if (typeof Toast !== 'undefined') {
            Toast.error(errorMsg);
        } else {
            alert('❌ ' + errorMsg);
        }
    }
}

document.addEventListener('DOMContentLoaded', initData);
</script>
@endsection