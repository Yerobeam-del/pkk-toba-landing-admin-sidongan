{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Edit Desa')
@section('page-title', 'Edit Desa')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-desa-edit.css') }}">


{{-- Header --}}
<div class="desa-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title">Edit Desa</h1>
        <p class="u-muted">Perbarui data desa/kelurahan yang sudah ada</p>
    </div>
    <x-admin.back-button :href="route('admin.desa.index')" />
</div>

{{-- Form Card --}}
<div class="card">
    <form id="desaForm" data-current-kec="{{ old('kecamatan_id', $desa->kecamatan_id) }}" data-current-desa="{{ old('desa_code', $desa->kode_wilayah) }}" action="{{ route('admin.desa.update', $desa) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        
        {{-- Kecamatan Select (dengan Icon Pin) --}}
        <div class="u-mb-6">
            <label class="u-label">Kecamatan *</label>
            <div class="u-relative">
                <svg class="u-a33" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                    >
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                
                <select name="kecamatan_id" id="kecamatanSelect" class="form-control" required 
                        style="width:100%;padding:0.75rem 2.5rem 0.75rem 3rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.85rem center;background-size:18px;cursor:pointer;line-height:1.5">
                    <option value="">Memuat data kecamatan...</option>
                </select>
            </div>
            <small class="u-hint">Data kecamatan dari database</small>
        </div>

        {{-- Desa Select (dengan Icon Rumah) --}}
        <div class="u-mb-6">
            <label class="u-label">Desa / Kelurahan *</label>
            <div class="u-relative">
                <svg class="u-a33" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                    >
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                
                <select name="desa_code" id="desaSelect" class="form-control" required disabled
                        style="width:100%;padding:0.75rem 2.5rem 0.75rem 3rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;background:#fff;font-family:inherit;font-size:0.9rem;appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E&quot;);background-repeat:no-repeat;background-position:right 0.85rem center;background-size:18px;cursor:not-allowed;opacity:0.6;line-height:1.5">
                    <option value="">Pilih Kecamatan Terlebih Dahulu</option>
                </select>
            </div>
            <input type="hidden" name="desa_name" id="desaNameInput" value="{{ old('desa_name', $desa->name) }}">
            <small class="u-hint" id="desaHelp">Data desa otomatis dari API wilayah.id</small>
            <small id="desaError" style="color:#ef4444;display:none;margin-top:0.25rem;font-size:0.85rem"></small>
        </div>

        {{-- Population & Households: otomatis dari database SIEDA (tidak diinput manual) --}}
        <div class="u-mb-6">
            <div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.9rem 1.1rem;border-radius:10px;background:rgba(20,184,166,0.06);border:1px solid rgba(20,184,166,0.18)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0f6b63" stroke-width="2" style="flex-shrink:0;margin-top:0.1rem">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
                <div style="font-size:0.85rem;color:var(--text-muted);line-height:1.6">
                    Jumlah <strong style="color:var(--text-dark)">Penduduk</strong> dan <strong style="color:var(--text-dark)">KK</strong> tidak dapat diubah dari sini —
                    angka normor otomatis diambil dari <strong style="color:#0f6b63">database SIEDA</strong> berdasarkan kode desa.
                    Yang dapat diatur hanyalah <strong style="color:var(--text-dark)">foto desa</strong> dan pengaturan tampilan di bawah.
                </div>
            </div>
        </div>

        {{-- Image & Sort Order --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Foto Desa *</label>
                <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">
                
                @if($desa->image)
                <div class="u-a53" id="existingImage">
                    <img src="{{ asset('storage/'.$desa->image) }}" style="width:80px;height:60px;border-radius:8px;object-fit:cover;background:#f8fafc">
                    <div>
                        <div style="font-weight:600;font-size:0.9rem">Foto saat ini</div>
                        <div class="u-text-muted-sm">Upload baru untuk mengganti</div>
                    </div>
                </div>
                @endif
                
                <div class="u-a2" id="newImagePreview">
                    <img id="previewImg" src="" style="width:100%;max-width:200px;height:auto;border-radius:12px;object-fit:cover;background:#f8fafc">
                    <span class="u-a17">Preview Foto Baru</span>
                </div>
                
                <small class="u-hint">
                    Format: JPG/PNG/WebP, maksimal 2MB • Gambar ini yang tampil pada kartu desa di website
                </small>
            </div>
            <div>
                <label class="u-label">Urutan Tampil</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $desa->sort_order) }}" min="0">
                <small class="u-hint">Semakin kecil angka, semakin awal tampil</small>
            </div>
        </div>

        {{-- Is Active Checkbox - Custom Style --}}
        <div class="u-mb-8">
            <label class="u-a9">
                <input class="u-hidden" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', $desa->is_active) ? 'checked' : '' }}>
                <div class="u-a10" id="isActiveBox">
                    <svg class="u-check-svg" id="isActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span class="u-check-label">Tampilkan di Website</span>
            </label>
            <small class="u-hint-sm">
                Jika dicentang, desa akan tampil di website. Jika tidak, desa disembunyikan sementara.
            </small>
        </div>

        {{-- Action Buttons --}}
        <div class="u-form-actions">
            <x-admin.cancel-button :href="route('admin.desa.index')" />
            <button type="submit" class="btn btn-primary u-inline-flex-center-gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Update Desa
            </button>
        </div>
    </form>
    
    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="u-alert-danger-box">
        <strong class="u-label-simple">Periksa kembali input berikut:</strong>
        <ul class="u-list-indent">
            @foreach($errors->all() as $err) <li class="u-mb-1">{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif
</div>

@push('scripts')
    <script src="{{ asset('assets/admin/js/desa-form.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
