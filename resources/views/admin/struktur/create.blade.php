@extends('admin.layouts.app')
@section('title', 'Tambah Anggota Struktur')
@section('page-title', 'Tambah Anggota')

@section('content')
<style>
/* ==========================================
   MOBILE RESPONSIVE IMPROVEMENTS
   ========================================== */

@media (max-width: 768px) {
    /* Header - Mobile */
    .struktur-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .struktur-header h1 {
        font-size: 1.25rem !important;
    }
    
    .struktur-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    
    /* Form Grid - Mobile */
    .form-grid-2 {
        grid-template-columns: 1fr !important;
        gap: 1.25rem !important;
    }
    
    /* Form Controls - Mobile */
    .form-control {
        font-size: 16px !important; /* Prevent zoom on iOS */
        padding: 0.75rem !important;
        min-height: 44px;
    }
    
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="file"],
    select,
    textarea {
        font-size: 16px !important;
        min-height: 44px;
    }
    
    textarea {
        min-height: 100px !important;
        resize: vertical;
    }
    
    /* Labels - Mobile */
    label {
        font-size: 0.9rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    /* Helper Text - Mobile */
    small {
        font-size: 0.75rem !important;
        line-height: 1.4 !important;
    }
    
    /* File Upload - Mobile */
    input[type="file"] {
        padding: 0.75rem !important;
        border: 2px dashed #e2e8f0 !important;
        border-radius: 8px !important;
        background: #f8fafc !important;
        cursor: pointer;
    }
    
    /* Photo Preview - Mobile */
    #previewContainer {
        margin-top: 1rem !important;
    }
    
    #previewContainer > div {
        flex-direction: column !important;
        align-items: center !important;
        text-align: center !important;
        gap: 1rem !important;
    }
    
    #photoPreview {
        width: 120px !important;
        height: 120px !important;
    }
    
    /* Action Buttons - Mobile */
    div[style*="justify-content:flex-end"] {
        flex-direction: column-reverse !important;
        gap: 0.75rem !important;
    }
    
    div[style*="justify-content:flex-end"] .btn,
    div[style*="justify-content:flex-end"] button {
        width: 100% !important;
        padding: 0.875rem 1.5rem !important;
        font-size: 1rem !important;
        font-weight: 600 !important;
        justify-content: center !important;
    }
    
    /* Card Padding - Mobile */
    .card {
        padding: 1.25rem !important;
    }
    
    /* Form Sections - Mobile */
    div[style*="margin-bottom"] {
        margin-bottom: 1.25rem !important;
    }
}

/* ==========================================
   TOUCH-FRIENDLY IMPROVEMENTS
   ========================================== */

@media (max-width: 768px) {
    /* Larger touch targets */
    button, 
    a, 
    input[type="file"],
    select,
    .btn {
        min-height: 44px;
    }
    
    /* Better spacing */
    .form-grid-2 > div {
        margin-bottom: 0;
    }
    
    /* Select dropdown improvements */
    select {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        padding-right: 3rem !important;
    }
    
    /* Better visual feedback */
    .btn:active,
    button:active {
        transform: scale(0.98) !important;
        opacity: 0.9 !important;
    }
}

/* ==========================================
   CROP MODAL - MOBILE
   ========================================== */

@media (max-width: 768px) {
    #cropModal > div {
        height: 95vh !important;
        border-radius: 12px !important;
    }
    
    #cropModal h3 {
        font-size: 1rem !important;
    }
    
    #cropModal .btn {
        padding: 0.65rem 1rem !important;
        font-size: 0.85rem !important;
    }
    
    #cropModal > div > div:last-child {
        padding: 0.75rem 1rem !important;
    }
}
</style>

{{-- Header --}}
<div class="struktur-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Tambah Anggota</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Tambahkan data anggota baru ke dalam struktur organisasi</p>
    </div>
    <a href="{{ route('admin.struktur.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;flex-shrink:0;padding:0.6rem 1rem;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;transition:all 0.2s" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        <span class="desktop-only">Kembali</span>
    </a>
</div>

{{-- Form Card --}}
<div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1.5rem;">
    <form action="{{ route('admin.struktur.store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
        @csrf
        
        {{-- Group & Position --}}
        <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem;color:#334155">1. Kelompok <span style="color:#ef4444">*</span></label>
                <select name="group" id="groupSelect" class="form-control" required onchange="updatePositions()" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:0.9rem;transition:border-color 0.2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="">-- Pilih Kelompok --</option>
                    <option value="pengurus">Pengurus Inti</option>
                    <option value="pokja1">Pokja I</option>
                    <option value="pokja2">Pokja II</option>
                    <option value="pokja3">Pokja III</option>
                    <option value="pokja4">Pokja IV</option>
                </select>
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem;line-height:1.5">Menentukan bagian struktur tempat anggota ini muncul</small>
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem;color:#334155">2. Jabatan <span style="color:#ef4444">*</span></label>
                <select name="position" id="positionSelect" class="form-control" required style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:0.9rem;transition:border-color 0.2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
                    <option value="">-- Pilih Kelompok Dulu --</option>
                </select>
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem;line-height:1.5">Opsi jabatan menyesuaikan kelompok yang dipilih</small>
            </div>
        </div>

        {{-- Name --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem;color:#334155">3. Nama Lengkap <span style="color:#ef4444">*</span></label>
            <input type="text" name="name" class="form-control" required placeholder="Contoh: INDAH KARUNIA PRATIWI SITUMEANG, SH" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:0.9rem;transition:border-color 0.2s" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        {{-- Description --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem;color:#334155">Deskripsi / Catatan (Opsional)</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Misal: NIP, riwayat singkat, atau catatan internal" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:0.9rem;transition:border-color 0.2s;resize:vertical;min-height:80px" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
        </div>

        {{-- Photo Upload with Crop --}}
        <div style="margin-bottom:2rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem;color:#334155">Foto Anggota</label>
            <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*" onchange="handlePhotoUpload(event)" style="width:100%;padding:0.75rem;border:2px dashed #e2e8f0;border-radius:8px;background:#f8fafc;font-size:0.9rem;cursor:pointer;transition:all 0.2s" onmouseover="this.style.borderColor='var(--primary)';this.style.background='#f0fdfa'" onmouseout="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem;line-height:1.5">JPG/PNG, maksimal 2MB. Klik foto untuk mengatur crop.</small>
            
            {{-- Preview Container --}}
            <div id="previewContainer" style="margin-top:1rem;padding:1rem;background:#f8fafc;border-radius:8px;border:1px solid rgba(0,0,0,0.06)">
                <div style="display:flex;align-items:center;gap:1rem">
                    <img id="photoPreview" style="width:80px;height:80px;border-radius:12px;object-fit:cover;background:#fff;cursor:pointer;display:none;box-shadow:0 2px 8px rgba(0,0,0,0.08)" onclick="openCropModal()">
                    <div style="flex:1;min-width:0">
                        <div style="font-weight:600;font-size:0.9rem;color:#334155;margin-bottom:0.25rem">Belum ada foto</div>
                        <div style="font-size:0.85rem;color:var(--text-muted)">Klik foto untuk atur crop</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden input for cropped image --}}
        <input type="hidden" name="cropped_photo" id="croppedPhoto">

        {{-- Action Buttons --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1.5rem;border-top:1px solid rgba(0,0,0,0.06)">
            <a href="{{ route('admin.struktur.index') }}" class="btn" style="background:#f1f5f9;color:#475569;padding:0.75rem 1.5rem;border-radius:8px;text-decoration:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.5rem" onmouseover="this.style.background='#e2e8f0';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f1f5f9';this.style.transform='translateY(0)'">Batal</a>
            <button type="submit" class="btn btn-primary" id="submitBtn" style="background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;padding:0.75rem 2rem;border:none;border-radius:8px;font-weight:600;font-size:0.9rem;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.5rem;box-shadow:0 4px 12px rgba(20,184,166,0.3)" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(20,184,166,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(20,184,166,0.3)'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                Simpan Data
            </button>
        </div>
    </form>
</div>

{{-- Crop Modal --}}
<div id="cropModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:2000;align-items:center;justify-content:center;padding:1rem;backdrop-filter:blur(4px)">
    <div style="background:#fff;border-radius:16px;max-width:700px;width:100%;height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.3);animation:modalSlideUp 0.3s ease">
        {{-- Header --}}
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-shrink:0;background:#f8fafc">
            <h3 style="margin:0;font-size:1.1rem;font-weight:700;color:#1e293b">Atur Foto Profil</h3>
            <button onclick="closeCropModal()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#94a3b8;padding:0.25rem;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;transition:all 0.2s" onmouseover="this.style.background='#f1f5f9';this.style.color='#ef4444'" onmouseout="this.style.background='none';this.style.color='#94a3b8'">&times;</button>
        </div>
        
        {{-- Image Container (Scrollable) --}}
        <div style="flex:1;overflow:hidden;position:relative;background:#f8fafc;padding:1.5rem;display:flex;align-items:center;justify-content:center">
            <div style="max-height:100%;overflow:auto;display:flex;align-items:center;justify-content:center;width:100%">
                <img id="cropImage" style="max-width:100%;display:block;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
            </div>
        </div>
        
        {{-- Controls (Fixed at bottom) --}}
        <div style="padding:1rem 1.5rem;background:#fff;border-top:1px solid rgba(0,0,0,0.06);flex-shrink:0">
            <div style="display:flex;gap:0.5rem;justify-content:center;margin-bottom:0.75rem;flex-wrap:wrap">
                <button type="button" onclick="rotateImage(-90)" class="btn" style="background:#f8fafc;color:#475569;white-space:nowrap;padding:0.65rem 1rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:0.85rem;transition:all 0.2s" onmouseover="this.style.background='#f1f5f9';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f8fafc';this.style.transform='translateY(0)'">↺ Putar Kiri</button>
                <button type="button" onclick="rotateImage(90)" class="btn" style="background:#f8fafc;color:#475569;white-space:nowrap;padding:0.65rem 1rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:0.85rem;transition:all 0.2s" onmouseover="this.style.background='#f1f5f9';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f8fafc';this.style.transform='translateY(0)'">Putar Kanan ↻</button>
                <button type="button" onclick="resetCrop()" class="btn" style="background:#f8fafc;color:#475569;white-space:nowrap;padding:0.65rem 1rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:0.85rem;transition:all 0.2s" onmouseover="this.style.background='#f1f5f9';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f8fafc';this.style.transform='translateY(0)'">Reset</button>
            </div>
            <div style="text-align:center;font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem">
                Drag untuk geser, scroll untuk zoom
            </div>
            <div style="display:flex;gap:0.75rem;justify-content:flex-end">
                <button type="button" onclick="closeCropModal()" class="btn" style="background:#f1f5f9;color:#475569;white-space:nowrap;padding:0.75rem 1.5rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:0.9rem;transition:all 0.2s" onmouseover="this.style.background='#e2e8f0';this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#f1f5f9';this.style.transform='translateY(0)'">Batal</button>
                <button type="button" onclick="applyCrop()" class="btn btn-primary" style="background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;white-space:nowrap;padding:0.75rem 1.5rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:0.9rem;transition:all 0.2s;box-shadow:0 4px 12px rgba(20,184,166,0.3)" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(20,184,166,0.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(20,184,166,0.3)'">Terapkan Crop</button>
            </div>
        </div>
    </div>
</div>

<script>
// Animation
const style = document.createElement('style');
style.textContent = `
    @keyframes modalSlideUp {
        from { opacity: 0; transform: translateY(20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
`;
document.head.appendChild(style);

// Cropper variables
let cropper = null;
let originalFile = null;
let isSubmitting = false;

const positions = {
    pengurus: [
        'Ketua Pembina', 
        'Ketua TP PKK', 
        'Staf Ahli 1', 
        'Staf Ahli 2', 
        'Sekretaris', 
        'Bendahara', 
        'Ketua I', 
        'Ketua II', 
        'Ketua III', 
        'Ketua IV'
    ],
    pokja1: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota'],
    pokja2: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota'],
    pokja3: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota'],
    pokja4: ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Anggota']
};

function updatePositions() {
    const group = document.getElementById('groupSelect').value;
    const posSelect = document.getElementById('positionSelect');
    posSelect.innerHTML = '<option value="">-- Pilih Jabatan --</option>';
    if (group && positions[group]) {
        positions[group].forEach(pos => {
            const opt = document.createElement('option');
            opt.value = pos;
            opt.textContent = pos;
            posSelect.appendChild(opt);
        });
    }
}

// Handle photo upload
function handlePhotoUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (file.size > 2 * 1024 * 1024) {
        if (typeof Toast !== 'undefined') {
            Toast.error('Ukuran foto terlalu besar. Maksimal 2MB.');
        } else {
            alert('Ukuran foto terlalu besar. Maksimal 2MB.');
        }
        event.target.value = '';
        return;
    }
    
    originalFile = file;
    const preview = document.getElementById('photoPreview');
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        
        // Update text
        const textDiv = preview.nextElementSibling;
        textDiv.querySelector('div:first-child').textContent = 'Foto dipilih';
        textDiv.querySelector('div:last-child').textContent = 'Klik foto untuk atur crop';
        
        // Add remove button if not exists
        if (!document.querySelector('button[onclick="removePhoto()"]')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.onclick = removePhoto;
            btn.style.cssText = 'margin-left:auto;background:#fef2f2;color:#ef4444;border:none;padding:0.5rem 1rem;border-radius:6px;cursor:pointer;font-size:0.85rem;font-weight:600;transition:all 0.2s';
            btn.textContent = 'Hapus';
            btn.onmouseover = function() { this.style.background='#fee2e2'; this.style.transform='translateY(-2px)'; };
            btn.onmouseout = function() { this.style.background='#fef2f2'; this.style.transform='translateY(0)'; };
            textDiv.parentElement.appendChild(btn);
        }
    };
    reader.readAsDataURL(file);
}

// Remove photo
function removePhoto() {
    document.getElementById('photoInput').value = '';
    const preview = document.getElementById('photoPreview');
    preview.style.display = 'none';
    preview.src = '#';
    document.getElementById('croppedPhoto').value = '';
    originalFile = null;
    
    // Reset text
    const textDiv = preview.nextElementSibling;
    textDiv.querySelector('div:first-child').textContent = 'Belum ada foto';
    textDiv.querySelector('div:last-child').textContent = 'Klik foto untuk atur crop';
    
    // Remove button
    const btn = document.querySelector('button[onclick="removePhoto()"]');
    if (btn) btn.remove();
    
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
}

// Open crop modal
function openCropModal() {
    if (!originalFile) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Silakan upload foto terlebih dahulu.');
        } else {
            alert('Silakan upload foto terlebih dahulu.');
        }
        return;
    }
    
    if (typeof Cropper === 'undefined') {
        if (typeof Toast !== 'undefined') {
            Toast.error('Cropper.js belum ter-load. Silakan refresh halaman.');
        } else {
            alert('Cropper.js belum ter-load. Silakan refresh halaman.');
        }
        return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const cropImage = document.getElementById('cropImage');
        cropImage.src = e.target.result;
        
        document.getElementById('cropModal').style.display = 'flex';
        
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
        cropImage.onload = function() {
            try {
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.8,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    minContainerWidth: 300,
                    minContainerHeight: 300
                });
            } catch (error) {
                console.error('Error initializing cropper:', error);
                if (typeof Toast !== 'undefined') {
                    Toast.error('Gagal menginisialisasi crop tool.');
                } else {
                    alert('Gagal menginisialisasi crop tool.');
                }
            }
        };
    };
    reader.readAsDataURL(originalFile);
}

// Close crop modal
function closeCropModal() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('cropModal').style.display = 'none';
}

// Rotate image
function rotateImage(degrees) {
    if (cropper) cropper.rotate(degrees);
}

// Reset crop
function resetCrop() {
    if (cropper) cropper.reset();
}

// Apply crop
function applyCrop() {
    if (!cropper) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Crop tool belum siap. Silakan coba lagi.');
        } else {
            alert('Crop tool belum siap. Silakan coba lagi.');
        }
        return;
    }
    
    try {
        const canvas = cropper.getCroppedCanvas({
            width: 400,
            height: 400,
            imageSmoothingQuality: 'high',
            fillColor: '#fff'
        });
        
        if (!canvas) {
            if (typeof Toast !== 'undefined') {
                Toast.error('Gagal membuat hasil crop.');
            } else {
                alert('Gagal membuat hasil crop.');
            }
            return;
        }
        
        const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('photoPreview').src = croppedDataUrl;
        document.getElementById('croppedPhoto').value = croppedDataUrl;
        
        // Update text to show it's been cropped
        const preview = document.getElementById('photoPreview');
        const textDiv = preview.nextElementSibling;
        textDiv.querySelector('div:first-child').textContent = 'Foto dipilih (sudah di-crop)';
        textDiv.querySelector('div:last-child').textContent = 'Klik untuk crop ulang';
        
        if (typeof Toast !== 'undefined') {
            Toast.success('Foto berhasil di-crop!');
        }
        
        closeCropModal();
    } catch (error) {
        console.error('Error applying crop:', error);
        if (typeof Toast !== 'undefined') {
            Toast.error('Gagal menerapkan crop. Silakan coba lagi.');
        } else {
            alert('Gagal menerapkan crop. Silakan coba lagi.');
        }
    }
}

// AUTO-CROP saat form submit jika user belum crop
document.getElementById('mainForm').addEventListener('submit', function(e) {
    // Jika sudah submitting, lanjutkan
    if (isSubmitting) return true;
    
    const croppedPhoto = document.getElementById('croppedPhoto').value;
    
    // Jika sudah ada cropped photo, lanjutkan submit
    if (croppedPhoto) {
        return true;
    }
    
    // Jika ada file tapi belum di-crop, auto-crop
    if (originalFile) {
        e.preventDefault();
        isSubmitting = true;
        
        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Memproses...';
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                // Buat canvas untuk auto-crop (center square)
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                canvas.width = 400;
                canvas.height = 400;
                
                // Hitung crop area (center square)
                const size = Math.min(img.width, img.height);
                const x = (img.width - size) / 2;
                const y = (img.height - size) / 2;
                
                // Draw cropped image
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, 400, 400);
                ctx.drawImage(img, x, y, size, size, 0, 0, 400, 400);
                
                // Convert to base64
                const croppedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
                document.getElementById('croppedPhoto').value = croppedDataUrl;
                
                // Submit form setelah auto-crop
                setTimeout(() => form.submit(), 100);
            };
            img.src = e.target.result;
        };
        
        reader.readAsDataURL(originalFile);
        return false;
    }
    
    // Jika tidak ada foto, lanjutkan submit
    return true;
});

// Add spin animation
const spinStyle = document.createElement('style');
spinStyle.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(spinStyle);
</script>
@endsection