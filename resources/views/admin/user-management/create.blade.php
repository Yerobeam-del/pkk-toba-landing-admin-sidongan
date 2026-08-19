{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Tambah Akun')
@section('page-title', 'Tambah Akun Baru')

    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-user-management-create.css') }}">


@section('content')

<div class="u-header-row-plain">
    <div>
        <h1 class="u-page-title">Tambah Akun Baru</h1>
        <p class="u-muted">Buat akun pengguna baru</p>
    </div>
    <x-admin.back-button :href="route('admin.user-management.index')" />
</div>

@if($errors->any())
<div class="u-a34">
    <strong>Terjadi kesalahan:</strong>
    <ul style="margin:0.5rem 0 0 0;padding-left:1.25rem">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card u-a71">
    <form action="{{ route('admin.user-management.store') }}" method="POST">
        @csrf

        <div class="u-grid-gap-6">

            {{-- Nama Lengkap (Full Width) --}}
            <div>
                <label class="u-label-simple">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            {{-- Grid 2 Kolom: Email & Phone --}}
            <div class="form-grid-2 u-grid-2-plain">
                {{-- Email --}}
                <div>
                    <label class="u-label-dark">Email <span class="u-text-danger">*</span></label>
                    <div class="email-input-group u-flex-center-gap-2">
                        <input type="text" id="email_username" name="email_username" placeholder="username"
                            value="{{ old('email_username') }}"
                            style="flex:1;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem;outline:none" required>
                        <span class="email-domain" style="padding:0.75rem 1rem;background:#f1f5f9;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem;color:#64748b;font-weight:600">
                            @pkk-toba.id
                        </span>
                    </div>
                    <input type="hidden" name="email" id="email_full">
                    <small style="color:var(--text-muted);margin-top:0.25rem;display:block">Email otomatis: username@pkk-toba.id</small>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="u-label-simple">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number') }}">
                </div>

                {{-- Personal Email --}}
                <div>
                    <label class="u-label-simple">
                        Email Pribadi
                        <span style="font-weight:400;color:var(--text-muted);font-size:0.8rem">(Gmail, Yahoo, dll)</span>
                    </label>
                    <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email') }}" placeholder="namaketua@gmail.com">
                    <small class="u-hint-tight">
                        Email pribadi untuk menerima link reset password jika lupa password.
                    </small>
                </div>
            </div>

            {{-- Grid 2 Kolom: Password & Confirm --}}
            <div class="form-grid-2 u-grid-2-plain">
                <div>
                    <label class="u-label-simple">Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div>
                    <label class="u-label-simple">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            {{-- Grid 2 Kolom: Role & SIDONGAN Role --}}
            <div class="form-grid-2 u-grid-2-plain">
                {{-- Role Admin Panel --}}
                <div>
                    <label class="u-label-simple">Role Admin Panel <span class="u-text-danger">*</span></label>
                    <select name="role_id" id="roleSelect" class="form-control" required >
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            {{-- Hanya tampilkan Super Admin jika user yang login adalah Super Admin --}}
                            @if(auth()->user()->hasRole('super_admin') || $role->name !== 'super_admin')
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->display_name }} - {{ $role->description }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small class="u-hint-tight">
                        Administrator: Akses penuh | Anggota: Akses terbatas
                    </small>
                </div>

                {{-- SIDONGAN Role (Conditional) --}}
                <div class="u-hidden" id="sidonganRoleSection">
                    <label class="u-label-simple">Peran di SIDONGAN <span class="u-text-danger">*</span></label>
                    <select name="sidongan_role" id="sidonganRole" class="form-control">
                        <option value="">-- Pilih Peran --</option>
                        @foreach($sidonganRoles as $key => $label)
                            <option value="{{ $key }}" {{ old('sidongan_role') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <small class="u-hint-tight">
                        Pilih peran untuk akses SIDONGAN
                    </small>
                </div>

                {{-- SIEDA Role (Conditional) --}}
                <div class="u-hidden" id="siedaRoleSection">
                    <label class="u-label-simple">Peran di SIEDA <span class="u-text-danger">*</span></label>
                    <select name="sieda_role" id="siedaRole" class="form-control">
                        <option value="">-- Pilih Peran --</option>
                        <option value="operator" {{ old('sieda_role') == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="kader" {{ old('sieda_role') == 'kader' ? 'selected' : '' }}>Kader</option>
                    </select>
                    <small class="u-hint-tight">
                        Pilih peran untuk akses SIEDA
                    </small>
                </div>

                {{-- SIEDA Wilayah Access --}}
                <div id="siedaWilayahSection" style="display:none; grid-column: 1 / -1;">
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1.25rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0369a1; margin: 0 0 1rem 0;">
                            <i class="fas fa-map-marker-alt u-mr-2"></i>
                            Wilayah Akses SIEDA
                        </h4>

                        <div class="u-grid-2-gap-4">
                            {{-- Kecamatan Dropdown --}}
                            <div id="kecamatanField">
                                <label class="u-a40">
                                    Kecamatan <span class="u-text-danger">*</span>
                                </label>
                                <select name="sieda_kecamatan" id="siedaKecamatan" class="form-control">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <small class="u-a41">
                                    Operator: Akses semua desa di kecamatan ini
                                </small>
                            </div>

                            {{-- Kelurahan Dropdown --}}
                            <div class="u-hidden" id="kelurahanField">
                                <label class="u-a40">
                                    Desa/Kelurahan <span class="u-text-danger">*</span>
                                </label>
                                <select name="sieda_kelurahan" id="siedaKelurahan" class="form-control">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                                <small class="u-a41">
                                    Kader: Hanya akses desa ini
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permission Section (Hanya untuk Anggota) --}}
            <div id="permissionSection" style="display:none;border:1px solid var(--border);border-radius:8px;padding:1.25rem;background:#f8fafc">
                <label class="u-label-simple">Permission Akses</label>
                <small style="color:var(--text-muted);display:block;margin-bottom:1rem">
                    Akses tambahan khusus untuk akun ini saja, di luar akses bawaan role yang dipilih.
                    Tidak memengaruhi akun lain dengan role yang sama.
                </small>

                <div class="permission-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem">
                    @foreach($permissions as $group => $perms)
                        <div style="padding:1rem;background:#fff;border-radius:8px;border:1px solid var(--border)">
                            <h4 style="font-size:0.9rem;font-weight:700;color:var(--primary);margin-bottom:0.75rem;text-transform:capitalize;padding-bottom:0.5rem;border-bottom:1px solid var(--border)">
                                {{ ucfirst(str_replace('-', ' ', $group)) }}
                            </h4>
                            <div style="display:grid;gap:0.5rem">
                                @foreach($perms as $perm)
                                    @if($perm->name !== 'publish-berita')
                                    <label class="custom-checkbox-label" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.4rem 0.5rem;border-radius:6px;transition:all 0.2s">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="permission-checkbox custom-checkbox-input" data-group="{{ $group }}" style="display:none">
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

            {{-- Aplikasi yang Diakses --}}
            <div>
                <label class="u-label-simple">Aplikasi yang Diakses</label>
                <div style="border:1px solid var(--border);border-radius:8px;padding:1rem;max-height:250px;overflow-y:auto;background:#f8fafc">
                    @forelse($applications as $app)
                    <label class="custom-checkbox-label" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.5rem;border-radius:6px;transition:all 0.2s;margin-bottom:0.25rem">
                        <input type="checkbox" name="applications[]" value="{{ $app->id }}" class="application-checkbox custom-checkbox-input" data-app-name="{{ $app->name }}" data-app-short="{{ $app->short_name }}" style="display:none">
                        <div class="custom-checkbox-box u-a42">
                            <svg class="custom-checkbox-check u-check-svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <span class="u-a43">{{ $app->name }}</span>
                    </label>
                    @empty
                    <p style="color:var(--text-muted);margin:0;text-align:center;padding:1rem">Belum ada aplikasi</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--border)">
            <x-admin.cancel-button :href="route('admin.user-management.index')" />
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan Akun
            </button>
        </div>
    </form>
</div>

    <script src="{{ asset('assets/admin/js/admin-user-management-create.js') }}"></script>


@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
