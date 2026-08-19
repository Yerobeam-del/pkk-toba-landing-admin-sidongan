{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Tambah Aplikasi')
@section('page-title', 'Tambah Aplikasi Baru')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-aplikasi-create.css') }}">


{{-- Header --}}
<div class="aplikasi-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title">Tambah Aplikasi</h1>
        <p class="u-muted">Tambahkan aplikasi atau sistem informasi baru ke dalam daftar</p>
    </div>
    <x-admin.back-button :href="route('admin.aplikasi.index')" />
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.aplikasi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        {{-- Nama Lengkap --}}
        <div class="u-mb-6">
            <label class="u-label">Nama Aplikasi Lengkap *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: SIEDA - Sistem Informasi E-Dasawisma">
            <small class="u-hint">Nama lengkap aplikasi yang akan ditampilkan di website</small>
        </div>

        {{-- Short Name & Category --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Nama Singkat *</label>
                <input type="text" name="short_name" id="shortNameInput" class="form-control" value="{{ old('short_name') }}" required placeholder="Contoh: SIEDA" style="text-transform:uppercase" maxlength="50">
                <small class="u-hint">Singkatan unik untuk icon placeholder</small>
            </div>
            <div>
                <label class="u-label">Kategori *</label>
                <select name="category" class="form-control" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="layanan" {{ old('category') == 'layanan' ? 'selected' : '' }}>Layanan</option>
                    <option value="aplikasi" {{ old('category') == 'aplikasi' ? 'selected' : '' }}>Aplikasi</option>
                </select>
            </div>
        </div>

        {{-- Description --}}
        <div class="u-mb-6">
            <label class="u-label">Deskripsi Lengkap *</label>
            <textarea name="description" id="description" class="form-control" rows="4" required maxlength="1000" placeholder="Deskripsi detail tentang aplikasi, fitur, dan fungsionalitas">{{ old('description') }}</textarea>
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
        <div class="u-mb-6">
            <label class="u-label">Poin-Poin Fitur Aplikasi</label>
            <small style="color:var(--text-muted);display:block;margin-bottom:1rem;font-size:0.8rem">Tambahkan 2-5 poin keunggulan/fitur aplikasi</small>
            
            <div id="features-container">
                {{-- Fitur 1 --}}
                <div class="feature-item u-a26">
                    <input type="text" name="features[]" class="form-control" 
                        value="{{ old('features.0', 'Terintegrasi dengan data PKK') }}" 
                        placeholder="Contoh: Terintegrasi dengan data PKK" required>
                    <button type="button" class="btn u-delete-btn" data-remove-feature 
                            title="Hapus poin" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
                {{-- Fitur 2 --}}
                <div class="feature-item u-a26">
                    <input type="text" name="features[]" class="form-control" 
                        value="{{ old('features.1', 'Akses real-time 24/7') }}" 
                        placeholder="Contoh: Akses real-time 24/7" required>
                    <button type="button" class="btn u-delete-btn" data-remove-feature 
                            title="Hapus poin" disabled>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
                {{-- Fitur 3 --}}
                <div class="feature-item u-a26">
                    <input type="text" name="features[]" class="form-control" 
                        value="{{ old('features.2', 'Keamanan data terjamin') }}" 
                        placeholder="Contoh: Keamanan data terjamin" required>
                    <button type="button" class="btn u-delete-btn" data-remove-feature 
                            title="Hapus poin">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
            </div>
            
            <button type="button" id="add-feature-btn" class="btn" 
                    style="margin-top:0.5rem;width:100%;background:#f8fafc;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Poin Fitur
            </button>
            
            <small id="features-warning" style="color:#ef4444;display:none;margin-top:0.5rem;font-size:0.85rem">
                ⚠️ Maksimal 5 poin fitur
            </small>
        </div>

        {{-- Status & URL --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Status Aplikasi *</label>
                <select name="status" class="form-control" required >
                    <option value="">-- Pilih Status --</option>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif - Siap Digunakan</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Dalam Maintenance - Sedang Perbaikan</option>
                    <option value="development" {{ old('status') == 'development' ? 'selected' : '' }}>Dalam Pengembangan - Coming Soon</option>
                </select>
                <small class="u-hint">Status menentukan tampilan di landing page</small>
            </div>
            <div id="urlField">
                <label class="u-label">URL Aplikasi</label>
                <input type="url" name="url" class="form-control" value="{{ old('url') }}" placeholder="https://example.com">
                <small class="u-hint">Link untuk mengakses aplikasi</small>
            </div>
        </div>

        {{-- Icon & Sort Order --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Icon/Logo Aplikasi</label>
                <input type="file" name="icon" class="form-control" accept="image/*" id="iconInput">
                
                {{-- Preview --}}
                <div class="u-a2" id="iconPreview">
                    <img class="u-a51" id="previewImg" src="">
                    <span class="u-a17">Preview Icon</span>
                </div>
                
                <small class="u-hint">
                    Format: JPG/PNG/WebP, maks 2MB. Ukuran ideal: 200x200px
                </small>
            </div>
            <div>
                <label class="u-label">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $nextSortOrder) }}" min="1" readonly style="background:#f8fafc;cursor:not-allowed">
                <small class="u-hint">
                    Otomatis terisi (urutan berikutnya: <strong class="u-a52">{{ $nextSortOrder }}</strong>).
                    Semakin kecil angka, semakin awal tampil.
                </small>
            </div>
        </div>

        @include('admin.aplikasi.partials.color-picker')

        {{-- Is Active Checkbox - Custom Style --}}
        <div class="u-mb-8">
            <label class="u-a9">
                <input class="u-hidden" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <div class="u-a10" id="isActiveBox">
                    <svg class="u-check-svg" id="isActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span class="u-check-label">Tampilkan di Website</span>
            </label>
            <small class="u-hint-sm">
                Jika dicentang, aplikasi akan tampil di landing page. Jika tidak, aplikasi disembunyikan sementara.
            </small>
        </div>

        @php
            // Hitung berapa aplikasi yang sudah tampil di Beranda
            $berandaCount = \App\Models\Application::where('show_in_quick_access', true)
                ->where('is_active', true)
                ->where('status', 'active')
                ->count();
            $berandaFull = $berandaCount >= 2;
        @endphp

        {{-- Visibility Settings --}}
        <div class="u-mb-8">
            <label style="font-weight:600;display:block;margin-bottom:1rem;font-size:0.9rem">Tampilkan Aplikasi Di</label>
            <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(250px, 1fr));gap:0.75rem">
                
                {{-- Floating Button --}}
                <label class="vis-label u-a27">
                    <input type="checkbox" name="show_in_floating" value="1" {{ old('show_in_floating', true) ? 'checked' : '' }} class="vis-checkbox u-hidden">
                    <div class="vis-check-box u-a28">
                        <svg class="u-check-svg-fast" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span class="u-check-label-block">Floating Button</span>
                        <span class="u-text-muted-xs">Tombol mengambang di pojok kanan bawah</span>
                    </div>
                </label>

                {{-- Footer --}}
                <label class="vis-label u-a27">
                    <input type="checkbox" name="show_in_footer" value="1" {{ old('show_in_footer', true) ? 'checked' : '' }} class="vis-checkbox u-hidden">
                    <div class="vis-check-box u-a28">
                        <svg class="u-check-svg-fast" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span class="u-check-label-block">Footer</span>
                        <span class="u-text-muted-xs">Quick access di bagian bawah halaman</span>
                    </div>
                </label>

                {{-- Beranda - Dengan Validasi --}}
                <label class="vis-label" style="display:flex;align-items:center;gap:0.75rem;cursor:{{ $berandaFull ? 'not-allowed' : 'pointer' }};padding:0.75rem 1rem;background:{{ $berandaFull ? '#f1f5f9' : '#f8fafc' }};border-radius:10px;transition:all 0.2s;border:2px solid {{ $berandaFull ? '#e2e8f0' : 'transparent' }};opacity:{{ $berandaFull ? '0.6' : '1' }}" 
                    @if(!$berandaFull) @endif>
                    <input type="checkbox" name="show_in_quick_access" value="1" {{ old('show_in_quick_access', true) && !$berandaFull ? 'checked' : '' }} {{ $berandaFull ? 'disabled' : '' }} class="vis-checkbox u-hidden">
                    <div class="vis-check-box" style="width:22px;height:22px;border:2px solid {{ $berandaFull ? '#cbd5e1' : '#cbd5e1' }};border-radius:6px;background:#fff;transition:all 0.25s;display:flex;align-items:center;justify-content:center;flex-shrink:0;{{ $berandaFull ? 'opacity:0.5' : '' }}">
                        <svg class="u-check-svg-fast" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <div>
                        <span class="u-check-label-block">Beranda</span>
                        <span class="u-text-muted-xs">Quick access di halaman utama</span>
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
                    <br><span class="u-text-danger">Saat ini sudah ada {{ $berandaCount }} aplikasi yang tampil di Beranda.</span>
                @endif
            </small>
        </div>

        {{-- Action Buttons --}}
        <div class="u-form-actions">
            <x-admin.cancel-button :href="route('admin.aplikasi.index')" />
            <button type="submit" class="btn btn-primary u-inline-flex-center-gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Simpan Aplikasi
            </button>
        </div>
    </form>
</div>

    @push('scripts')
    <script src="{{ asset('assets/admin/js/aplikasi-form.js') }}"></script>
    @endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
