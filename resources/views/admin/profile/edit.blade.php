@extends('admin.layouts.app')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
@endpush

@section('content')

{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Edit Profil</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Perbarui informasi akun dan foto profil Anda</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn" style="background:#f8fafc;color:var(--text-dark)">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:0.25rem">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
<div style="background:#f0fdf4;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#166534;display:flex;align-items:center;gap:0.75rem">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

{{-- Form Card --}}
<div class="card" style="padding:0;overflow:hidden;border-radius:12px">
    <form id="profileForm" action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PATCH')
        
        {{-- Avatar Upload Section --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc;text-align:center">
            <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem 0">Foto Profil</h3>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0 0 1rem 0">Unggah foto profil baru untuk memperbarui tampilan akun</p>
            
            {{-- Avatar Preview Container --}}
            <div style="position:relative;display:inline-block;margin-bottom:1rem">
                {{-- Avatar Circle --}}
                <div style="width:120px;height:120px;border-radius:50%;overflow:hidden;background:#f1f5f9;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.08);display:flex;align-items:center;justify-content:center">
                    @if($user->avatar)
                        <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" style="width:100%;height:100%;object-fit:cover;display:block">
                    @else
                        <div id="avatarPlaceholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;font-size:2.5rem;font-weight:700">
                            {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                        </div>
                        <img id="avatarPreview" style="width:100%;height:100%;object-fit:cover;display:none">
                    @endif
                </div>
                
                {{-- Camera Button --}}
                <button type="button" onclick="document.getElementById('avatarInput').click()" 
                        style="position:absolute;bottom:5px;right:5px;background:var(--primary);color:#fff;border:2px solid #fff;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,0.2);transition:all 0.2s;z-index:10"
                        onmouseover="this.style.transform='scale(1.1)';this.style.background='#0d9488'" 
                        onmouseout="this.style.transform='scale(1)';this.style.background='var(--primary)'">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:block">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                </button>
            </div>
            
            <div style="margin-top:0.5rem">
                <p style="color:var(--text-muted);font-size:0.85rem;margin:0">Klik ikon kamera untuk upload foto</p>
                <p style="color:var(--text-muted);font-size:0.75rem;margin:0.25rem 0 0 0">JPG/PNG/WebP, maksimal 2MB</p>
            </div>
            
            {{-- File Input --}}
            <input type="file" name="avatar" id="avatarInput" style="display:none" accept="image/*" onchange="openCropper(event)">
            
            {{-- ✅ HIDDEN INPUT FOR BASE64 --}}
            <input type="hidden" name="cropped_avatar_base64" id="croppedAvatarBase64">
            
            {{-- Validation Error --}}
            @error('avatar')
                <small style="color:#ef4444;display:block;margin-top:0.4rem;font-size:0.85rem">{{ $message }}</small>
            @enderror
        </div>
        
        {{-- Form Fields --}}
        <div style="padding:1.5rem">
            <div style="display:grid;gap:1.5rem;max-width:500px;margin:0 auto">
                
                {{-- Name Field --}}
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap Anda">
                    @error('name')
                        <small style="color:#ef4444;display:block;margin-top:0.4rem;font-size:0.85rem">{{ $message }}</small>
                    @enderror
                </div>
                
                {{-- Email Field --}}
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="contoh@email.com">
                    @error('email')
                        <small style="color:#ef4444;display:block;margin-top:0.4rem;font-size:0.85rem">{{ $message }}</small>
                    @enderror
                    @if($user->email_verified_at === null)
                        <small style="color:#f59e0b;display:block;margin-top:0.4rem;font-size:0.85rem">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:0.25rem">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                            Email belum terverifikasi
                        </small>
                    @endif
                </div>
                
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div style="padding:1.5rem;border-top:1px solid rgba(0,0,0,0.06);background:#f8fafc;display:flex;gap:0.75rem;justify-content:flex-end">
            <a href="{{ route('admin.dashboard') }}" class="btn" style="background:#fff;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Cropper Modal --}}
<div id="cropperModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.75);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:1.5rem;max-width:600px;width:90%;max-height:90vh;overflow:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
            <h3 style="font-size:1.1rem;font-weight:700;color:var(--text-dark);margin:0">Crop Foto Profil</h3>
            <button onclick="closeCropper()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0.25rem">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        
        <div style="margin-bottom:1rem;background:#000;border-radius:8px;overflow:hidden;max-height:50vh">
            <img id="cropperImage" style="max-width:100%;display:block">
        </div>
        
        <div style="display:flex;gap:0.75rem;justify-content:flex-end">
            <button onclick="closeCropper()" class="btn" style="background:#f8fafc;color:var(--text-dark)">Batal</button>
            <button onclick="cropAndSave()" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Potong & Simpan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper = null;

function openCropper(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    // Validasi ukuran file (2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file terlalu besar. Maksimal 2MB.');
        event.target.value = '';
        return;
    }
    
    // Validasi tipe file
    if (!file.type.match('image.*')) {
        alert('File harus berupa gambar (JPG/PNG/WebP).');
        event.target.value = '';
        return;
    }
    
    const reader = new FileReader();
    
    reader.onload = function(e) {
        const image = document.getElementById('cropperImage');
        image.src = e.target.result;
        
        // Tampilkan modal
        document.getElementById('cropperModal').style.display = 'flex';
        
        // Inisialisasi Cropper setelah image load
        setTimeout(() => {
            if (cropper) {
                cropper.destroy();
            }
            
            cropper = new Cropper(image, {
                aspectRatio: 1, // Persegi
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
            });
        }, 100);
    };
    
    reader.readAsDataURL(file);
}

function closeCropper() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    document.getElementById('cropperModal').style.display = 'none';
    document.getElementById('avatarInput').value = '';
}

// ✅ FUNGSI CROPANDSAVE YANG DIPERBAIKI (BASE64)
function cropAndSave() {
    if (!cropper) return;
    
    // Dapatkan hasil crop sebagai Data URL (base64)
    const dataUrl = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        fillColor: '#fff',
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
    }).toDataURL('image/jpeg', 0.9);
    
    // Simpan ke hidden input
    document.getElementById('croppedAvatarBase64').value = dataUrl;
    
    // Update preview
    const preview = document.getElementById('avatarPreview');
    const placeholder = document.getElementById('avatarPlaceholder');
    
    preview.src = dataUrl;
    preview.style.display = 'block';
    
    if (placeholder) {
        placeholder.style.display = 'none';
    }
    
    // Tutup modal
    closeCropper();
}

// ✅ DISABLE FILE INPUT JIKA BASE64 ADA (agar tidak konflik saat submit)
document.getElementById('profileForm').addEventListener('submit', function(e) {
    const base64 = document.getElementById('croppedAvatarBase64').value;
    const fileInput = document.getElementById('avatarInput');
    
    // Jika ada base64, nonaktifkan file input agar tidak dikirim ganda
    if (base64 && base64.length > 100) {
        fileInput.disabled = true;
    }
});

// Auto-hide success message after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const successMsg = document.querySelector('[style*="background:#f0fdf4"]');
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.transition = 'opacity 0.3s, transform 0.3s';
            successMsg.style.opacity = '0';
            successMsg.style.transform = 'translateY(-10px)';
            setTimeout(() => successMsg.remove(), 300);
        }, 5000);
    }
});
</script>
@endpush

@endsection