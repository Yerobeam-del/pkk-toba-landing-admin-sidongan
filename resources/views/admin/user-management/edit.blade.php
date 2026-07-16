@extends('admin.layouts.app')
@section('title', 'Edit Akun')
@section('page-title', 'Edit Akun')

<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .form-grid-2 {
        grid-template-columns: 1fr !important;
    }
    
    .permission-grid {
        grid-template-columns: 1fr !important;
    }
    
    .email-input-group {
        flex-direction: column !important;
    }
    
    .email-input-group input[type="text"] {
        width: 100% !important;
    }
    
    .email-domain {
        width: 100% !important;
        text-align: center !important;
    }
}
</style>

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Edit Akun</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Update informasi akun {{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.user-management.index') }}" class="btn">← Kembali</a>
</div>

@if($errors->any())
<div style="background:#fef2f2;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#dc2626">
    <strong>Terjadi kesalahan:</strong>
    <ul style="margin:0.5rem 0 0 0;padding-left:1.25rem">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card" style="padding:1.5rem;border-radius:12px">
    <form action="{{ route('admin.user-management.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="display:grid;gap:1.5rem">
            
            {{-- Nama Lengkap (Full Width) --}}
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem">Nama Lengkap *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            
            {{-- Grid 2 Kolom: Email & Phone --}}
            <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                {{-- Email --}}
                <div>
                    <label style="display:block;font-weight:600;margin-bottom:0.5rem;color:var(--text-dark)">Email <span style="color:#ef4444">*</span></label>
                    @php
                        $emailParts = explode('@', $user->email);
                        $emailUsername = $emailParts[0] ?? '';
                    @endphp
                    <div class="email-input-group" style="display:flex;align-items:center;gap:0.5rem">
                        <input type="text" id="email_username" name="email_username" placeholder="username" 
                            value="{{ old('email_username', $emailUsername) }}"
                            style="flex:1;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem;outline:none"
                            onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'" required>
                        <span class="email-domain" style="padding:0.75rem 1rem;background:#f1f5f9;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem;color:#64748b;font-weight:600">
                            @pkk-toba.id
                        </span>
                    </div>
                    <input type="hidden" name="email" id="email_full" value="{{ old('email', $user->email) }}">
                    <small style="color:var(--text-muted);margin-top:0.25rem;display:block">Email otomatis: username@pkk-toba.id</small>
                </div>
                
                {{-- Phone --}}
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem">Nomor Telepon</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}">
                </div>
            </div>
            
            {{-- Grid 2 Kolom: Password & Confirm --}}
            <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    <small style="color:var(--text-muted);display:block;margin-top:0.25rem">Kosongkan jika tidak ingin mengubah</small>
                </div>
                
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            {{-- Grid 2 Kolom: Role & SIDONGAN Role --}}
            <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem">
                {{-- Role Admin Panel --}}
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem">Role Admin Panel <span style="color:#ef4444">*</span></label>
                    <select name="role_id" id="roleSelect" class="form-control" required onchange="togglePermissionSection()">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }} - {{ $role->description }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted);display:block;margin-top:0.25rem">
                        Administrator: Akses penuh | Anggota: Akses terbatas
                    </small>
                </div>
                
                {{-- SIDONGAN Role --}}
                <div id="sidonganRoleSection" style="display:none">
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem">Peran di SIDONGAN</label>
                    <select name="sidongan_role" id="sidonganRole" class="form-control">
                        <option value="">-- Pilih Peran --</option>
                        @foreach($sidonganRoles as $key => $label)
                            <option value="{{ $key }}" {{ old('sidongan_role', $user->sidongan_role) == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted);display:block;margin-top:0.25rem">
                        Pilih peran untuk akses SIDONGAN
                    </small>
                </div>
                                
                {{-- SIEDA Role (Conditional) --}}
                <div id="siedaRoleSection" style="display:none">
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem">Peran di SIEDA</label>
                    <select name="sieda_role" id="siedaRole" class="form-control">
                        <option value="">-- Pilih Peran --</option>
                        <option value="operator" {{ old('sieda_role', $user->sieda_role) == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="kader" {{ old('sieda_role', $user->sieda_role) == 'kader' ? 'selected' : '' }}>Kader</option>
                    </select>
                    <small style="color:var(--text-muted);display:block;margin-top:0.25rem">
                        Pilih peran untuk akses SIEDA
                    </small>
                </div>

                {{-- SIEDA Wilayah Access --}}
                <div id="siedaWilayahSection" style="display:none; grid-column: 1 / -1;">
                    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 1.25rem;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #0369a1; margin: 0 0 1rem 0;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem;"></i>
                            Wilayah Akses SIEDA
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            {{-- Kecamatan Dropdown --}}
                            <div id="kecamatanField">
                                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem; font-size: 0.9rem;">
                                    Kecamatan <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="sieda_kecamatan" id="siedaKecamatan" class="form-control">
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                    Operator: Akses semua desa di kecamatan ini
                                </small>
                            </div>
                            
                            {{-- Kelurahan Dropdown --}}
                            <div id="kelurahanField" style="display: none;">
                                <label style="font-weight: 600; display: block; margin-bottom: 0.5rem; font-size: 0.9rem;">
                                    Desa/Kelurahan <span style="color: #ef4444;">*</span>
                                </label>
                                <select name="sieda_kelurahan" id="siedaKelurahan" class="form-control">
                                    <option value="">-- Pilih Desa/Kelurahan --</option>
                                </select>
                                <small style="color: var(--text-muted); display: block; margin-top: 0.25rem;">
                                    Kader: Hanya akses desa ini
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permission Section (Hanya untuk Anggota) --}}
            <div id="permissionSection" style="display:none;border:1px solid var(--border);border-radius:8px;padding:1.25rem;background:#f8fafc">
                <label style="font-weight:600;display:block;margin-bottom:0.5rem">Permission Akses <span style="color:#ef4444">*</span></label>
                <small style="color:var(--text-muted);display:block;margin-bottom:1rem">Pilih modul yang bisa diakses user ini</small>
                
                <div class="permission-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem">
                    @foreach($permissions as $group => $perms)
                        <div style="padding:1rem;background:#fff;border-radius:8px;border:1px solid var(--border)">
                            <h4 style="font-size:0.9rem;font-weight:700;color:var(--primary);margin-bottom:0.75rem;text-transform:capitalize;padding-bottom:0.5rem;border-bottom:1px solid var(--border)">
                                {{ ucfirst(str_replace('-', ' ', $group)) }}
                            </h4>
                            <div style="display:grid;gap:0.5rem">
                                @foreach($perms as $perm)
                                    @if($perm->name !== 'publish-berita')
                                    <label class="custom-checkbox-label" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.4rem 0.5rem;border-radius:6px;transition:all 0.2s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" 
                                            class="permission-checkbox custom-checkbox-input" 
                                            data-group="{{ $group }}"
                                            {{ in_array($perm->id, $userPermissions) ? 'checked' : '' }}
                                            style="display:none">
                                        <div class="custom-checkbox-box" style="width:20px;height:20px;border:2px solid #cbd5e1;border-radius:5px;background:#fff;transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                            <svg class="custom-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1)">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        </div>
                                        <span style="font-size:0.9rem;color:#334155;user-select:none">{{ $perm->display_name }}</span>
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
                <label style="font-weight:600;display:block;margin-bottom:0.5rem">Aplikasi yang Diakses</label>
                <div style="border:1px solid var(--border);border-radius:8px;padding:1rem;max-height:250px;overflow-y:auto;background:#f8fafc">
                    @forelse($applications as $app)
                    <label class="custom-checkbox-label" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.5rem;border-radius:6px;transition:all 0.2s;margin-bottom:0.25rem" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                        <input type="checkbox" name="applications[]" value="{{ $app->id }}" 
                            class="application-checkbox custom-checkbox-input" 
                            data-app-name="{{ $app->name }}" 
                            data-app-short="{{ $app->short_name }}"
                            {{ in_array($app->id, $userApplications) ? 'checked' : '' }}
                            style="display:none">
                        <div class="custom-checkbox-box" style="width:20px;height:20px;border:2px solid #cbd5e1;border-radius:5px;background:#fff;transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg class="custom-checkbox-check" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1)">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <span style="font-size:0.9rem;color:#334155;user-select:none">{{ $app->name }}</span>
                    </label>
                    @empty
                    <p style="color:var(--text-muted);margin:0;text-align:center;padding:1rem">Belum ada aplikasi</p>
                    @endforelse
                </div>
            </div>
        </div>
        
        <div style="margin-top:1.5rem;display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid var(--border)">
            <a href="{{ route('admin.user-management.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Update Akun
            </button>
        </div>
    </form>
</div>

<script>
// ==========================================
// CUSTOM CHECKBOX HANDLER
// ==========================================
function initCustomCheckboxes() {
    const checkboxes = document.querySelectorAll('.custom-checkbox-input');
    
    checkboxes.forEach(checkbox => {
        const label = checkbox.closest('.custom-checkbox-label');
        const box = label.querySelector('.custom-checkbox-box');
        const check = label.querySelector('.custom-checkbox-check');
        
        // Set initial state (penting untuk edit - checkbox yang sudah checked)
        updateCustomCheckbox(box, check, checkbox.checked);
        
        // On change
        checkbox.addEventListener('change', function() {
            updateCustomCheckbox(box, check, this.checked);
        });
    });
}

function updateCustomCheckbox(box, check, isChecked) {
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

// ==========================================
// EMAIL GENERATOR
// ==========================================
function updateEmail() {
    const username = document.getElementById('email_username').value.trim();
    const fullEmail = document.getElementById('email_full');
    fullEmail.value = username ? username + '@pkk-toba.id' : '';
}

// ==========================================
// TOGGLE PERMISSION SECTION
// ==========================================
function togglePermissionSection() {
    const roleSelect = document.getElementById('roleSelect');
    const permissionSection = document.getElementById('permissionSection');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption ? selectedOption.text.toLowerCase() : '';
    
    if (roleName.includes('anggota')) {
        permissionSection.style.display = 'block';
    } else {
        permissionSection.style.display = 'none';
        // Uncheck semua permission untuk administrator
        const checkboxes = document.querySelectorAll('#permissionSection input[type="checkbox"]');
        checkboxes.forEach(cb => {
            cb.checked = false;
            const label = cb.closest('.custom-checkbox-label');
            const box = label.querySelector('.custom-checkbox-box');
            const check = label.querySelector('.custom-checkbox-check');
            updateCustomCheckbox(box, check, false);
        });
    }
}

// ==========================================
// INITIALIZATION
// ==========================================
document.getElementById('email_username').addEventListener('input', updateEmail);

document.addEventListener('DOMContentLoaded', function() {
    // Init email
    updateEmail();
    
    // Init permission section
    togglePermissionSection();
    
    // Init custom checkboxes
    initCustomCheckboxes();
    
    // SIDONGAN Role Logic
    const checkboxes = document.querySelectorAll('input[name="applications[]"]');
    const sidonganRoleSection = document.getElementById('sidonganRoleSection');
    const sidonganRoleSelect = document.getElementById('sidonganRole');
    
    function checkSidonganStatus() {
        let isSidonganChecked = false;
        
        checkboxes.forEach(checkbox => {
            const appShort = (checkbox.dataset.appShort || '').toLowerCase();
            if (appShort === 'sidongan' && checkbox.checked) {
                isSidonganChecked = true;
            }
        });
        
        if (isSidonganChecked) {
            sidonganRoleSection.style.display = 'block';
            if (sidonganRoleSelect) sidonganRoleSelect.required = true;
        } else {
            sidonganRoleSection.style.display = 'none';
            if (sidonganRoleSelect) {
                sidonganRoleSelect.required = false;
                sidonganRoleSelect.value = '';
            }
        }
    }
    
    // SIEDA Role Logic
    const siedaRoleSection = document.getElementById('siedaRoleSection');
    const siedaRoleSelect = document.getElementById('siedaRole');

    function checkSiedaStatus() {
        let isSiedaChecked = false;

        checkboxes.forEach(checkbox => {
            const appShort = (checkbox.dataset.appShort || '').toLowerCase();
            if (appShort === 'sieda' && checkbox.checked) {
                isSiedaChecked = true;
            }
        });

        if (isSiedaChecked) {
            siedaRoleSection.style.display = 'block';
        } else {
            siedaRoleSection.style.display = 'none';
            if (siedaRoleSelect) {
                siedaRoleSelect.value = '';
            }
        }
    }

    // SIEDA Wilayah Access Logic
    const siedaWilayahSection = document.getElementById('siedaWilayahSection');
    const siedaKecamatanSelect = document.getElementById('siedaKecamatan');
    const siedaKelurahanSelect = document.getElementById('siedaKelurahan');
    const kecamatanField = document.getElementById('kecamatanField');
    const kelurahanField = document.getElementById('kelurahanField');
    
    // Load Kecamatan from API
    async function loadKecamatan() {
        try {
            // Kabupaten Toba code is 12.12
            const response = await fetch('/api/v1/wilayah/districts/12.12');
            const result = await response.json();
            
            if (result.success && result.data) {
                siedaKecamatanSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                result.data.forEach(kec => {
                    const option = document.createElement('option');
                    option.value = kec.code;
                    option.textContent = kec.name;
                    option.dataset.code = kec.code;
                    siedaKecamatanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kecamatan:', error);
            siedaKecamatanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Load Kelurahan based on Kecamatan
    async function loadKelurahan(districtCode) {
        if (!districtCode) {
            siedaKelurahanSelect.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
            return;
        }
        
        try {
            const response = await fetch(`/api/v1/wilayah/villages/${districtCode}`);
            const result = await response.json();
            
            if (result.success && result.data) {
                siedaKelurahanSelect.innerHTML = '<option value="">-- Pilih Desa/Kelurahan --</option>';
                result.data.forEach(kel => {
                    const option = document.createElement('option');
                    option.value = kel.code;
                    option.textContent = kel.name;
                    siedaKelurahanSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading kelurahan:', error);
            siedaKelurahanSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        }
    }
    
    // Handle SIEDA Role Change
    function handleSiedaRoleChange() {
        const selectedRole = siedaRoleSelect ? siedaRoleSelect.value : '';
        
        if (selectedRole === 'operator' || selectedRole === 'kader') {
            siedaWilayahSection.style.display = 'block';
            loadKecamatan();
            
            if (selectedRole === 'operator') {
                // Operator: hanya pilih kecamatan
                kecamatanField.style.display = 'block';
                kelurahanField.style.display = 'none';
                siedaKecamatanSelect.required = true;
                siedaKelurahanSelect.required = false;
                siedaKelurahanSelect.value = '';
            } else if (selectedRole === 'kader') {
                // Kader: pilih kecamatan dan kelurahan
                kecamatanField.style.display = 'block';
                kelurahanField.style.display = 'block';
                siedaKecamatanSelect.required = true;
                siedaKelurahanSelect.required = true;
            }
        } else {
            siedaWilayahSection.style.display = 'none';
            siedaKecamatanSelect.required = false;
            siedaKelurahanSelect.required = false;
            siedaKecamatanSelect.value = '';
            siedaKelurahanSelect.value = '';
        }
    }
    
    // Event listener for SIEDA role change
    if (siedaRoleSelect) {
        siedaRoleSelect.addEventListener('change', handleSiedaRoleChange);
    }
    
    // Event listener for Kecamatan change
    if (siedaKecamatanSelect) {
        siedaKecamatanSelect.addEventListener('change', function() {
            if (siedaRoleSelect && siedaRoleSelect.value === 'kader') {
                loadKelurahan(this.value);
            }
        });
    }
    
    // Call on initial load if editing
    if (siedaRoleSelect && siedaRoleSelect.value) {
        handleSiedaRoleChange();
    }

    // Pre-fill values for edit
    if (siedaRoleSelect && siedaRoleSelect.value) {
        handleSiedaRoleChange();
        
        // Pre-fill kecamatan
        const savedKecamatan = "{{ old('sieda_kecamatan', $user->sieda_kecamatan) }}";
        if (savedKecamatan && siedaKecamatanSelect) {
            setTimeout(() => {
                siedaKecamatanSelect.value = savedKecamatan;
                
                // If kader, load kelurahan
                if (siedaRoleSelect.value === 'kader') {
                    loadKelurahan(savedKecamatan).then(() => {
                        const savedKelurahan = "{{ old('sieda_kelurahan', $user->sieda_kelurahan) }}";
                        if (savedKelurahan && siedaKelurahanSelect) {
                            siedaKelurahanSelect.value = savedKelurahan;
                        }
                    });
                }
            }, 500);
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkSiedaStatus);
    });

    // Initial check
    checkSiedaStatus();
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkSidonganStatus);
    });
    
    checkSidonganStatus();
});
</script>

@endsection