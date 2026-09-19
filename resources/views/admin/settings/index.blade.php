{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Pengaturan Situs')
@section('page-title', 'Pengaturan Situs')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-tentang-index.css') }}">


{{-- Header Section --}}
<div class="tentang-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title-tight">Pengaturan Situs</h1>
        <p class="u-muted">Kelola konten footer beranda: judul, alamat, dan sosial media</p>
    </div>
</div>

{{-- Form Card --}}
<div class="card" style="padding:0;overflow:hidden">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Section: Identitas Footer --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <h3 class="section-title u-a69">Identitas Footer</h3>
            </div>
            <p class="u-a70">Judul besar di atas footer dan teks copyright di bagian bawah</p>

            <div class="u-grid-gap-6">
                <div>
                    <label class="u-label">Judul Footer *</label>
                    <input type="text" name="footer_title" class="form-control" value="{{ old('footer_title', $settings['footer_title']) }}" required placeholder="Contoh: PKK Kabupaten Toba">
                </div>

                <div>
                    <label class="u-label">Teks Copyright *</label>
                    <input type="text" name="footer_copyright" class="form-control" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" required placeholder="Contoh: TP-PKK Kabupaten Toba">
                    <small class="u-muted" style="display:block;margin-top:0.5rem;font-size:0.85rem">Tahun otomatis ditambahkan di depan teks ini (contoh: &copy; 2026 TP-PKK Kabupaten Toba. All rights reserved.)</small>
                </div>
            </div>
        </div>

        {{-- Section: Alamat --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <h3 class="section-title u-a69">Alamat Kantor</h3>
            </div>
            <p class="u-a70">Ditampilkan di footer beranda dan di halaman Tentang Kami (lokasi kantor)</p>

            <div>
                <label class="u-label">Alamat Lengkap *</label>
                <textarea name="footer_address" class="form-control" rows="3" required placeholder="Jl. D. I. Panjaitan, No. 1, Balige,&#10;Kabupaten Toba,&#10;Sumatera Utara 22311">{{ old('footer_address', $settings['footer_address']) }}</textarea>
                <small class="u-muted" style="display:block;margin-top:0.5rem;font-size:0.85rem">Setiap baris baru akan ditampilkan sebagai baris terpisah di footer</small>
            </div>
        </div>

        {{-- Section: Logo Footer --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f0f9ff">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                <h3 class="section-title u-a69">Logo Footer</h3>
            </div>
            <p class="u-a70">Kosongkan jika ingin memakai logo bawaan. Format: jpg, png, webp (maks 2MB)</p>

            <div class="u-grid-gap-6">
                @foreach([
                    'toba' => ['label' => 'Logo Kabupaten Toba', 'current' => $settings['footer_logo_toba'], 'fallback' => 'assets/landing/images/Logo-Kabupaten-Toba-Transparent.png'],
                    'pkk' => ['label' => 'Logo PKK', 'current' => $settings['footer_logo_pkk'], 'fallback' => 'assets/landing/images/Logo-PKK-Transparent.png'],
                ] as $slot => $logo)
                <div>
                    <label class="u-label">{{ $logo['label'] }}</label>
                    <div style="display:flex;gap:1rem;align-items:center;flex-wrap:wrap">
                        <img src="{{ $logo['current'] ? asset('storage/' . $logo['current']) : asset($logo['fallback']) }}"
                             alt="{{ $logo['label'] }}"
                             style="width:72px;height:72px;object-fit:contain;background:#fff;border:1px solid rgba(0,0,0,0.08);border-radius:10px;padding:6px">
                        <div style="flex:1;min-width:220px">
                            <input type="file" name="footer_logo_{{ $slot }}" accept=".jpg,.jpeg,.png,.webp" class="form-control" style="padding:0.4rem">
                            @if($logo['current'])
                            <label style="display:inline-flex;align-items:center;gap:0.4rem;margin-top:0.5rem;font-size:0.85rem;color:var(--text-muted);cursor:pointer">
                                <input type="checkbox" name="remove_logo_{{ $slot }}" value="1">
                                Hapus logo custom (kembali ke logo bawaan)
                            </label>
                            @endif
                        </div>
                    </div>
                    @error('footer_logo_' . $slot)
                        <small style="color:#dc2626;display:block;margin-top:0.4rem;font-size:0.85rem">{{ $message }}</small>
                    @enderror
                </div>
                @endforeach
            </div>
        </div>

        {{-- Section: Sosial Media --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f0f9ff">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                    <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                </svg>
                <h3 class="section-title u-a69">Instagram</h3>
            </div>
            <p class="u-a70">Tautan "Ikuti Kami" di footer beranda</p>

            <div class="u-grid-gap-6">
                <div>
                    <label class="u-label">URL Instagram *</label>
                    <input type="url" name="instagram_url" class="form-control" value="{{ old('instagram_url', $settings['instagram_url']) }}" required placeholder="https://www.instagram.com/tppkktoba_/">
                </div>

                <div>
                    <label class="u-label">Handle yang Ditampilkan *</label>
                    <input type="text" name="instagram_handle" class="form-control" value="{{ old('instagram_handle', $settings['instagram_handle']) }}" required placeholder="@tppkktoba_">
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons" style="padding:1.5rem;display:flex;gap:0.75rem;justify-content:flex-end;background:#f8fafc">
            <x-admin.cancel-button :href="route('admin.dashboard')" />
            <button type="submit" class="btn btn-primary u-inline-flex-center-gap-2">
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

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
