/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Index Desa — dipisah dari HTML (desa/index.blade.php)
 * URL tambah dibaca dari data-base-url pada #filterKecamatan.
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


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
    const baseUrl = document.getElementById('filterKecamatan')?.dataset.baseUrl || '';
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
                        : `<div style="width:40px;height:40px;border-radius:8px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:var(--text-muted)">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                           </div>`
                    }
                </td>
                <td style="padding:0.875rem 1rem;font-weight:600;color:var(--text-dark)">${d.name}</td>
                <td style="padding:0.875rem 1rem;color:var(--text-muted)">${(d.population||0).toLocaleString('id-ID')}</td>
                <td style="padding:0.875rem 1rem;color:var(--text-muted)">${(d.households||0).toLocaleString('id-ID')}</td>
                <td style="padding:0.875rem 1rem">
                    ${d.is_active 
                        ? `<span class="u-a57">
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
                        <a href="/admin/desa/${d.id}/edit" class="btn-edit u-a18" title="Edit" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <button onclick="deleteDesa('${d.id}', '${d.name.replace(/'/g, "\\'")}')" class="btn-del" title="Hapus" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:var(--text-muted);border-radius:6px;transition:all 0.2s;border:none;cursor:pointer" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'">
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
                        : `<div class="desa-card-photo" style="display:flex;align-items:center;justify-content:center;color:var(--text-muted)">
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
                        <div class="u-a30">${(d.population||0).toLocaleString('id-ID')}</div>
                    </div>
                    <div>
                        <span style="color:var(--text-muted);font-size:0.75rem">KK</span>
                        <div class="u-a30">${(d.households||0).toLocaleString('id-ID')}</div>
                    </div>
                </div>
                <div class="desa-card-actions">
                    <a href="/admin/desa/${d.id}/edit" title="Edit" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </a>
                    <button onclick="deleteDesa('${d.id}', '${d.name.replace(/'/g, "\\'")}')" title="Hapus" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='var(--text-muted)'">
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
                <div class="u-flex-center-gap-3">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>${kec.name}</span>
                </div>
                <div class="u-flex-center-gap-4">
                    <span style="background:rgba(20,184,166,0.1);color:var(--primary);padding:0.35rem 0.85rem;border-radius:20px;font-size:0.8rem;font-weight:600">
                        ${count} Desa
                    </span>
                    <svg id="icon-${kec.id}" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="transition:transform 0.2s"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </button>
            
            <div id="content-${kec.id}" style="display:none;background:#fff">
                <div class="kec-stats" style="display:flex;gap:2rem;margin:0;padding:1rem 1.5rem;background:#f8fafc;border-bottom:1px solid rgba(0,0,0,0.06);font-size:0.9rem;flex-wrap:wrap">
                    <div class="u-flex-center-gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>Total Penduduk: <strong class="u-a52">${totalPenduduk.toLocaleString('id-ID')}</strong></span>
                    </div>
                    <div class="u-flex-center-gap-2">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Total KK: <strong class="u-a52">${totalKK.toLocaleString('id-ID')}</strong></span>
                    </div>
                </div>
                
                {{-- Desktop Table View --}}
                <div class="desktop-table-view" style="overflow-x:auto">
                    <table style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:700px">
                        <thead style="background:#f8fafc">
                            <tr>
                                <th class="u-th">Foto</th>
                                <th class="u-th">Nama Desa</th>
                                <th class="u-th">Penduduk</th>
                                <th class="u-th">KK</th>
                                <th class="u-th">Status</th>
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
        // Toast dimuat global di layout, jadi tidak perlu cadangan confirm() bawaan
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
        Toast.error(err.message || 'Terjadi kesalahan saat menghapus desa.');
    }
}

document.addEventListener('DOMContentLoaded', initData);

