{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Tambah Akun')
@section('page-title', 'Tambah Akun Baru')

    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-user-management-create.css') }}">

@section('content')

{{-- Header --}}
<div class="u-header-row-plain">
    <div>
        <h1 class="u-page-title">Tambah Akun Baru</h1>
        <p class="u-muted">Buat akun pengguna baru untuk sistem PKK</p>
    </div>
    <x-admin.back-button :href="route('admin.user-management.index')" />
</div>

{{-- Form Card --}}
<div class="card u-a71">
    <form action="{{ route('admin.user-management.store') }}" method="POST" id="createUserForm">
        @csrf

        {{-- =============================================
             Section 1: Informasi Dasar
             ============================================= --}}
        <div class="form-section">
            <div class="form-section-header">
                <span class="form-section-number">1</span>
                <div>
                    <h3 class="form-section-title">Informasi Dasar</h3>
                    <p class="form-section-desc">Nama lengkap dan kontak pengguna</p>
                </div>
            </div>

            <div class="form-section-body">
                {{-- Foto Profil --}}
                <div class="form-field">
                    <label class="u-label-slate">Foto Profil <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem">(Opsional)</span></label>
                    <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*" style="cursor:pointer">
                    <small class="u-hint-line">JPG/PNG, maksimal 2MB. Foto akan di-crop otomatis menjadi persegi.</small>

                    {{-- Preview Container --}}
                    <div id="previewContainer" class="avatar-preview-container">
                        <div class="avatar-preview-row">
                            <img id="photoPreview" class="avatar-preview-img" data-action="open-crop">
                            <div id="avatarPlaceholder" class="avatar-placeholder">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <span>Belum ada foto</span>
                            </div>
                            <div id="avatarText" class="avatar-text">
                                <div class="avatar-text-primary">Klik foto untuk atur crop</div>
                                <div class="avatar-text-secondary">Format: JPG/PNG, maks 2MB</div>
                            </div>
                            <button type="button" id="removePhotoBtn" class="avatar-remove-btn" style="display:none" data-action="remove-photo">Hapus</button>
                        </div>
                    </div>
                    <input type="hidden" name="cropped_photo" id="croppedPhoto">
                </div>

                {{-- Nama Lengkap --}}
                <div class="form-field">
                    <label class="u-label-slate">Nama Lengkap <span class="u-text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Siti Nurhaliza">
                    @error('name')
                        <small class="u-error-block">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Grid 2 Kolom: Email & Telepon --}}
                <div class="form-grid-2">
                    {{-- Email --}}
                    <div class="form-field">
                        <label class="u-label-slate">Email Kantor <span class="u-text-danger">*</span></label>
                        <div class="email-input-group">
                            <input type="text" id="email_username" name="email_username" class="form-control email-username-input"
                                placeholder="username" value="{{ old('email_username') }}" required>
                            <span class="email-domain">@pkk-toba.id</span>
                        </div>
                        <input type="hidden" name="email" id="email_full">
                        <small class="u-hint-line">Email otomatis: <span id="email_preview" class="u-email-preview">username@pkk-toba.id</span></small>
                        <div id="emailStatus" class="email-status"></div>
                        @error('email')
                            <small class="u-error-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Telepon --}}
                    <div class="form-field">
                        <label class="u-label-slate">Nomor Telepon</label>
                        <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}" placeholder="08123456789">
                        @error('phone_number')
                            <small class="u-error-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Email Pribadi --}}
                <div class="form-field">
                    <label class="u-label-slate">
                        Email Pribadi
                        <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem">(Opsional)</span>
                    </label>
                    <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email') }}" placeholder="namaketua@gmail.com">
                    <small class="u-hint-line">Email pribadi untuk menerima link reset password jika lupa password.</small>
                    @error('personal_email')
                        <small class="u-error-block">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        {{-- =============================================
             Section 2: Password
             ============================================= --}}
        <div class="form-section">
            <div class="form-section-header">
                <span class="form-section-number">2</span>
                <div>
                    <h3 class="form-section-title">Password</h3>
                    <p class="form-section-desc">Buat kata sandi untuk akun ini</p>
                </div>
            </div>

            <div class="form-section-body">
                {{-- Generate Password Button --}}
                <div class="form-field">
                    <button type="button" class="generate-password-btn" onclick="generateRandomPassword()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                        </svg>
                        Generate Password Acak
                    </button>
                    <small class="u-hint-line">Klik untuk membuat password acak yang kuat (16 karakter). Password akan otomatis terisi & ter-copy ke clipboard.</small>
                </div>

                <div class="form-grid-2">
                    <div class="form-field">
                        <label class="u-label-slate">Password <span class="u-text-danger">*</span></label>
                        <div class="u-relative">
                            <input type="password" name="password" id="passwordInput" class="form-control" required placeholder="Minimal 8 karakter" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('passwordInput', this)" tabindex="-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength" style="display:none">
                            <div class="password-strength-bar" id="passwordStrengthBar"></div>
                        </div>
                        @error('password')
                            <small class="u-error-block">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label class="u-label-slate">Konfirmasi Password <span class="u-text-danger">*</span></label>
                        <div class="u-relative">
                            <input type="password" name="password_confirmation" id="passwordConfirmInput" class="form-control" required placeholder="Ulangi password" autocomplete="new-password">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('passwordConfirmInput', this)" tabindex="-1">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                        <div class="password-match" id="passwordMatch" style="display:none"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =============================================
             Section 3: Role & Peran
             ============================================= --}}
        <div class="form-section">
            <div class="form-section-header">
                <span class="form-section-number">3</span>
                <div>
                    <h3 class="form-section-title">Role & Peran</h3>
                    <p class="form-section-desc">Tentukan hak akses pengguna di sistem</p>
                </div>
            </div>

            <div class="form-section-body">
                <div class="form-grid-2">
                    {{-- Role Admin Panel --}}
                    <div class="form-field">
                        <label class="u-label-slate">Role Admin Panel <span class="u-text-danger">*</span></label>
                        <select name="role_id" id="roleSelect" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                @if(auth()->user()->hasRole('super_admin') || $role->name !== 'super_admin')
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }} — {{ $role->description }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="u-hint-line">Administrator: Akses penuh | Anggota: Akses terbatas</small>
                        @error('role_id')
                            <small class="u-error-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- SIDONGAN Role (Conditional) --}}
                    <div class="form-field u-hidden" id="sidonganRoleSection">
                        <label class="u-label-slate">Peran di SIDONGAN <span class="u-text-danger">*</span></label>
                        <select name="sidongan_role" id="sidonganRole" class="form-control">
                            <option value="">-- Pilih Peran --</option>
                            @foreach($sidonganRoles as $key => $label)
                                <option value="{{ $key }}" {{ old('sidongan_role') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="u-hint-line">Pilih peran untuk akses SIDONGAN</small>
                        @error('sidongan_role')
                            <small class="u-error-block">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- SIEDA Role (Conditional) --}}
                    <div class="form-field u-hidden" id="siedaRoleSection">
                        <label class="u-label-slate">Peran di SIEDA <span class="u-text-danger">*</span></label>
                        <select name="sieda_role" id="siedaRole" class="form-control">
                            <option value="">-- Pilih Peran --</option>
                            <option value="operator" {{ old('sieda_role') == 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="kader" {{ old('sieda_role') == 'kader' ? 'selected' : '' }}>Kader</option>
                            <option value="viewer" {{ old('sieda_role') == 'viewer' ? 'selected' : '' }}>Viewer (Hanya Baca)</option>
                        </select>
                        <small class="u-hint-line">Pilih peran untuk akses SIEDA. Viewer hanya bisa melihat data.</small>
                        @error('sieda_role')
                            <small class="u-error-block">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- SIEDA Wilayah Access --}}
                <div id="siedaWilayahSection" class="form-field" style="display:none">
                    <div class="wilayah-panel">
                        <div class="wilayah-panel-header">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <h4>Wilayah Akses SIEDA</h4>
                        </div>

                        <div class="form-grid-2">
                            <div id="kecamatanField">
                                <label class="u-label-slate">Kecamatan <span class="u-text-danger">*</span></label>
                                <select name="sieda_kecamatan" id="siedaKecamatan" class="form-control">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <small class="u-hint-line">Operator: Akses semua desa di kecamatan ini</small>
                            </div>

                            <div id="kelurahanField" class="u-hidden">
                                <label class="u-label-slate">Desa/Kelurahan <span class="u-text-danger">*</span></label>
                                <select name="sieda_kelurahan" id="siedaKelurahan" class="form-control">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                                <small class="u-hint-line">Kader: Hanya akses desa ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =============================================
             Section 4: Permission (Hanya untuk Anggota)
             ============================================= --}}
        <div id="permissionSection" class="form-section" style="display:none">
            <div class="form-section-header">
                <span class="form-section-number form-section-number--accent">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <div>
                    <h3 class="form-section-title">Permission Akses</h3>
                    <p class="form-section-desc">Hak akses tambahan khusus untuk akun ini saja, di luar akses bawaan role yang dipilih</p>
                </div>
            </div>

            <div class="form-section-body">
                <div class="permission-grid">
                    @foreach($permissions as $group => $perms)
                        <div class="permission-card">
                            <h4 class="permission-card-title">{{ ucfirst(str_replace('-', ' ', $group)) }}</h4>
                            <div class="permission-card-list">
                                @foreach($perms as $perm)
                                    @if($perm->name !== 'publish-berita')
                                    <label class="permission-item">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                            class="permission-checkbox custom-checkbox-input"
                                            data-group="{{ $group }}"
                                            style="display:none">
                                        <div class="custom-checkbox-box u-a42">
                                            <svg class="custom-checkbox-check u-check-svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </div>
                                        <span class="u-a43">{{ $perm->display_name }}</span>
                                    </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- =============================================
             Section 5: Akses Aplikasi
             ============================================= --}}
        <div class="form-section">
            <div class="form-section-header">
                <span class="form-section-number">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                        <line x1="8" y1="21" x2="16" y2="21"/>
                        <line x1="12" y1="17" x2="12" y2="21"/>
                    </svg>
                </span>
                <div>
                    <h3 class="form-section-title">Akses Aplikasi</h3>
                    <p class="form-section-desc">Pilih aplikasi mana saja yang dapat diakses oleh pengguna ini</p>
                </div>
            </div>

            <div class="form-section-body">
                <div class="app-grid">
                    @forelse($applications as $app)
                    <label class="app-item">
                        <input type="checkbox" name="applications[]" value="{{ $app->id }}"
                            class="application-checkbox custom-checkbox-input"
                            data-app-name="{{ $app->name }}"
                            data-app-short="{{ $app->short_name }}"
                            style="display:none">
                        <div class="app-item-content">
                            <div class="custom-checkbox-box u-a42">
                                <svg class="custom-checkbox-check u-check-svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <div>
                                <span class="app-item-name">{{ $app->name }}</span>
                                @if($app->short_name)
                                    <span class="app-item-slug">{{ $app->short_name }}</span>
                                @endif
                            </div>
                        </div>
                    </label>
                    @empty
                    <div class="app-empty">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted)">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                        <span>Belum ada aplikasi yang tersedia</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- =============================================
             Submit Actions
             ============================================= --}}
        <div class="form-actions">
            <x-admin.cancel-button :href="route('admin.user-management.index')" />
            <button type="submit" class="btn btn-primary form-submit-btn" id="submitBtn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                <span>Simpan Akun</span>
            </button>
        </div>
    </form>
</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="{{ asset('assets/admin/js/admin-user-management-create.js') }}"></script>
    <script src="{{ asset('assets/admin/js/admin-user-avatar.js') }}"></script>

@endsection

{{-- Crop Modal --}}
<div id="cropModal" class="crop-modal-overlay">
    <div class="crop-modal">
        <div class="crop-modal-header">
            <h3>Atur Foto Profil</h3>
            <button type="button" class="crop-modal-close" data-action="close-crop">&times;</button>
        </div>
        <div class="crop-modal-body">
            <img id="cropImage" class="crop-modal-image">
        </div>
        <div class="crop-modal-footer">
            <div class="crop-modal-tools">
                <button type="button" class="btn u-a13" data-action="rotate-crop" data-deg="-90">↺ Putar Kiri</button>
                <button type="button" class="btn u-a13" data-action="rotate-crop" data-deg="90">Putar Kanan ↻</button>
                <button type="button" class="btn u-a13" data-action="reset-crop">Reset</button>
            </div>
            <div class="crop-modal-hint">Drag untuk geser, scroll untuk zoom</div>
            <div class="crop-modal-actions">
                <button type="button" class="btn btn-cancel" data-action="close-crop">Batal</button>
                <button type="button" class="btn btn-primary form-submit-btn" data-action="apply-crop">Terapkan Crop</button>
            </div>
        </div>
    </div>
</div>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
