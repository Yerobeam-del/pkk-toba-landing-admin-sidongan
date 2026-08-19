{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Tambah Berita')
@section('page-title', 'Tambah Berita Baru')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-berita-create.css') }}">


{{-- Header --}}
<div class="berita-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title">Tambah Berita</h1>
        <p class="u-muted">Buat artikel berita baru untuk website PKK Kabupaten Toba</p>
    </div>
    <x-admin.back-button :href="route('admin.berita.index')" />
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Judul --}}
        <div class="u-mb-6">
            <label class="u-label">Judul Berita *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Contoh: PKK Toba Gelar Sosialisasi Kesehatan Ibu dan Anak">
        </div>

        {{-- Kategori & Tanggal --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Kategori *</label>
                <input type="text" name="category" class="form-control" value="{{ old('category') }}" required placeholder="Contoh: Kegiatan, Program, Prestasi">
                <small class="u-hint">Kategori untuk pengelompokan berita</small>
            </div>
            <div>
                <label class="u-label">Tanggal Publikasi</label>
                <input type="date" name="published_at" class="form-control" value="{{ old('published_at', date('Y-m-d')) }}">
                <small class="u-hint">Tanggal berita akan ditampilkan</small>
            </div>
        </div>

        {{-- Excerpt dengan Character Counter --}}
        <div class="u-mb-6">
            <label class="u-label">Ringkasan (Excerpt) *</label>

            <textarea
                name="excerpt"
                id="excerptInput"
                class="form-control"
                rows="3"
                maxlength="160"
                required
                placeholder="Ringkasan singkat yang akan muncul di listing berita"
            >{{ old('excerpt') }}</textarea>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.4rem;">
                <small style="color:var(--text-muted); font-size:0.8rem">Maksimal 160 karakter untuk preview optimal</small>
                <span id="excerptCounter" style="font-size:0.8rem; font-weight:600; color:var(--text-muted); transition: color 0.2s;">0/160</span>
            </div>
        </div>

        {{-- CKEditor Content --}}
        <div class="u-mb-6">
            <label class="u-label">Konten Lengkap *</label>
            <textarea name="content" id="contentEditor" class="form-control" rows="10">{{ old('content') }}</textarea>
            <small class="u-hint">
                Gunakan toolbar di atas untuk memformat teks, membuat list, atau menambahkan gambar.
            </small>
        </div>

        {{-- Image Upload --}}
        <div class="u-mb-6">
            <label class="u-label">Gambar Berita</label>
            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">

            <div class="u-a2" id="imagePreview">
                <img id="previewImg" src="" style="width:100%;max-width:400px;height:auto;border-radius:12px;object-fit:cover;background:#f8fafc">
                <span class="u-a17">Preview Gambar</span>
            </div>

            <small class="u-hint">
                Format: JPG/PNG/WebP, maksimal 2MB. Ukuran direkomendasikan: 1200x630px
            </small>
        </div>

        {{-- Publish Checkbox - Custom Style --}}
        <div class="u-mb-8">
            <label class="u-a9">
                <input class="u-hidden" type="checkbox" name="is_published" id="isPublished" value="1" {{ old('is_published') ? 'checked' : '' }}>
                <div class="u-a10" id="isPublishedBox">
                    <svg class="u-check-svg" id="isPublishedCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span class="u-check-label">Publikasikan sekarang</span>
            </label>
            <small class="u-hint-sm">
                Jika tidak dicentang, berita akan tersimpan sebagai draft
            </small>
        </div>

        {{-- Action Buttons --}}
        <div class="u-form-actions">
            <x-admin.cancel-button :href="route('admin.berita.index')" />
            <button type="submit" class="btn btn-primary u-inline-flex-center-gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Simpan Berita
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
<!-- CKEditor 5 Classic Build dengan Alignment -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

    <script src="{{ asset('assets/admin/js/admin-berita-create.js') }}"></script>

@endpush

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
