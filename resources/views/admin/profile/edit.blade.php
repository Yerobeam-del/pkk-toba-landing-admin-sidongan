@extends('admin.layouts.app')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
.profile-grid{display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start}
@media(max-width:768px){.profile-grid{grid-template-columns:1fr}}
.profile-sidebar-card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border:1px solid #e2e8f0;overflow:hidden;position:sticky;top:5.5rem}
@media(max-width:768px){.profile-sidebar-card{position:static}}
.profile-main-card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border:1px solid #e2e8f0;overflow:hidden}
.profile-tabs{display:flex;border-bottom:1px solid #e2e8f0;background:#f8fafc;overflow-x:auto}
.profile-tab{display:flex;align-items:center;gap:0.5rem;padding:0.875rem 1.25rem;font-size:0.85rem;font-weight:600;color:#64748b;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;transition:all 0.2s;white-space:nowrap;text-decoration:none}
.profile-tab:hover{color:var(--primary);background:rgba(20,184,166,0.05)}
.profile-tab.active{color:var(--primary);border-bottom-color:var(--primary);background:rgba(20,184,166,0.08)}
.profile-tab svg{width:18px;height:18px;flex-shrink:0}
.tab-content{display:none;padding:1.5rem}
.tab-content.active{display:block;animation:fadeIn 0.3s ease}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.completion-ring{width:100px;height:100px;border-radius:50%;display:flex;align-items:center;justify-content:center;position:relative;margin:0 auto}
.completion-ring svg{transform:rotate(-90deg)}
.completion-ring .bg-circle{fill:none;stroke:#e2e8f0;stroke-width:6}
.completion-ring .progress-circle{fill:none;stroke:var(--primary);stroke-width:6;stroke-linecap:round;stroke-dasharray:283;transition:stroke-dashoffset 1s ease}
.completion-ring .center-text{position:absolute;font-size:1.5rem;font-weight:800;color:var(--primary)}
.section-header{font-size:1.1rem;font-weight:700;color:#1e293b;margin:0 0 .25rem 0}
.section-desc{font-size:.85rem;color:#94a3b8;margin:0 0 1.25rem 0}
.strength-bar{height:6px;border-radius:4px;background:#e2e8f0;overflow:hidden;margin-top:.75rem}
.strength-bar-fill{height:100%;border-radius:4px;transition:all .3s;width:0}
.strength-label{font-size:.8rem;font-weight:600;margin-top:.35rem;text-align:right}
.app-card{display:flex;align-items:center;gap:1rem;padding:1rem;border-radius:10px;border:1px solid #e2e8f0;transition:all .2s}
.app-card:hover{border-color:var(--primary);box-shadow:0 2px 8px rgba(20,184,166,0.1)}
.app-icon-box{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.app-icon-box svg{width:24px;height:24px;color:#fff}
.perm-tag{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .75rem;border-radius:999px;font-size:.8rem;font-weight:500;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.form-field{margin-bottom:1.25rem}
.form-field label{display:block;font-weight:600;font-size:.85rem;color:#334155;margin-bottom:.4rem}
.form-field .form-control{width:100%;padding:.7rem .9rem;border:2px solid #e2e8f0;border-radius:8px;font-size:.9rem;transition:border-color .2s;outline:none}
.form-field .form-control:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(20,184,166,0.1)}
.checklist-item{display:flex;align-items:center;gap:.5rem;padding:.35rem 0;font-size:.85rem;color:#475569}
.checklist-item .icon{width:18px;height:18px;flex-shrink:0}
.checklist-item .icon.done{color:var(--primary)}
.checklist-item .icon.pending{color:#cbd5e1}
</style>
@endpush

@section('content')

@php
$appList = [
['name' => 'Admin Panel', 'icon' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5', 'color' => '#14b8a6', 'url' => route('admin.dashboard'), 'accessible' => true],
['name' => 'SIDONGAN', 'icon' => 'M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z', 'color' => '#8b5cf6', 'url' => 'https://sidongan.' . config('app.landing_domain', 'pkktoba.id'), 'accessible' => $user->hasSidonganAccess()],
['name' => 'SIEDA', 'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z', 'color' => '#0ea5a4', 'url' => '#', 'accessible' => !empty($user->sieda_role)],
];
@endphp

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;margin:0 0 0.25rem 0">Edit Profil</h1>
        <p style="color:#94a3b8;margin:0;font-size:0.9rem">Kelola informasi akun dan akses aplikasi Anda</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1rem;background:#f1f5f9;color:#475569;border-radius:8px;text-decoration:none;font-size:0.85rem;font-weight:500;transition:all 0.2s">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>
</div>

@if(session('success'))
<div style="background:#f0fdf4;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#166534;display:flex;align-items:center;gap:0.75rem" id="successMsg">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#dc2626">
    <ul style="margin:0;padding-left:1.25rem">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="profile-grid">

    {{-- SIDEBAR --}}
    <div class="profile-sidebar-card">
        <div style="padding:2rem 1.5rem 1.5rem;text-align:center;background:linear-gradient(180deg,rgba(20,184,166,0.06) 0%,transparent 100%)">
            <div style="position:relative;display:inline-block;margin-bottom:0.75rem">
                <div style="width:90px;height:90px;border-radius:50%;overflow:hidden;border:3px solid #fff;box-shadow:0 4px 12px rgba(0,0,0,0.1);margin:0 auto">
                    @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width:100%;height:100%;object-fit:cover">
                    @else
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;font-size:2rem;font-weight:700">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                    @endif
                </div>
                @if($user->hasVerifiedPersonalEmail())
                <div style="position:absolute;bottom:2px;right:2px;width:22px;height:22px;background:var(--primary);border:2px solid #fff;border-radius:50%;display:flex;align-items:center;justify-content:center">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                @endif
            </div>
            <h3 style="font-size:1.1rem;font-weight:700;color:#1e293b;margin:0 0 0.25rem 0">{{ $user->name }}</h3>
            <p style="color:#64748b;margin:0;font-size:0.85rem">{{ $user->email }}</p>
            <div style="display:flex;gap:0.35rem;justify-content:center;margin-top:0.5rem;flex-wrap:wrap">
                @if($user->hasSidonganAccess())
                <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.2rem 0.6rem;background:#f5f3ff;color:#6d28d9;border-radius:999px;font-size:0.75rem;font-weight:500">{{ $user->sidongan_role_name }}</span>
                @endif
                @if(!empty($user->sieda_role))
                <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.2rem 0.6rem;background:#ecfeff;color:#0e7490;border-radius:999px;font-size:0.75rem;font-weight:500">SIEDA</span>
                @endif
                @if($user->role)
                <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.2rem 0.6rem;background:#f0fdf4;color:#15803d;border-radius:999px;font-size:0.75rem;font-weight:500">{{ ucfirst($user->role->name) }}</span>
                @endif
            </div>
        </div>

        {{-- Completion Ring --}}
        <div style="padding:1.25rem 1.5rem;border-top:1px solid #f1f5f9">
            <p style="font-size:0.85rem;font-weight:600;color:#64748b;margin:0 0 0.75rem 0;text-align:center">Kelengkapan Profil</p>
            <div class="completion-ring">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle class="bg-circle" cx="50" cy="50" r="45"/>
                    <circle class="progress-circle" cx="50" cy="50" r="45" stroke-dashoffset="{{ 283 - (283 * $completionPercentage / 100) }}"/>
                </svg>
                <span class="center-text">{{ $completionPercentage }}%</span>
            </div>
            <div style="margin-top:0.75rem">
                @foreach($completionItems as $key => $done)
                <div class="checklist-item">
                    @if($done)
                    <svg class="icon done" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                    @else
                    <svg class="icon pending" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                    @endif
                    <span>{{ match($key) { 'name' => 'Nama lengkap', 'email' => 'Email', 'avatar' => 'Foto profil', 'phone_number' => 'Nomor telepon', 'personal_email' => 'Email pribadi', default => $key } }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Account Info --}}
        <div style="padding:1.25rem 1.5rem;border-top:1px solid #f1f5f9">
            <p style="font-size:0.85rem;font-weight:600;color:#64748b;margin:0 0 0.75rem 0;text-align:center">Info Akun</p>
            <div style="display:flex;flex-direction:column;gap:0.5rem">
                <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:#475569">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#94a3b8"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Bergabung {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:#475569">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#94a3b8"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    <span>Terakhir diperbarui {{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="profile-main-card">
        <div class="profile-tabs" role="tablist">
            <button class="profile-tab active" data-tab="profil" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Profil
            </button>
            <button class="profile-tab" data-tab="keamanan" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Keamanan
            </button>
            <button class="profile-tab" data-tab="apps" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Akses Aplikasi
            </button>
            <button class="profile-tab" data-tab="perizinan" role="tab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Perizinan
            </button>
        </div>

        {{-- TAB 1: INFORMASI PROFIL --}}
        <div class="tab-content active" id="tab-profil">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf @method('PATCH')
                <div style="display:grid;gap:1.5rem;max-width:600px">

                    {{-- Avatar Upload --}}
                    <div style="display:flex;align-items:center;gap:1.25rem;padding:1rem;background:#f8fafc;border-radius:10px;border:1px dashed #e2e8f0">
                        <div style="position:relative;flex-shrink:0">
                            <div style="width:64px;height:64px;border-radius:50%;overflow:hidden;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,0.08)">
                                @if($user->avatar)
                                <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="" style="width:100%;height:100%;object-fit:cover">
                                @else
                                <div id="avatarPlaceholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;font-size:1.25rem;font-weight:700">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                                <img id="avatarPreviewImg" style="width:100%;height:100%;object-fit:cover;display:none">
                                @endif
                            </div>
                            <button type="button" onclick="document.getElementById('avatarInput').click()" style="position:absolute;bottom:-2px;right:-2px;width:26px;height:26px;background:var(--primary);color:#fff;border:2px solid #fff;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </button>
                        </div>
                        <div style="flex:1">
                            <p style="font-weight:600;font-size:0.9rem;color:#1e293b;margin:0 0 0.15rem 0">Foto Profil</p>
                            <p style="font-size:0.8rem;color:#94a3b8;margin:0">Klik ikon kamera untuk mengganti foto. JPG/PNG/WebP, maks 2MB.</p>
                        </div>
                        <input type="file" name="avatar" id="avatarInput" style="display:none" accept="image/*">
                        <input type="hidden" name="cropped_avatar_base64" id="croppedAvatarBase64">
                    </div>

                    {{-- Nama --}}
                    <div class="form-field">
                        <label for="name">Nama Lengkap <span style="color:#ef4444">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap Anda">
                        @error('name') <small style="color:#ef4444;display:block;margin-top:0.3rem;font-size:0.8rem">{{ $message }}</small> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-field">
                        <label for="email">Email <span style="color:#ef4444">*</span></label>
                        <div style="display:flex;align-items:center;gap:0.5rem">
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="contoh@email.com" style="flex:1">
                            @if($user->email_verified_at)
                            <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.35rem 0.6rem;background:#f0fdf4;color:#16a34a;border-radius:6px;font-size:0.75rem;font-weight:500;white-space:nowrap;flex-shrink:0"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Terverifikasi</span>
                            @else
                            <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.35rem 0.6rem;background:#fef3c7;color:#d97706;border-radius:6px;font-size:0.75rem;font-weight:500;white-space:nowrap;flex-shrink:0">Belum Verifikasi</span>
                            @endif
                        </div>
                        @error('email') <small style="color:#ef4444;display:block;margin-top:0.3rem;font-size:0.8rem">{{ $message }}</small> @enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div class="form-field">
                        <label for="phone_number">Nomor Telepon</label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 0812-3456-7890">
                        <small style="color:#94a3b8;display:block;margin-top:0.3rem;font-size:0.8rem">Opsional. Nomor yang bisa dihubungi untuk keperluan administrasi.</small>
                        @error('phone_number') <small style="color:#ef4444;display:block;margin-top:0.3rem;font-size:0.8rem">{{ $message }}</small> @enderror
                    </div>

                    {{-- Personal Email --}}
                    <div style="padding:1rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
                        <label style="font-weight:600;font-size:0.85rem;color:#334155;margin-bottom:0.4rem;display:flex;align-items:center;gap:0.5rem">
                            Email Pribadi <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.15rem 0.45rem;background:#fef3c7;color:#d97706;border-radius:999px;font-size:0.7rem;font-weight:500">Digunakan untuk reset password</span>
                        </label>
                        @if($user->hasVerifiedPersonalEmail())
                        <div style="display:flex;align-items:center;gap:0.5rem">
                            <span style="color:#16a34a;font-weight:600;font-size:0.9rem">{{ $user->personal_email }}</span>
                            <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.2rem 0.5rem;background:#f0fdf4;color:#16a34a;border-radius:6px;font-size:0.75rem;font-weight:500">Terverifikasi</span>
                        </div>
                        @elseif($user->personal_email)
                        <div style="display:flex;align-items:center;gap:0.5rem">
                            <span style="color:#d97706;font-weight:600;font-size:0.9rem">{{ $user->personal_email }}</span>
                            <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:0.2rem 0.5rem;background:#fef3c7;color:#d97706;border-radius:6px;font-size:0.75rem;font-weight:500">Menunggu Verifikasi</span>
                        </div>
                        <p style="font-size:0.8rem;color:#94a3b8;margin:0.35rem 0 0 0">Cek email {{ $user->personal_email }} untuk link verifikasi.</p>
                        @else
                        <p style="font-size:0.85rem;color:#64748b;margin:0">Belum diatur. <a href="{{ route('personal-email.setup') }}" style="color:var(--primary);font-weight:600;text-decoration:underline">Atur Sekarang</a></p>
                        @endif
                    </div>
                </div>

                {{-- Submit --}}
                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #e2e8f0;display:flex;gap:0.75rem;justify-content:flex-end">
                    <button type="reset" style="padding:0.7rem 1.5rem;background:#f1f5f9;color:#475569;border:none;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer">Reset</button>
                    <button type="submit" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.5rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- TAB 2: KEAMANAN --}}
        <div class="tab-content" id="tab-keamanan">
            <div style="max-width:500px">
                <h3 class="section-header">Ubah Password</h3>
                <p class="section-desc">Pastikan password Anda kuat dan tidak digunakan di akun lain.</p>

                <form action="{{ route('admin.profile.password.update') }}" method="POST">
                    @csrf @method('PUT')
        <div class="form-field">
                        <label for="current_password">Password Saat Ini <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <input type="password" id="current_password" name="current_password" class="form-control" required placeholder="Masukkan password saat ini" style="padding-right:2.5rem">
                            <button type="button" onclick="togglePassword('current_password',this)" style="position:absolute;right:0.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0.25rem" tabindex="-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('current_password') <small style="color:#ef4444;display:block;margin-top:0.3rem;font-size:0.8rem">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-field">
                        <label for="password">Password Baru <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <input type="password" id="password" name="password" class="form-control" required placeholder="Minimal 8 karakter" style="padding-right:2.5rem" oninput="checkPasswordStrength(this.value)">
                            <button type="button" onclick="togglePassword('password',this)" style="position:absolute;right:0.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0.25rem" tabindex="-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <div class="strength-bar"><div class="strength-bar-fill" id="strengthFill"></div></div>
                        <div class="strength-label" id="strengthLabel" style="color:#94a3b8"></div>
                        @error('password') <small style="color:#ef4444;display:block;margin-top:0.3rem;font-size:0.8rem">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-field">
                        <label for="password_confirmation">Konfirmasi Password Baru <span style="color:#ef4444">*</span></label>
                        <div style="position:relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required placeholder="Ketik ulang password baru" style="padding-right:2.5rem" oninput="checkPasswordMatch(this.value)">
                            <button type="button" onclick="togglePassword('password_confirmation',this)" style="position:absolute;right:0.6rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;padding:0.25rem" tabindex="-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        <small id="matchMsg" style="display:block;margin-top:0.3rem;font-size:0.8rem"></small>
                    </div>
                    <button type="submit" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.5rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Update Password
                    </button>
                </form>

                <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid #e2e8f0">
                    <h4 style="font-size:0.95rem;font-weight:700;color:#1e293b;margin:0 0 0.75rem 0">Riwayat Keamanan</h4>
                    <div style="display:flex;flex-direction:column;gap:0.5rem">
                        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#f8fafc;border-radius:8px">
                            <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                            <div><p style="margin:0;font-weight:600;font-size:0.85rem;color:#1e293b">Akun Dibuat</p><p style="margin:0;font-size:0.8rem;color:#94a3b8">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-' }}</p></div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#f8fafc;border-radius:8px">
                            <div style="width:36px;height:36px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></div>
                            <div><p style="margin:0;font-weight:600;font-size:0.85rem;color:#1e293b">Terakhir Diperbarui</p><p style="margin:0;font-size:0.8rem;color:#94a3b8">{{ $user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') : '-' }} ({{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }})</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: AKSES APLIKASI --}}
        <div class="tab-content" id="tab-apps">
            <h3 class="section-header">Akses Aplikasi</h3>
            <p class="section-desc">Aplikasi yang dapat Anda akses dengan akun ini.</p>
            <div style="display:grid;gap:0.75rem">
                @foreach($appList as $app)
                <div class="app-card" style="opacity:{{ $app['accessible'] ? '1' : '0.5' }}">
                    <div class="app-icon-box" style="background:{{ $app['color'] }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $app['icon'] }}"/></svg>
                    </div>
                    <div style="flex:1">
                        <p style="margin:0;font-weight:600;color:#1e293b;font-size:0.9rem">{{ $app['name'] }}</p>
                        @if($app['accessible'])
                        <span style="font-size:0.8rem;color:var(--primary);font-weight:500">Akses Aktif</span>
                        @else
                        <span style="font-size:0.8rem;color:#94a3b8">Tidak Ada Akses</span>
                        @endif
                    </div>
                    @if($app['url'] !== '#' && $app['accessible'])
                    <a href="{{ $app['url'] }}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.4rem 0.75rem;background:{{ $app['color'] }};color:#fff;border-radius:6px;text-decoration:none;font-size:0.8rem;font-weight:500;white-space:nowrap">Buka</a>
                    @endif
                </div>
                @endforeach

                @if($linkedApplications && $linkedApplications->count() > 0)
                <h4 style="font-size:0.9rem;font-weight:700;color:#64748b;margin:1rem 0 0.5rem 0">Aplikasi Tambahan</h4>
                @foreach($linkedApplications as $lap)
                <div class="app-card">
                    <div class="app-icon-box" style="background:{{ $lap->effective_color ?? '#14b8a6' }}">
                        @if($lap->icon)
                        <img src="{{ $lap->icon }}" alt="" style="width:24px;height:24px">
                        @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        @endif
                    </div>
                    <div style="flex:1">
                        <p style="margin:0;font-weight:600;color:#1e293b;font-size:0.9rem">{{ $lap->name }}</p>
                        <span style="font-size:0.8rem;color:var(--primary);font-weight:500">Akses Aktif</span>
                    </div>
                    @if($lap->url)
                    <a href="{{ $lap->url }}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.4rem 0.75rem;background:{{ $lap->effective_color ?? '#14b8a6' }};color:#fff;border-radius:6px;text-decoration:none;font-size:0.8rem;font-weight:500">Buka</a>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- TAB 4: PERIZINAN --}}
        <div class="tab-content" id="tab-perizinan">
            <h3 class="section-header">Izin Efektif</h3>
            <p class="section-desc">Semua izin akses yang dimiliki akun ini dari role dan izin khusus.</p>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1.5rem">
                @forelse($effectivePermissions as $perm)
                <span class="perm-tag">{{ $perm }}</span>
                @empty
                <p style="color:#94a3b8;font-size:0.9rem">Tidak ada izin khusus.</p>
                @endforelse
            </div>
            @if($user->role)
            <div style="padding:1rem;background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0">
                <div style="display:flex;align-items:center;gap:0.75rem">
                    <div style="width:40px;height:40px;border-radius:8px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                    <div><p style="margin:0;font-weight:600;font-size:0.9rem;color:#1e293b">Role: {{ ucfirst($user->role->name) }}</p><p style="margin:0;font-size:0.8rem;color:#64748b">Izin bawaan dari role ini ditambah izin khusus akun.</p></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- CROPPER MODAL --}}
<div id="cropperModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.75);z-index:9999;align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:12px;padding:1.5rem;max-width:600px;width:90%;max-height:90vh;overflow:auto">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
            <h3 style="font-size:1.1rem;font-weight:700;color:#1e293b;margin:0">Crop Foto Profil</h3>
            <button type="button" onclick="closeCropper()" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0.25rem"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div style="margin-bottom:1rem;background:#000;border-radius:8px;overflow:hidden;max-height:50vh">
            <img id="cropperImage" style="max-width:100%;display:block">
        </div>
        <div style="display:flex;gap:0.75rem;justify-content:flex-end">
            <button type="button" onclick="closeCropper()" style="padding:0.6rem 1.25rem;background:#f1f5f9;color:#475569;border:none;border-radius:8px;font-size:0.85rem;font-weight:600;cursor:pointer">Batal</button>
            <button type="button" onclick="cropAndSave()" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.25rem;background:var(--primary);color:#fff;border:none;border-radius:8px;font-size:0.85rem;font-weight:600;cursor:pointer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Potong & Simpan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
let cropper = null;

document.getElementById('avatarInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { alert('Ukuran file maksimal 2MB.'); e.target.value = ''; return; }
    if (!file.type.match('image.*')) { alert('File harus berupa gambar.'); e.target.value = ''; return; }
    const reader = new FileReader();
    reader.onload = function(ev) {
        const img = document.getElementById('cropperImage');
        img.src = ev.target.result;
        document.getElementById('cropperModal').style.display = 'flex';
        setTimeout(function() {
            if (cropper) cropper.destroy();
            cropper = new Cropper(img, { aspectRatio: 1, viewMode: 1, dragMode: 'move', autoCropArea: 0.8, restore: false, guides: true, center: true, cropBoxMovable: true, cropBoxResizable: true, toggleDragModeOnDblclick: false });
        }, 100);
    };
    reader.readAsDataURL(file);
});

function closeCropper() {
    if (cropper) { cropper.destroy(); cropper = null; }
    document.getElementById('cropperModal').style.display = 'none';
    document.getElementById('avatarInput').value = '';
}

function cropAndSave() {
    if (!cropper) return;
    var dataUrl = cropper.getCroppedCanvas({ width: 400, height: 400, fillColor: '#fff', imageSmoothingEnabled: true, imageSmoothingQuality: 'high' }).toDataURL('image/jpeg', 0.9);
    document.getElementById('croppedAvatarBase64').value = dataUrl;
    var preview = document.getElementById('avatarPreview');
    var placeholder = document.getElementById('avatarPlaceholder');
    if (preview) { preview.src = dataUrl; }
    if (placeholder) { placeholder.style.display = 'none'; }
    closeCropper();
}

document.getElementById('profileForm').addEventListener('submit', function() {
    var base64 = document.getElementById('croppedAvatarBase64').value;
    if (base64 && base64.length > 100) {
        document.getElementById('avatarInput').disabled = true;
    }
});

document.querySelectorAll('.profile-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.profile-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.tab-content').forEach(function(tc) { tc.classList.remove('active'); });
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var msg = document.getElementById('successMsg');
    if (msg) {
        setTimeout(function() {
            msg.style.transition = 'opacity 0.3s, transform 0.3s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            setTimeout(function() { msg.remove(); }, 300);
        }, 5000);
    }
});
</script>
@endpush

@endsection