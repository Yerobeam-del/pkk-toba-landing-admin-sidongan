@extends('sidongan.layouts.app')
@section('title', 'Edit Profil - SIDONGAN')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
/* ===== Profile Edit Page ===== */
.sp-grid { display:grid; grid-template-columns:1fr 2fr; gap:1.5rem; align-items:start; }
@media(max-width:768px){ .sp-grid{ grid-template-columns:1fr; } }

.sp-card {
    background: var(--card-bg, #fff);
    border-radius: 12px;
    border: 1px solid var(--border-light, #e2e8f0);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
}
.sp-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-light, #e2e8f0);
    display: flex; align-items: center; gap: 0.75rem;
}
.sp-card-header h2 {
    font-size: 1rem; font-weight: 700; color: var(--text-dark, #1e293b); margin: 0;
}
.sp-card-body { padding: 1.5rem; }

/* Sidebar card */
.sp-sidebar { text-align: center; }
.sp-sidebar .sp-avatar-wrap { position: relative; display: inline-block; margin-bottom: 1rem; }
.sp-sidebar .sp-avatar {
    width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
    border: 3px solid var(--border-light, #e2e8f0);
}
.sp-sidebar .sp-avatar-fallback {
    width: 100px; height: 100px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary, #14b8a6), #0d9488);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 2rem; font-weight: 700;
    margin: 0 auto; border: 3px solid var(--border-light, #e2e8f0);
}
.sp-sidebar .sp-avatar-edit {
    position: absolute; bottom: 2px; right: 2px;
    width: 32px; height: 32px; border-radius: 50%;
    background: var(--primary, #14b8a6); color: #fff;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid var(--card-bg, #fff); cursor: pointer;
    transition: transform 0.2s;
}
.sp-sidebar .sp-avatar-edit:hover { transform: scale(1.1); }
.sp-sidebar .sp-user-name {
    font-size: 1.1rem; font-weight: 700; color: var(--text-dark, #1e293b); margin: 0 0 0.25rem;
}
.sp-sidebar .sp-user-role {
    font-size: 0.8rem; color: var(--text-muted, #64748b); margin: 0 0 1rem;
}

/* Completion ring */
.sp-ring { position: relative; width: 90px; height: 90px; margin: 0 auto 0.75rem; }
.sp-ring svg { transform: rotate(-90deg); }
.sp-ring .bg-circle { fill: none; stroke: var(--border-light, #e2e8f0); stroke-width: 6; }
.sp-ring .progress-circle { fill: none; stroke: var(--primary, #14b8a6); stroke-width: 6; stroke-linecap: round; transition: stroke-dashoffset 0.6s ease; }
.sp-ring .ring-text {
    position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
    font-size: 0.85rem; font-weight: 700; color: var(--primary, #14b8a6);
}

/* Checklist */
.sp-checklist { list-style: none; padding: 0; margin: 0; }
.sp-checklist li {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.35rem 0; font-size: 0.8rem; color: var(--text-dark, #334155);
}
.sp-checklist .sp-check-done { color: #16a34a; flex-shrink: 0; }
.sp-checklist .sp-check-pending { color: var(--border-light, #d1d5db); flex-shrink: 0; }

/* Form fields */
.sp-form-grid { display: grid; gap: 1.25rem; }
.sp-form-row { display: grid; grid-template-columns:1fr 1fr; gap:1rem; }
@media(max-width:600px){ .sp-form-row{ grid-template-columns:1fr; } }
.sp-field label {
    display: block; font-size: 0.8rem; font-weight: 600;
    color: var(--text-dark, #334155); margin-bottom: 0.4rem;
}
.sp-field label .required { color: #ef4444; }
.sp-field input[type="text"],
.sp-field input[type="email"],
.sp-field input[type="tel"] {
    width: 100%; padding: 0.6rem 0.85rem; border-radius: 8px;
    border: 1px solid var(--border-light, #e2e8f0);
    background: var(--card-bg, #fff); color: var(--text-dark, #1e293b);
    font-size: 0.9rem; font-family: inherit;
    transition: border-color 0.2s;
}
.sp-field input:focus { outline: none; border-color: var(--primary, #14b8a6); box-shadow: 0 0 0 3px rgba(20,184,166,0.1); }
.sp-field .sp-hint { font-size: 0.75rem; color: var(--text-muted, #64748b); margin-top: 0.3rem; }
.sp-field .sp-error { font-size: 0.75rem; color: #ef4444; margin-top: 0.3rem; }

/* Avatar upload section */
.sp-avatar-upload {
    display: flex; align-items: center; gap: 1rem;
    padding: 1rem; background: var(--surface-bg, #f8fafc); border-radius: 10px;
    border: 1px solid var(--border-light, #e2e8f0);
}
.sp-avatar-upload .sp-avatar-sm {
    width: 64px; height: 64px; border-radius: 50%; object-fit: cover;
    border: 2px solid var(--border-light, #e2e8f0); flex-shrink: 0;
}
.sp-avatar-upload .sp-avatar-sm-fallback {
    width: 64px; height: 64px; border-radius: 50%;
    background: linear-gradient(135deg, var(--primary, #14b8a6), #0d9488);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.4rem; font-weight: 700; flex-shrink: 0;
    border: 2px solid var(--border-light, #e2e8f0);
}
.sp-avatar-upload-info { flex: 1; }
.sp-avatar-upload-info p { margin: 0; }
.sp-avatar-upload-info .info-title { font-size: 0.85rem; font-weight: 600; color: var(--text-dark, #334155); }
.sp-avatar-upload-info .info-hint { font-size: 0.75rem; color: var(--text-muted, #64748b); margin-top: 0.15rem; }

/* Verified badge */
.sp-badge-verified {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.2rem 0.55rem; border-radius: 6px;
    font-size: 0.7rem; font-weight: 500; white-space: nowrap;
    background: #f0fdf4; color: #16a34a;
}
.sp-badge-pending {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.2rem 0.55rem; border-radius: 6px;
    font-size: 0.7rem; font-weight: 500; white-space: nowrap;
    background: #fef3c7; color: #d97706;
}

/* Personal email section */
.sp-personal-email-box {
    padding: 1rem; background: var(--surface-bg, #f8fafc); border-radius: 10px;
    border: 1px solid var(--border-light, #e2e8f0);
}
.sp-personal-email-box label {
    font-size: 0.85rem; font-weight: 600; color: var(--text-dark, #334155);
    margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem;
}
.sp-personal-email-box a { color: var(--primary, #14b8a6); font-weight: 600; text-decoration: underline; }

/* Submit buttons */
.sp-actions {
    margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-light, #e2e8f0);
    display: flex; gap: 0.75rem; justify-content: flex-end;
}
.sp-btn {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1.25rem; border-radius: 8px; font-size: 0.85rem;
    font-weight: 600; font-family: inherit; cursor: pointer;
    border: 1px solid transparent; transition: all 0.2s;
}
.sp-btn-primary {
    background: var(--primary, #14b8a6); color: #fff;
}
.sp-btn-primary:hover { opacity: 0.9; }
.sp-btn-secondary {
    background: var(--surface-bg, #f1f5f9); color: #475569;
    border-color: var(--border-light, #e2e8f0);
}
.sp-btn-secondary:hover { background: var(--border-light, #e2e8f0); }

/* Password tab */
.sp-password-grid { max-width: 400px; }

/* Cropper modal */
.sp-cropper-overlay {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6);
    z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;
}
.sp-cropper-overlay.active { display: flex; }
.sp-cropper-box {
    background: var(--card-bg, #fff); border-radius: 16px; padding: 1.5rem;
    max-width: 500px; width: 100%; max-height: 90vh; overflow-y: auto;
}
.sp-cropper-box h3 { font-size: 1rem; font-weight: 700; color: var(--text-dark, #1e293b); margin: 0 0 1rem; }
.sp-cropper-box img { max-width: 100%; max-height: 60vh; border-radius: 8px; }
.sp-cropper-actions { display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1rem; }
</style>
@endpush

@section('content')

@php
    $nameParts = explode(' ', $currentUser->name ?? $user->name ?? 'U');
    $initials = count($nameParts) >= 2
        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
        : strtoupper(substr($user->name ?? 'U', 0, 2));
@endphp

@if($errors->any())
<div style="padding:0.85rem 1.25rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:0.85rem;font-weight:500;margin-bottom:1.5rem">
    <ul style="margin:0;padding-left:1.2rem">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.5rem">
    <a href="{{ route('sidongan.dashboard') }}" style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.45rem 0.85rem;border-radius:8px;background:var(--surface-bg,#f1f5f9);color:var(--text-muted,#64748b);text-decoration:none;font-size:0.8rem;font-weight:500;transition:all 0.2s">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>
    <h1 style="font-size:1.25rem;font-weight:700;color:var(--text-dark,#1e293b);margin:0">Edit Profil</h1>
</div>

<div class="sp-grid">

    {{-- SIDEBAR --}}
    <div class="sp-card sp-sidebar">
        <div class="sp-card-body">
            <div class="sp-avatar-wrap">
                @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="sp-avatar">
                @else
                <div class="sp-avatar-fallback">{{ $initials }}</div>
                @endif
            </div>
            <p class="sp-user-name">{{ $user->name }}</p>
            <p class="sp-user-role">{{ $user->sidongan_role_name }}</p>

            {{-- Completion Ring --}}
            <div style="margin-bottom:1rem">
                <p style="font-size:0.75rem;color:var(--text-muted,#64748b);margin:0 0 0.5rem;text-transform:uppercase;letter-spacing:0.5px;font-weight:600">Kelengkapan Profil</p>
                <div class="sp-ring">
                    <svg width="90" height="90" viewBox="0 0 100 100">
                        <circle class="bg-circle" cx="50" cy="50" r="42"/>
                        <circle class="progress-circle" cx="50" cy="50" r="42" stroke-dasharray="264" stroke-dashoffset="{{ 264 - (264 * $completionPercentage / 100) }}"/>
                    </svg>
                    <span class="ring-text">{{ $completionPercentage }}%</span>
                </div>
                <ul class="sp-checklist">
                    @foreach($completionItems as $key => $done)
                    <li>
                        @if($done)
                        <svg class="sp-check-done" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                        @else
                        <svg class="sp-check-pending" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                        @endif
                        <span>{{ match($key) { 'name' => 'Nama lengkap', 'email' => 'Email', 'avatar' => 'Foto profil', 'phone_number' => 'Nomor telepon', 'personal_email' => 'Email pribadi', default => $key } }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Account Info --}}
            <div style="border-top:1px solid var(--border-light,#e2e8f0);padding-top:1rem;margin-top:0.5rem">
                <p style="font-size:0.75rem;color:var(--text-muted,#64748b);margin:0 0 0.5rem;text-transform:uppercase;letter-spacing:0.5px;font-weight:600">Info Akun</p>
                <div style="display:flex;flex-direction:column;gap:0.5rem">
                    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--text-muted,#64748b)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>Bergabung {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;color:var(--text-muted,#64748b)">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        <span>Terakhir diubah {{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="sp-card">
        {{-- Tabs --}}
        <div class="sp-card-header" style="gap:0;border-bottom:none;padding-bottom:0">
            <button class="sp-tab active" data-tab="profil" onclick="switchTab('profil')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Profil
            </button>
            <button class="sp-tab" data-tab="keamanan" onclick="switchTab('keamanan')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Keamanan
            </button>
        </div>
        <style>
            .sp-tab {
                display:inline-flex;align-items:center;gap:0.4rem;padding:0.7rem 1rem;
                background:none;border:none;border-bottom:2px solid transparent;
                font-size:0.85rem;font-weight:500;color:var(--text-muted,#64748b);
                cursor:pointer;font-family:inherit;transition:all 0.2s;margin-bottom:-1px;
            }
            .sp-tab:hover { color:var(--text-dark,#334155); }
            .sp-tab.active { color:var(--primary,#14b8a6);border-bottom-color:var(--primary,#14b8a6);font-weight:600; }
            .sp-tab-content { display:none; }
            .sp-tab-content.active { display:block; }
        </style>

        <div class="sp-card-body">

            {{-- TAB 1: INFORMASI PROFIL --}}
            <div class="sp-tab-content active" id="tab-profil">
                <form action="{{ route('sidongan.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf @method('PATCH')

                    <div class="sp-form-grid">

                        {{-- Avatar Upload --}}
                        <div class="sp-avatar-upload">
                            <div style="position:relative;flex-shrink:0">
                                <div class="sp-avatar-sm-wrap">
                                    @if($user->avatar)
                                    <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="" class="sp-avatar-sm">
                                    @else
                                    <div id="avatarPlaceholder" class="sp-avatar-sm-fallback">{{ $initials }}</div>
                                    <img id="avatarPreviewImg" class="sp-avatar-sm" style="display:none" alt="">
                                    @endif
                                </div>
                                <button type="button" data-action="pick-avatar" class="sp-avatar-edit" title="Ganti foto">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                </button>
                            </div>
                            <div class="sp-avatar-upload-info">
                                <p class="info-title">Foto Profil</p>
                                <p class="info-hint">Klik ikon kamera untuk mengganti. JPG/PNG/WebP, maks 2MB.</p>
                            </div>
                            <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none">
                            <input type="hidden" name="cropped_avatar_base64" id="croppedAvatarBase64">
                        </div>

                        {{-- Nama --}}
                        <div class="sp-form-row">
                            <div class="sp-field">
                                <label for="name">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap">
                                @error('name') <p class="sp-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="sp-field">
                                <label for="email">Email <span class="required">*</span></label>
                                <div style="display:flex;gap:0.5rem;align-items:center">
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="contoh@email.com" style="flex:1">
                                    @if($user->email_verified_at)
                                    <span class="sp-badge-verified">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        Terverifikasi
                                    </span>
                                    @else
                                    <span class="sp-badge-pending">Belum Verifikasi</span>
                                    @endif
                                </div>
                                @error('email') <p class="sp-error">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Nomor Telepon --}}
                        <div class="sp-field">
                            <label for="phone_number">Nomor Telepon</label>
                            <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 0812-3456-7890">
                            <p class="sp-hint">Opsional. Nomor yang bisa dihubungi untuk keperluan administrasi.</p>
                            @error('phone_number') <p class="sp-error">{{ $message }}</p> @enderror
                        </div>

                        {{-- Personal Email --}}
                        <div class="sp-personal-email-box">
                            <label>
                                Email Pribadi
                                <span class="sp-badge-pending" style="padding:0.1rem 0.35rem;font-size:0.65rem">Digunakan untuk reset password</span>
                            </label>
                            @if($user->hasVerifiedPersonalEmail())
                            <div style="display:flex;gap:0.5rem;align-items:center;margin-top:0.4rem">
                                <span style="color:#16a34a;font-weight:600;font-size:0.9rem">{{ $user->personal_email }}</span>
                                <span class="sp-badge-verified">Terverifikasi</span>
                            </div>
                            @elseif($user->personal_email)
                            <div style="display:flex;gap:0.5rem;align-items:center;margin-top:0.4rem">
                                <span style="color:#d97706;font-weight:600;font-size:0.9rem">{{ $user->personal_email }}</span>
                                <span class="sp-badge-pending">Menunggu Verifikasi</span>
                            </div>
                            <p style="font-size:0.8rem;color:var(--text-muted,#64748b);margin:0.35rem 0 0">Cek email {{ $user->personal_email }} untuk link verifikasi.</p>
                            @else
                            <p style="font-size:0.85rem;color:var(--text-muted,#64748b);margin:0.4rem 0 0">Belum diatur. <a href="{{ route('personal-email.setup') }}">Atur Sekarang</a></p>
                            @endif
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="sp-actions">
                        <button type="reset" class="sp-btn sp-btn-secondary">Reset</button>
                        <button type="submit" class="sp-btn sp-btn-primary" id="submitBtn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- TAB 2: KEAMANAN --}}
            <div class="sp-tab-content" id="tab-keamanan">
                <form action="{{ route('sidongan.profile.password.update') }}" method="POST">
                    @csrf @method('PUT')

                    <div class="sp-password-grid sp-form-grid">
                        <div class="sp-field">
                            <label for="current_password">Password Saat Ini <span class="required">*</span></label>
                            <input type="password" id="current_password" name="current_password" required placeholder="Masukkan password saat ini">
                            @error('current_password') <p class="sp-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="sp-field">
                            <label for="password">Password Baru <span class="required">*</span></label>
                            <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter">
                            @error('password') <p class="sp-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="sp-field">
                            <label for="password_confirmation">Konfirmasi Password Baru <span class="required">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="sp-actions">
                        <button type="submit" class="sp-btn sp-btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Cropper Modal --}}
<div class="sp-cropper-overlay" id="cropperOverlay">
    <div class="sp-cropper-box">
        <h3>Potong Foto Profil</h3>
        <img id="cropperImage" src="" alt="Crop">
        <div class="sp-cropper-actions">
            <button type="button" class="sp-btn sp-btn-secondary" onclick="closeCropper()">Batal</button>
            <button type="button" class="sp-btn sp-btn-primary" onclick="applyCrop()">Gunakan Foto</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
// Tab switching
function switchTab(tab) {
    document.querySelectorAll('.sp-tab').forEach(function(t) { t.classList.remove('active'); });
    document.querySelectorAll('.sp-tab-content').forEach(function(c) { c.classList.remove('active'); });
    document.querySelector('.sp-tab[data-tab="' + tab + '"]').classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

// Avatar cropper
var cropper = null;
var avatarInput = document.getElementById('avatarInput');
var cropperImage = document.getElementById('cropperImage');
var cropperOverlay = document.getElementById('cropperOverlay');
var croppedBase64 = document.getElementById('croppedAvatarBase64');

document.querySelector('[data-action="pick-avatar"]').addEventListener('click', function() {
    avatarInput.click();
});

avatarInput.addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(ev) {
        cropperImage.src = ev.target.result;
        cropperOverlay.classList.add('active');
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropperImage, {
            aspectRatio: 1,
            viewMode: 1,
            minCropBoxSize: 100,
            background: false,
        });
    };
    reader.readAsDataURL(file);
});

function applyCrop() {
    if (!cropper) return;
    var canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
    var base64 = canvas.toDataURL('image/jpeg', 0.85);
    croppedBase64.value = base64;

    // Update preview
    var previewImg = document.getElementById('avatarPreviewImg');
    var placeholder = document.getElementById('avatarPlaceholder');
    var existingPreview = document.getElementById('avatarPreview');

    if (previewImg) { previewImg.src = base64; previewImg.style.display = 'block'; }
    if (placeholder) placeholder.style.display = 'none';
    if (existingPreview) { existingPreview.src = base64; }

    closeCropper();
}

function closeCropper() {
    cropperOverlay.classList.remove('active');
    if (cropper) { cropper.destroy(); cropper = null; }
}

// Submit loading state
document.getElementById('profileForm').addEventListener('submit', function() {
    var btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><circle cx="12" cy="12" r="10"/></svg> Menyimpan...';
});

// Show validation errors as toasts
@if($errors->any())
(function() {
    var errors = @json($errors->all());
    errors.forEach(function(msg) { Toast.error(msg); });
})();
@endif
</script>
<style>@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
@endpush
