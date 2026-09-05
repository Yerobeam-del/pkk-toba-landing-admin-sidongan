{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-profile-edit.css') }}">

@endpush

@section('content')

@php
// SSO: URL balik ke SIEDA (login otomatis tanpa mengetik kredensial).
$ssoBackUrl = app(\App\Services\SsoTokenService::class)->buildCallbackUrl($user->email);
$appList = [
['name' => 'Admin Panel', 'icon' => 'M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5', 'color' => '#14b8a6', 'url' => route('admin.dashboard'), 'accessible' => true],
['name' => 'SIDONGAN', 'icon' => 'M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z', 'color' => '#8b5cf6', 'url' => 'https://' . config('app.sidongan_domain', 'sidongan.tobakab.go.id'), 'accessible' => $user->hasSidonganAccess()],
['name' => 'SIEDA', 'icon' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z', 'color' => '#0ea5a4', 'url' => $ssoBackUrl, 'accessible' => !empty($user->sieda_role)],
];
@endphp

<div class="u-header-row-plain">
    <div>
        <h1>Edit Profil</h1>
        <p>Kelola informasi akun dan akses aplikasi Anda</p>
    </div>
    @if (session('sso_from_sieda'))
    {{-- Diakses dari SIEDA: tombol Kembali diganti menjadi Kembali ke SIEDA --}}
    <a href="{{ $ssoBackUrl }}" class="profile-nav-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        Kembali ke SIEDA
    </a>
    @else
    <a href="{{ route('admin.dashboard') }}" class="profile-nav-secondary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>
    @endif
</div>

@if(session('success'))
<div class="profile-success-alert" id="successMsg">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="u-a34">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="profile-grid">

    {{-- SIDEBAR --}}
    <div class="profile-sidebar-card">
        <div class="profile-header">
            <div class="profile-avatar-wrap">
                <div class="profile-avatar-lg">
                    @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="profile-avatar-img">
                    @else
                    <div class="profile-avatar-fallback profile-avatar-fallback-lg">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                    @endif
                </div>
                @if($user->hasVerifiedPersonalEmail())
                <div class="profile-avatar-badge">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                @endif
            </div>
            <h3 class="profile-user-name">{{ $user->name }}</h3>
            <p class="profile-user-email">{{ $user->email }}</p>
            <div class="profile-user-tags">
                @if($user->hasSidonganAccess())
                <span class="profile-tag profile-tag-role">{{ $user->sidongan_role_name }}</span>
                @endif
                @if(!empty($user->sieda_role))
                <span class="profile-tag profile-tag-sieda">SIEDA</span>
                @endif
                @if($user->role)
                <span class="profile-tag profile-tag-admin">{{ ucfirst($user->role->name) }}</span>
                @endif
            </div>
        </div>

        {{-- Completion Ring --}}
        <div class="profile-sidebar-section">
            <p class="profile-sidebar-label">Kelengkapan Profil</p>
            <div class="completion-ring">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle class="bg-circle" cx="50" cy="50" r="45"/>
                    <circle class="progress-circle" cx="50" cy="50" r="45" stroke-dashoffset="{{ 283 - (283 * $completionPercentage / 100) }}"/>
                </svg>
                <span class="center-text">{{ $completionPercentage }}%</span>
            </div>
            <div class="profile-progress-wrap">
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
        <div class="profile-sidebar-section">
            <p class="profile-sidebar-label">Info Akun</p>
            <div class="profile-sidebar-list">
                <div class="profile-sidebar-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:var(--text-muted)"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Bergabung {{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div class="profile-sidebar-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:var(--text-muted)"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    <span>Terakhir diperbarui {{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="profile-main-card">
        {{-- Tab list — pola WAI-ARIA Tabs: roving tabindex + navigasi panah
             (lihat admin-profile-edit.js). aria-selected/tabindex disinkronkan JS. --}}
        <div class="profile-tabs" role="tablist" aria-label="Bagian halaman profil">
            <button class="profile-tab active" data-tab="profil" id="tab-btn-profil" role="tab" aria-selected="true" aria-controls="tab-profil">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Informasi Profil
            </button>
            <button class="profile-tab" data-tab="keamanan" id="tab-btn-keamanan" role="tab" aria-selected="false" aria-controls="tab-keamanan" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Keamanan
            </button>
            <button class="profile-tab" data-tab="apps" id="tab-btn-apps" role="tab" aria-selected="false" aria-controls="tab-apps" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Akses Aplikasi
            </button>
            <button class="profile-tab" data-tab="perizinan" id="tab-btn-perizinan" role="tab" aria-selected="false" aria-controls="tab-perizinan" tabindex="-1">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Perizinan
            </button>
        </div>

        {{-- TAB 1: INFORMASI PROFIL --}}
        <div class="tab-content active" id="tab-profil" role="tabpanel" aria-labelledby="tab-btn-profil" tabindex="0">
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf @method('PATCH')
                <div class="profile-form-grid">

                    {{-- Avatar Upload --}}
                    <div class="profile-avatar-upload">
                        <div style="position:relative;flex-shrink:0">
                            <div class="profile-avatar-md">
                                @if($user->avatar)
                                <img id="avatarPreview" src="{{ asset('storage/' . $user->avatar) }}" alt="" class="profile-avatar-img">
                                @else
                                <div id="avatarPlaceholder" class="profile-avatar-fallback profile-avatar-fallback-md">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                                <img id="avatarPreviewImg" class="profile-avatar-img" style="display:none">
                                @endif
                            </div>
                            <button type="button" data-action="pick-avatar" class="profile-avatar-badge profile-avatar-badge-lg">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </button>
                        </div>
                        <div class="u-flex-1">
                            <p class="profile-avatar-upload-info">Foto Profil</p>
                            <p class="profile-avatar-upload-hint">Klik ikon kamera untuk mengganti foto. JPG/PNG/WebP, maks 2MB.</p>
                        </div>
                        <input class="u-hidden" type="file" name="avatar" id="avatarInput" accept="image/*">
                        <input type="hidden" name="cropped_avatar_base64" id="croppedAvatarBase64">
                    </div>

                    {{-- Nama --}}
                    <div class="form-field">
                        <label for="name">Nama Lengkap <span class="u-text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap Anda">
                        @error('name') <small class="u-error-block">{{ $message }}</small> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-field">
                        <label for="email">Email <span class="u-text-danger">*</span></label>
                        <div class="u-flex-center-gap-2">
                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required placeholder="contoh@email.com" style="flex:1">
                            @if($user->email_verified_at)
                            <span class="profile-status-pill profile-status-pill-green"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Terverifikasi</span>
                            @else
                            <span class="profile-status-pill profile-status-pill-amber" title="Mengubah email akan menghapus verifikasi — verifikasi ulang melalui link yang dikirim ke email baru">Belum Verifikasi</span>
                            @endif
                        </div>
                        @error('email') <small class="u-error-block">{{ $message }}</small> @enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div class="form-field">
                        <label for="phone_number">Nomor Telepon</label>
                        <input type="tel" id="phone_number" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" placeholder="Contoh: 0812-3456-7890">
                        <small style="color:var(--text-muted);display:block;margin-top:0.3rem;font-size:0.8rem">Opsional. Nomor yang bisa dihubungi untuk keperluan administrasi.</small>
                        @error('phone_number') <small class="u-error-block">{{ $message }}</small> @enderror
                    </div>

                    {{-- Personal Email --}}
                    <div class="profile-info-box">
                        <label class="profile-info-box-label">
                            Email Pribadi <span class="profile-tag profile-tag-sieda" style="padding:0.15rem 0.45rem;font-size:0.7rem">Digunakan untuk reset password</span>
                        </label>
                        @if($user->hasVerifiedPersonalEmail())
                        <div class="u-flex-center-gap-2">
                            <span class="profile-status-pill profile-status-pill-green" style="font-size:0.9rem">{{ $user->personal_email }}</span>
                            <span class="profile-status-pill profile-status-pill-green">Terverifikasi</span>
                        </div>
                        @elseif($user->personal_email)
                        <div class="u-flex-center-gap-2">
                            <span class="profile-status-pill profile-status-pill-amber" style="font-size:0.9rem">{{ $user->personal_email }}</span>
                            <span class="profile-status-pill profile-status-pill-amber">Menunggu Verifikasi</span>
                        </div>
                        <div class="profile-info-box-hint">
                            Cek email {{ $user->personal_email }} untuk link verifikasi, atau
                            <form action="{{ route('personal-email.resend') }}" method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="profile-link-btn" title="Kirim ulang link verifikasi">kirim ulang link</button>
                            </form>
                            (maks 3x per 30 menit).
                        </div>
                        @else
                        <p class="profile-info-box-hint" style="font-size:0.85rem;margin:0">Belum diatur. <a href="{{ route('personal-email.setup') }}" style="color:var(--primary);font-weight:600;text-decoration:underline">Atur Sekarang</a></p>
                        @endif
                    </div>
                </div>

                {{-- Submit --}}
                <div class="profile-form-footer">
                    <button type="reset" class="profile-submit-btn profile-btn-secondary">Reset</button>
                    <button type="submit" class="profile-submit-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        {{-- TAB 2: KEAMANAN --}}
        <div class="tab-content" id="tab-keamanan" role="tabpanel" aria-labelledby="tab-btn-keamanan" tabindex="0">
            <div style="max-width:500px">
                <h3 class="section-header">Ubah Password</h3>
                <p class="section-desc">Pastikan password Anda kuat dan tidak digunakan di akun lain.</p>

                <form action="{{ route('admin.profile.password.update') }}" method="POST">
                    @csrf @method('PUT')
        <div class="form-field">
                        <label for="current_password">Password Saat Ini <span class="u-text-danger">*</span></label>
                        <div class="u-relative">
                            <input type="password" id="current_password" name="current_password" class="form-control u-a59" required placeholder="Masukkan password saat ini">
                            <button class="u-a60" type="button" data-action="toggle-password" data-target="current_password" tabindex="-1">
                                <svg id="eyeOpenCurrent" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="u-hidden" id="eyeClosedCurrent" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                            </button>
                        </div>
                        @error('current_password') <small class="u-error-block">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-field">
                        <label for="password">Password Baru <span class="u-text-danger">*</span></label>
                        <div class="u-relative">
                            <input type="password" id="password" name="password" class="form-control u-a59" required placeholder="Minimal 8 karakter">
                            <button class="u-a60" type="button" data-action="toggle-password" data-target="password" tabindex="-1">
                                <svg id="eyeOpenPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="u-hidden" id="eyeClosedPassword" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                            </button>
                        </div>
                        <div class="strength-bar"><div class="strength-bar-fill" id="strengthFill"></div></div>
                        <div class="strength-label" id="strengthLabel" style="color:var(--text-muted)"></div>
                        @error('password') <small class="u-error-block">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-field">
                        <label for="password_confirmation">Konfirmasi Password Baru <span class="u-text-danger">*</span></label>
                        <div class="u-relative">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control u-a59" required placeholder="Ketik ulang password baru">
                            <button class="u-a60" type="button" data-action="toggle-password" data-target="password_confirmation" tabindex="-1">
                                <svg id="eyeOpenConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="u-hidden" id="eyeClosedConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                            </button>
                        </div>
                        <small id="matchMsg" style="display:block;margin-top:0.3rem;font-size:0.8rem"></small>
                    </div>
                    <button type="submit" class="profile-submit-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Update Password
                    </button>
                </form>

                <div class="profile-form-section">
                    <h4 class="profile-h4">Riwayat Keamanan</h4>
                    <div class="profile-sidebar-list">
                        <div class="u-a56">
                            <div class="profile-perm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
                            <div><p class="profile-app-label">Akun Dibuat</p><p class="profile-app-url">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y, H:i') : '-' }}</p></div>
                        </div>
                        <div class="u-a56">
                            <div class="profile-perm-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></div>
                            <div><p class="profile-app-label">Terakhir Diperbarui</p><p class="profile-app-url">{{ $user->updated_at ? $user->updated_at->translatedFormat('d F Y, H:i') : '-' }} ({{ $user->updated_at ? $user->updated_at->diffForHumans() : '-' }})</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: AKSES APLIKASI --}}
        <div class="tab-content" id="tab-apps" role="tabpanel" aria-labelledby="tab-btn-apps" tabindex="0">
            <h3 class="section-header">Akses Aplikasi</h3>
            <p class="section-desc">Aplikasi yang dapat Anda akses dengan akun ini.</p>
            <div class="u-grid-gap-3">
                @foreach($appList as $app)
                <div class="app-card" style="opacity:{{ $app['accessible'] ? '1' : '0.5' }}">
                    <div class="app-icon-box" style="background:{{ $app['color'] }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="{{ $app['icon'] }}"/></svg>
                    </div>
                    <div class="u-flex-1">
                        <p class="profile-app-label" style="font-size:0.9rem">{{ $app['name'] }}</p>
                        @if($app['accessible'])
                        <span class="profile-app-status">Akses Aktif</span>
                        @else
                        <span class="profile-app-status profile-app-status-muted">Tidak Ada Akses</span>
                        @endif
                    </div>
                    @if($app['url'] !== '#' && $app['accessible'])
                    <a href="{{ $app['url'] }}" target="_blank" rel="noopener noreferrer" class="profile-app-open-btn" style="background:{{ $app['color'] }}">Buka</a>
                    @endif
                </div>
                @endforeach

                @if($linkedApplications && $linkedApplications->count() > 0)
                <h4 class="profile-h4 profile-h4-muted">Aplikasi Tambahan</h4>
                @foreach($linkedApplications as $lap)
                <div class="app-card">
                    <div class="app-icon-box" style="background:{{ $lap->effective_color ?? '#14b8a6' }}">
                        @if($lap->icon)
                        <img src="{{ $lap->icon }}" alt="" style="width:24px;height:24px">
                        @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        @endif
                    </div>
                    <div class="u-flex-1">
                        <p class="profile-app-label" style="font-size:0.9rem">{{ $lap->name }}</p>
                        <span class="profile-app-status">Akses Aktif</span>
                    </div>
                    @if($lap->url)
                    <a href="{{ $lap->url }}" target="_blank" rel="noopener noreferrer" class="profile-app-open-btn" style="background:{{ $lap->effective_color ?? '#14b8a6' }}">Buka</a>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>

        {{-- TAB 4: PERIZINAN --}}
        <div class="tab-content" id="tab-perizinan" role="tabpanel" aria-labelledby="tab-btn-perizinan" tabindex="0">
            <h3 class="section-header">Izin Efektif</h3>
            <p class="section-desc">Semua izin akses yang dimiliki akun ini dari role dan izin khusus.</p>
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1.5rem">
                @forelse($effectivePermissions as $perm)
                <span class="perm-tag">{{ $perm }}</span>
                @empty
                <p style="color:var(--text-muted);font-size:0.9rem">Tidak ada izin khusus.</p>
                @endforelse
            </div>
            @if($user->role)
            <div class="profile-info-box">
                <div class="u-flex-center-gap-3">
                    <div class="profile-perm-icon profile-perm-icon-sm"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                    <div><p class="profile-app-label" style="font-size:0.9rem">Role: {{ ucfirst($user->role->name) }}</p><p class="profile-app-url">Izin bawaan dari role ini ditambah izin khusus akun.</p></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- CROPPER MODAL --}}
<div id="cropperModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.75);z-index:9999;align-items:center;justify-content:center">
    <div class="profile-modal-card">
        <div class="profile-modal-head">
            <h3 class="profile-modal-title">Crop Foto Profil</h3>
            <button type="button" data-action="close-cropper" class="profile-modal-close" aria-label="Tutup dialog crop foto"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div style="margin-bottom:1rem;background:#000;border-radius:8px;overflow:hidden;max-height:50vh">
            <img id="cropperImage" style="max-width:100%;display:block">
        </div>
        <div class="u-a61">
            <button type="button" data-action="close-cropper" class="profile-submit-btn profile-btn-secondary">Batal</button>
            <button type="button" data-action="crop-save" class="profile-submit-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Potong & Simpan
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="{{ asset('assets/admin/js/admin-profile-edit.js') }}"></script>

@endpush

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
