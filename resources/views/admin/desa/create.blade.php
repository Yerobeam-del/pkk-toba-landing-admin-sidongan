@extends('admin.layouts.app')
@section('title', 'Tambah Desa')
@section('page-title', 'Tambah Desa Baru')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .desa-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .desa-header h1 {
        font-size: 1.25rem !important;
    }
    
    .desa-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .form-grid-3 {
        grid-template-columns: 1fr !important;
    }
    
    .form-grid-2 {
        grid-template-columns: 1fr !important;
    }
}
</style>

{{-- Header --}}
<div class="desa-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Tambah Desa</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Tambahkan data desa/kelurahan baru ke dalam sistem</p>
    </div>
    <a href="{{ route('admin.desa.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;flex-shrink:0">← Kembali</a>
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.desa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Kecamatan Select --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Kecamatan *</label>
            <div style="position:relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                    style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;z-index:10">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                
                <select name="kecamatan_id" id="kecamatanSelect" class="form-control" required 
                        style="width:100%;padding:0.75rem 2.5rem 0.75rem 3rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.85rem center;background-size:18px;cursor:pointer;line-height:1.5">
                    <option value="">Memuat data kecamatan...</option>
                </select>
            </div>
            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Data kecamatan dari database</small>
        </div>

        {{-- Desa Select --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Desa / Kelurahan *</label>
            <div style="position:relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                     style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none;z-index:10">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                
                <select name="desa_code" id="desaSelect" class="form-control" required disabled
                        style="width:100%;padding:0.7rem 2.5rem 0.7rem 2.8rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.85rem center;background-size:18px;cursor:not-allowed;opacity:0.6;line-height:1.5">
                    <option value="">Pilih Kecamatan Terlebih Dahulu</option>
                </select>
            </div>
            <input type="hidden" name="desa_name" id="desaNameInput">
            <small id="desaHelp" style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Data desa otomatis dari API wilayah.id</small>
            <small id="desaError" style="color:#ef4444;display:none;margin-top:0.25rem;font-size:0.85rem"></small>
        </div>

        {{-- Population, Households & Sort Order --}}
        <div class="form-grid-3" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Jumlah Penduduk</label>
                <input type="number" name="population" class="form-control" value="{{ old('population', 0) }}" min="0" placeholder="0">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Total penduduk desa</small>
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Jumlah KK</label>
                <input type="number" name="households" class="form-control" value="{{ old('households', 0) }}" min="0" placeholder="0">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Kepala keluarga</small>
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Urutan Tampil</label>
                <input type="number" name="sort_order" id="sortOrderInput" class="form-control" value="{{ old('sort_order') }}" min="0" readonly style="background:#f8fafc;cursor:not-allowed">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Otomatis terisi</small>
            </div>
        </div>

        {{-- Image Upload --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Foto Desa</label>
            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
            
            <div id="imagePreview" style="margin-top:1rem;display:none">
                <img id="previewImg" src="" style="width:100%;max-width:200px;height:auto;border-radius:12px;object-fit:cover;background:#f8fafc">
                <span style="display:block;font-size:0.8rem;color:var(--text-muted);margin-top:0.4rem">Preview Foto</span>
            </div>
            
            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">
                Format: JPG/PNG/WebP, maksimal 2MB
            </small>
        </div>

        {{-- Is Active Checkbox - Custom Style --}}
        <div style="margin-bottom:2rem">
            <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px;transition:all 0.2s;width:fit-content" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="display:none">
                <div id="isActiveBox" style="width:22px;height:22px;border:2px solid #cbd5e1;border-radius:6px;background:#fff;transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg id="isActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1)">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span style="font-weight:600;color:#334155;font-size:0.9rem;user-select:none">Tampilkan di Website</span>
            </label>
            <small style="color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.85rem">
                Jika dicentang, desa akan tampil di website. Jika tidak, desa disembunyikan sementara.
            </small>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.04)">
            <a href="{{ route('admin.desa.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan Desa
            </button>
        </div>
    </form>
</div>

<script>
const kecamatanSelect = document.getElementById('kecamatanSelect');
const desaSelect = document.getElementById('desaSelect');
const desaNameInput = document.getElementById('desaNameInput');
const desaError = document.getElementById('desaError');
const desaHelp = document.getElementById('desaHelp');
const sortOrderInput = document.getElementById('sortOrderInput');

// 1. Load Max Sort Order
async function loadMaxSortOrder() {
    try {
        const res = await fetch('/api/v1/desas/max-sort-order');
        const json = await res.json();
        
        if (json.success) {
            const maxOrder = json.data?.max_sort_order ?? 0;
            sortOrderInput.value = maxOrder + 1;
        } else {
            sortOrderInput.value = 1;
        }
    } catch (error) {
        console.error('Error loading max sort order:', error);
        sortOrderInput.value = 1;
    }
}

// 2. Load Kecamatan from our API
document.addEventListener('DOMContentLoaded', async () => {
    await loadMaxSortOrder();
    
    try {
        const res = await fetch('/api/v1/kecamatans');
        const json = await res.json();
        
        if (!json.success) throw new Error(json.message || 'Gagal memuat kecamatan');
        if (!json.data || json.data.length === 0) throw new Error('Data kecamatan kosong');
        
        kecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
        
        json.data.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.dataset.wilayahCode = k.code;
            opt.textContent = k.name;
            kecamatanSelect.appendChild(opt);
        });
        
    } catch (error) {
        console.error('Error loading kecamatan:', error);
        kecamatanSelect.innerHTML = '<option value="">Gagal memuat kecamatan</option>';
    }
});

// 3. Load Desa when Kecamatan changes
kecamatanSelect.addEventListener('change', async function() {
    const selectedOption = this.options[this.selectedIndex];
    const wilayahCode = selectedOption.dataset.wilayahCode;
    const kecName = selectedOption.textContent;
    
    desaSelect.innerHTML = '<option value="">Memuat data desa...</option>';
    desaSelect.disabled = true;
    desaSelect.style.opacity = '0.6';
    desaSelect.style.cursor = 'not-allowed';
    desaNameInput.value = '';
    desaError.style.display = 'none';
    desaError.textContent = '';
    desaHelp.style.display = 'block';
    desaHelp.textContent = 'Data desa otomatis dari API wilayah.id';
    
    if (!wilayahCode) {
        desaSelect.innerHTML = '<option value="">Pilih Kecamatan Terlebih Dahulu</option>';
        return;
    }
    
    try {
        const proxyUrl = `/api/v1/wilayah/proxy/desa/${wilayahCode}`;
        const res = await fetch(proxyUrl);
        const json = await res.json();
        
        if (!json.success) {
            throw new Error(json.message || 'Gagal mengambil data desa');
        }
        
        if (!Array.isArray(json.data)) {
            throw new Error('Format data tidak valid');
        }
        
        if (json.data.length === 0) {
            desaSelect.innerHTML = '<option value="">Tidak ada desa di kecamatan ini</option>';
            desaHelp.style.display = 'none';
            return;
        }
        
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        desaSelect.disabled = false;
        desaSelect.style.opacity = '1';
        desaSelect.style.cursor = 'pointer';
        
        json.data.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.code;
            opt.textContent = d.name;
            desaSelect.appendChild(opt);
        });
        
        desaHelp.textContent = `${json.data.length} desa ditemukan di ${kecName}`;
        
    } catch (error) {
        console.error('Error loading desa:', error);
        desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
        desaSelect.disabled = true;
        desaSelect.style.opacity = '0.6';
        
        desaError.style.display = 'block';
        desaError.textContent = 'Gagal: ' + error.message;
        desaHelp.style.display = 'none';
    }
});

// 4. Update hidden input when desa is selected
desaSelect.addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    desaNameInput.value = this.value ? selected.textContent : '';
});

// 5. Image preview
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar terlalu besar. Maksimal 2MB.');
            e.target.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Checkbox animation handler
function updateCheckboxStyle(boxId, checkId, isChecked) {
    const box = document.getElementById(boxId);
    const check = document.getElementById(checkId);
    
    if (!box || !check) return;
    
    if (isChecked) {
        box.style.background = 'linear-gradient(135deg, #14b8a6, #0d9488)';
        box.style.borderColor = '#14b8a6';
        box.style.boxShadow = '0 2px 8px rgba(20,184,166,0.3)';
        check.style.opacity = '1';
        check.style.transform = 'scale(1)';
    } else {
        box.style.background = '#fff';
        box.style.borderColor = '#cbd5e1';
        box.style.boxShadow = 'none';
        check.style.opacity = '0';
        check.style.transform = 'scale(0.5)';
    }
}

// Initialize checkbox state
const isActiveCheckbox = document.getElementById('isActive');
if (isActiveCheckbox) {
    updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
    
    isActiveCheckbox.addEventListener('change', function() {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
    });
}
</script>
@endsection