@extends('admin.layouts.app')
@section('title', 'Edit Aplikasi')
@section('page-title', 'Edit Aplikasi')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .aplikasi-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .aplikasi-header h1 {
        font-size: 1.25rem !important;
    }
    
    .aplikasi-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    .form-grid-2 {
        grid-template-columns: 1fr !important;
    }
    
    .feature-item {
        flex-direction: column !important;
    }
    
    .feature-item button {
        width: 100% !important;
        margin-top: 0.5rem !important;
    }
}
</style>

{{-- Header --}}
<div class="aplikasi-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Edit Aplikasi</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Perbarui data aplikasi yang sudah ada</p>
    </div>
    <a href="{{ route('admin.aplikasi.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;flex-shrink:0">← Kembali</a>
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.aplikasi.update', $aplikasi) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- Nama Lengkap --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Nama Aplikasi Lengkap *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $aplikasi->name) }}" required placeholder="Contoh: SIEDA - Sistem Informasi E-Dasawisma">
        </div>

        {{-- Short Name & Category --}}
        <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Nama Singkat *</label>
                <input type="text" name="short_name" id="shortNameInput" class="form-control" value="{{ old('short_name', $aplikasi->short_name) }}" required placeholder="Contoh: SIEDA" style="text-transform:uppercase" maxlength="50">
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Kategori *</label>
                <select name="category" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="layanan" {{ old('category', $aplikasi->category) == 'layanan' ? 'selected' : '' }}>Layanan</option>
                    <option value="aplikasi" {{ old('category', $aplikasi->category) == 'aplikasi' ? 'selected' : '' }}>Aplikasi</option>
                </select>
            </div>
        </div>

        {{-- Description --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Deskripsi Lengkap *</label>
            <textarea name="description" id="description" class="form-control" rows="4" required maxlength="1000" placeholder="Deskripsi detail tentang aplikasi, fitur, dan fungsionalitas">{{ old('description', $aplikasi->description) }}</textarea>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.5rem">
                <small style="color:var(--text-muted);font-size:0.8rem">Deskripsi akan ditampilkan di landing page</small>
                <small id="charCount" style="font-size:0.85rem;font-weight:600;color:var(--text-muted)">
                    <span id="currentChars">0</span> / 1000 karakter
                </small>
            </div>
            <div id="charWarning" style="display:none;margin-top:0.5rem;padding:0.5rem;background:#fef3c7;border-radius:6px;font-size:0.8rem;color:#92400e">
                <strong>Peringatan:</strong> Deskripsi hampir mencapai batas maksimal
            </div>
        </div>

        {{-- Features --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Poin-Poin Fitur Aplikasi</label>
            <small style="color:var(--text-muted);display:block;margin-bottom:1rem;font-size:0.8rem">Tambahkan 2-5 poin keunggulan/fitur aplikasi</small>
            
            <div id="features-container">
                @php
                    $features = old('features', $aplikasi->features ?? [
                        'Terintegrasi dengan data PKK',
                        'Akses real-time 24/7',
                        'Keamanan data terjamin'
                    ]);
                @endphp
                
                @foreach($features as $index => $feature)
                <div class="feature-item" style="display:flex;gap:0.75rem;margin-bottom:0.75rem">
                    <input type="text" name="features[]" class="form-control" value="{{ $feature }}" placeholder="Masukkan poin fitur" required>
                    <button type="button" class="btn" onclick="removeFeature(this)" 
                            style="background:#f8fafc;color:#ef4444;padding:0.6rem;min-width:40px;border-radius:8px" 
                            title="Hapus poin" 
                            {{ count($features) <= 2 ? 'disabled' : '' }}>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </button>
                </div>
                @endforeach
            </div>
            
            <button type="button" id="add-feature-btn" class="btn" onclick="addFeature()" 
                    style="margin-top:0.5rem;width:100%;background:#f8fafc;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;{{ count($features) >= 5 ? 'display:none' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Tambah Poin Fitur
            </button>
            
            <small id="features-warning" style="color:#ef4444;display:none;margin-top:0.5rem;font-size:0.85rem">
                ⚠️ Maksimal 5 poin fitur
            </small>
        </div>

        {{-- Status & URL --}}
        <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Status Aplikasi *</label>
                <select name="status" class="form-control" required onchange="toggleUrlField(this.value)">
                    <option value="active" {{ old('status', $aplikasi->status) == 'active' ? 'selected' : '' }}>Aktif - Siap Digunakan</option>
                    <option value="maintenance" {{ old('status', $aplikasi->status) == 'maintenance' ? 'selected' : '' }}>Dalam Maintenance - Sedang Perbaikan</option>
                    <option value="development" {{ old('status', $aplikasi->status) == 'development' ? 'selected' : '' }}>Dalam Pengembangan - Coming Soon</option>
                </select>
            </div>
            <div id="urlField">
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">URL Aplikasi</label>
                <input type="url" name="url" class="form-control" value="{{ old('url', $aplikasi->url !== '#' ? $aplikasi->url : '') }}" {{ $aplikasi->status == 'development' ? 'disabled' : '' }} placeholder="https://example.com">
            </div>
        </div>

        {{-- Icon & Sort Order --}}
        <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Icon/Logo Aplikasi</label>
                <input type="file" name="icon" class="form-control" accept="image/*">
                
                @if($aplikasi->icon)
                <div style="margin-top:1rem;display:flex;align-items:center;gap:1rem">
                    <img src="{{ asset('storage/'.$aplikasi->icon) }}" style="width:80px;height:80px;border-radius:12px;object-fit:cover;background:#f8fafc">
                    <span style="font-size:0.85rem;color:var(--text-muted)">Icon saat ini <small style="color:#94a3b8">(upload baru untuk mengganti)</small></span>
                </div>
                @endif
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $aplikasi->sort_order) }}" min="0">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Semakin kecil angka, semakin awal tampil</small>
            </div>
        </div>

        {{-- Is Active Checkbox - Custom Style --}}
        <div style="margin-bottom:2rem">
            <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px;transition:all 0.2s;width:fit-content" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                <input type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $aplikasi->is_active) ? 'checked' : '' }} style="display:none">
                <div id="isActiveBox" style="width:22px;height:22px;border:2px solid #cbd5e1;border-radius:6px;background:#fff;transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg id="isActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1)">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span style="font-weight:600;color:#334155;font-size:0.9rem;user-select:none">Tampilkan di Website</span>
            </label>
            <small style="color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.85rem">
                Jika dicentang, aplikasi akan tampil di landing page. Jika tidak, aplikasi disembunyikan sementara.
            </small>
        </div>

        @php
            // Hitung berapa aplikasi yang sudah tampil di Beranda (kecuali aplikasi yang sedang diedit)
            $berandaCount = \App\Models\Application::where('show_in_quick_access', true)
                ->where('is_active', true)
                ->where('status', 'active')
                ->where('id', '!=', $aplikasi->id)  // Kecuali aplikasi yang sedang diedit
                ->count();
            $berandaFull = $berandaCount >= 2 && !$aplikasi->show_in_quick_access;
        @endphp

        {{-- Visibility Settings --}}
        <div style="margin-bottom:2rem">
            <label style="font-weight:600;display:block;margin-bottom:1rem;font-size:0.9rem">Tampilkan Aplikasi Di</label>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(250px, 1fr));gap:0.75rem">
                
                {{-- Floating Button --}}
                <label class="vis-label" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px;transition:all 0.2s;border:2px solid transparent" 
                    onmouseover="this.style.borderColor='var(--primary)'" 
                    onmouseout="this.style.borderColor='transparent'">
                    <input type="checkbox" name="show_in_floating" value="1" {{ old('show_in_floating', $aplikasi->show_in_floating) ? 'checked' : '' }} style="display:none" class="vis-checkbox">
                    <div class="vis-check-box" style="width:22px;height:22px;border:2px solid #cbd5e1;border-radius:6px;background:#fff;transition:all 0.25s;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span style="font-weight:600;color:#334155;font-size:0.9rem;display:block">Floating Button</span>
                        <span style="font-size:0.75rem;color:var(--text-muted)">Tombol mengambang di pojok kanan bawah</span>
                    </div>
                </label>

                {{-- Footer --}}
                <label class="vis-label" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px;transition:all 0.2s;border:2px solid transparent" 
                    onmouseover="this.style.borderColor='var(--primary)'" 
                    onmouseout="this.style.borderColor='transparent'">
                    <input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', $aplikasi->show_in_footer) ? 'checked' : '' }} style="display:none" class="vis-checkbox">
                    <div class="vis-check-box" style="width:22px;height:22px;border:2px solid #cbd5e1;border-radius:6px;background:#fff;transition:all 0.25s;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span style="font-weight:600;color:#334155;font-size:0.9rem;display:block">Footer</span>
                        <span style="font-size:0.75rem;color:var(--text-muted)">Quick access di bagian bawah halaman</span>
                    </div>
                </label>

                {{-- Beranda - Dengan Validasi --}}
                <label class="vis-label" style="display:flex;align-items:center;gap:0.75rem;cursor:{{ $berandaFull ? 'not-allowed' : 'pointer' }};padding:0.75rem 1rem;background:{{ $berandaFull ? '#f1f5f9' : '#f8fafc' }};border-radius:10px;transition:all 0.2s;border:2px solid {{ $berandaFull ? '#e2e8f0' : 'transparent' }};opacity:{{ $berandaFull ? '0.6' : '1' }}" 
                    @if(!$berandaFull) onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='transparent'" @endif>
                    <input type="checkbox" name="show_in_quick_access" value="1" {{ old('show_in_quick_access', $aplikasi->show_in_quick_access) && !$berandaFull ? 'checked' : '' }} {{ $berandaFull ? 'disabled' : '' }} style="display:none" class="vis-checkbox">
                    <div class="vis-check-box" style="width:22px;height:22px;border:2px solid {{ $berandaFull ? '#cbd5e1' : '#cbd5e1' }};border-radius:6px;background:#fff;transition:all 0.25s;display:flex;align-items:center;justify-content:center;flex-shrink:0;{{ $berandaFull ? 'opacity:0.5' : '' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span style="font-weight:600;color:#334155;font-size:0.9rem;display:block">Beranda</span>
                        <span style="font-size:0.75rem;color:var(--text-muted)">Quick access di halaman utama</span>
                        @if($berandaFull)
                            <br><span style="font-size:0.7rem;color:#ef4444;font-weight:600">Sudah mencapai batas (2)</span>
                        @endif
                    </div>
                </label>

            </div>
            <small style="color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.8rem">
                Pilih di mana aplikasi ini akan ditampilkan. Aplikasi harus aktif terlebih dahulu.
            </small>
            <small style="color:var(--primary);display:block;margin-top:0.25rem;font-size:0.8rem;font-weight:600">
                Catatan: Maksimal 2 aplikasi bisa tampil di Beranda.
                @if($berandaFull)
                    <br><span style="color:#ef4444">Saat ini sudah ada {{ $berandaCount }} aplikasi yang tampil di Beranda.</span>
                @endif
            </small>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.04)">
            <a href="{{ route('admin.aplikasi.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Update Aplikasi
            </button>
        </div>
    </form>
    
    {{-- Validation Errors --}}
    @if($errors->any())
    <div style="margin-top:1.5rem;padding:1rem;background:#fef2f2;border-radius:10px;color:#991b1b">
        <strong style="display:block;margin-bottom:0.5rem;font-weight:600">Periksa kembali input berikut:</strong>
        <ul style="padding-left:1.25rem;margin:0;font-size:0.9rem">
            @foreach($errors->all() as $err) <li style="margin-bottom:0.25rem">{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif
</div>

<script>
// Feature functions
function addFeature() {
    const container = document.getElementById('features-container');
    const currentCount = container.querySelectorAll('.feature-item').length;
    
    if (currentCount >= 5) {
        document.getElementById('features-warning').style.display = 'block';
        return;
    }
    
    document.getElementById('features-warning').style.display = 'none';
    
    const div = document.createElement('div');
    div.className = 'feature-item';
    div.style.cssText = 'display:flex;gap:0.75rem;margin-bottom:0.75rem';
    div.innerHTML = `
        <input type="text" name="features[]" class="form-control" placeholder="Masukkan poin fitur" required>
        <button type="button" class="btn" onclick="removeFeature(this)" 
                style="background:#f8fafc;color:#ef4444;padding:0.6rem;min-width:40px;border-radius:8px" title="Hapus poin">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
    updateDeleteButtons();
}

function removeFeature(btn) {
    const container = document.getElementById('features-container');
    const currentCount = container.querySelectorAll('.feature-item').length;
    
    if (currentCount <= 2) {
        alert('Minimal harus ada 2 poin fitur');
        return;
    }
    
    btn.closest('.feature-item').remove();
    updateDeleteButtons();
}

function updateDeleteButtons() {
    const container = document.getElementById('features-container');
    const items = container.querySelectorAll('.feature-item');
    const addBtn = document.getElementById('add-feature-btn');
    
    items.forEach(item => {
        const deleteBtn = item.querySelector('button[onclick="removeFeature(this)"]');
        if (items.length <= 2) {
            deleteBtn.disabled = true;
            deleteBtn.style.opacity = '0.4';
            deleteBtn.style.cursor = 'not-allowed';
        } else {
            deleteBtn.disabled = false;
            deleteBtn.style.opacity = '1';
            deleteBtn.style.cursor = 'pointer';
        }
    });
    
    if (items.length >= 5) {
        addBtn.style.display = 'none';
    } else {
        addBtn.style.display = 'inline-flex';
    }
}

function toggleUrlField(status) {
    const urlField = document.getElementById('urlField');
    const urlInput = urlField.querySelector('input');
    
    if (status === 'development') {
        urlField.style.opacity = '0.5';
        urlInput.disabled = true;
        urlInput.value = '#';
    } else {
        urlField.style.opacity = '1';
        urlInput.disabled = false;
        if (urlInput.value === '#') urlInput.value = '';
    }
}

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

// Init on load
document.addEventListener('DOMContentLoaded', () => {
    const statusSelect = document.querySelector('select[name="status"]');
    if (statusSelect) toggleUrlField(statusSelect.value);
    updateDeleteButtons();

    // Initialize checkbox state
    const isActiveCheckbox = document.getElementById('isActive');
    if (isActiveCheckbox) {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
        
        isActiveCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
        });
    }
});

// Auto-convert short_name ke UPPERCASE saat user mengetik
const shortNameInput = document.getElementById('shortNameInput');
if (shortNameInput) {
    shortNameInput.addEventListener('input', function(e) {
        // Simpan posisi cursor
        const cursorPosition = this.selectionStart;
        const oldValue = this.value;
        
        // Konversi ke uppercase
        this.value = this.value.toUpperCase();
        const newValue = this.value;
        
        // Kembalikan posisi cursor
        const offset = newValue.length - oldValue.length;
        this.setSelectionRange(cursorPosition + offset, cursorPosition + offset);
    });
}

// Character Counter untuk Description
const descriptionTextarea = document.getElementById('description');
const currentCharsSpan = document.getElementById('currentChars');
const charWarning = document.getElementById('charWarning');
const charCount = document.getElementById('charCount');

if (descriptionTextarea) {
    updateCharCounter();
    descriptionTextarea.addEventListener('input', updateCharCounter);
}

function updateCharCounter() {
    const currentLength = descriptionTextarea.value.length;
    const maxLength = 1000;
    
    currentCharsSpan.textContent = currentLength;
    
    if (currentLength >= 1000) {
        // Sudah mencapai batas maksimal
        charCount.style.color = '#ef4444';
        charWarning.style.display = 'block';
        charWarning.innerHTML = '<strong>Peringatan:</strong> Deskripsi mencapai batas maksimal';
    } else if (currentLength >= 950) {
        charCount.style.color = '#ef4444';
        charWarning.style.display = 'block';
        charWarning.innerHTML = '<strong>Peringatan:</strong> Deskripsi hampir mencapai batas maksimal (' + (maxLength - currentLength) + ' karakter lagi)';
    } else if (currentLength >= 900) {
        charCount.style.color = '#d97706';
        charWarning.style.display = 'block';
        charWarning.innerHTML = '<strong>Peringatan:</strong> Deskripsi hampir mencapai batas maksimal (' + (maxLength - currentLength) + ' karakter lagi)';
    } else if (currentLength >= 800) {
        charCount.style.color = '#f59e0b';
        charWarning.style.display = 'none';
    } else {
        charCount.style.color = 'var(--text-muted)';
        charWarning.style.display = 'none';
    }
}

// Visibility checkbox handler
document.querySelectorAll('.vis-checkbox').forEach(function(checkbox) {
    const box = checkbox.parentElement.querySelector('.vis-check-box');
    
    function updateVisStyle() {
        const svg = box.querySelector('svg');
        if (checkbox.checked) {
            box.style.background = 'linear-gradient(135deg, #14b8a6, #0d9488)';
            box.style.borderColor = '#14b8a6';
            svg.style.opacity = '1';
            svg.style.transform = 'scale(1)';
        } else {
            box.style.background = '#fff';
            box.style.borderColor = '#cbd5e1';
            svg.style.opacity = '0';
            svg.style.transform = 'scale(0.5)';
        }
    }
    
    updateVisStyle();
    checkbox.addEventListener('change', updateVisStyle);
});
</script>

@endsection