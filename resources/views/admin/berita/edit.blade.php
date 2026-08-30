{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Edit Berita')
@section('page-title', 'Edit Berita')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-berita-edit.css') }}">


{{-- Header --}}
<div class="berita-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title">Edit Berita</h1>
        <p class="u-muted">Perbarui konten berita yang sudah ada</p>
    </div>
    <x-admin.back-button :href="route('admin.berita.index')" />
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        {{-- Judul --}}
        <div class="u-mb-6">
            <label class="u-label">Judul Berita *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $berita->title) }}" required placeholder="Contoh: PKK Toba Gelar Sosialisasi Kesehatan Ibu dan Anak">
        </div>

        {{-- Kategori & Tanggal --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label">Kategori *</label>
                <input type="text" name="category" class="form-control" value="{{ old('category', $berita->category) }}" required placeholder="Contoh: Kegiatan, Program, Prestasi">
                <small class="u-hint">Kategori untuk pengelompokan berita</small>
            </div>
            <div>
                <label class="u-label">Tanggal Publikasi</label>
                <input type="date" name="published_at" class="form-control" value="{{ old('published_at', $berita->published_at ? $berita->published_at->format('Y-m-d') : '') }}">
                <small class="u-hint">Tanggal berita akan ditampilkan</small>
            </div>
        </div>

        {{-- Penulis --}}
        <div class="u-mb-6">
            <label class="u-label">Penulis *</label>
            <div style="display:flex; gap:1.5rem; margin-bottom:0.75rem;">
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.9rem;">
                    <input type="radio" name="author_type" value="account" {{ old('author_type', $berita->author_type ?? 'account') === 'account' ? 'checked' : '' }}
                           onchange="toggleAuthorInput(this.value)" style="accent-color:var(--primary)">
                    <span>Sesuai Akun ({{ auth()->user()->name }})</span>
                </label>
                <label style="display:flex; align-items:center; gap:0.5rem; cursor:pointer; font-size:0.9rem;">
                    <input type="radio" name="author_type" value="manual" {{ old('author_type', $berita->author_type ?? '') === 'manual' ? 'checked' : '' }}
                           onchange="toggleAuthorInput(this.value)" style="accent-color:var(--primary)">
                    <span>Ketik Manual</span>
                </label>
            </div>
            <input type="text" name="author" id="authorInput" class="form-control"
                   value="{{ old('author', $berita->author ?? '') }}"
                   placeholder="Ketik nama penulis..."
                   style="{{ old('author_type', $berita->author_type ?? 'account') === 'account' ? 'display:none' : '' }}">
            <small class="u-hint" id="authorHint">{{ ($berita->author_type ?? 'account') === 'account' ? 'Penulis akan otomatis diambil dari akun yang login' : 'Penulis diketik secara manual' }}</small>
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
            >{{ old('excerpt', $berita->excerpt ?? '') }}</textarea>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.4rem;">
                <small style="color:var(--text-muted); font-size:0.8rem">Maksimal 160 karakter untuk preview optimal</small>
                <span id="excerptCounter" style="font-size:0.8rem; font-weight:600; color:var(--text-muted); transition: color 0.2s;">0/160</span>
            </div>
        </div>

        {{-- Content dengan CKEditor --}}
        <div class="u-mb-6">
            <label class="u-label">Konten Lengkap *</label>
            <textarea name="content" id="contentEditor" class="form-control" rows="10" placeholder="Tulis konten berita lengkap di sini...">{{ old('content', $berita->content) }}</textarea>
            <small class="u-hint">Gunakan editor di atas untuk format teks yang lebih baik</small>
        </div>

        {{-- Image Upload (Multiple) --}}
        <div class="u-mb-6">
            <label class="u-label">Gambar Berita</label>
            
            {{-- Existing Images --}}
            @if($berita->image_path || $berita->images->count() > 0)
            <div style="margin-bottom:1rem;">
                <div style="font-size:0.85rem;font-weight:600;color:#64748b;margin-bottom:0.5rem;">Gambar Saat Ini:</div>
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap:0.75rem;">
                    @if($berita->image_path)
                    <div style="position:relative;border-radius:8px;overflow:hidden;aspect-ratio:4/3;background:#f8fafc;">
                        <img src="{{ asset('storage/' . $berita->image_path) }}" alt="Gambar utama" style="width:100%;height:100%;object-fit:cover;">
                        <span style="position:absolute;top:4px;right:4px;background:#276749;color:#fff;font-size:0.65rem;padding:2px 6px;border-radius:4px;">UTAMA</span>
                    </div>
                    @endif
                    @foreach($berita->images as $img)
                    <div style="position:relative;border-radius:8px;overflow:hidden;aspect-ratio:4/3;background:#f8fafc;">
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="{{ $img->caption ?? 'Gambar' }}" style="width:100%;height:100%;object-fit:cover;">
                        <a href="{{ route('admin.berita.delete-image', [$berita, $img->id]) }}" 
                           onclick="return confirm('Hapus gambar ini?')" 
                           style="position:absolute;top:4px;right:4px;background:#e53e3e;color:#fff;font-size:0.65rem;padding:2px 6px;border-radius:4px;text-decoration:none;">HAPUS</a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <input type="file" name="images[]" class="form-control" accept="image/*" id="imagesInput" multiple>
            <div id="imagesPreviewGrid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap:1rem; margin-top:1rem;"></div>
            
            <small class="u-hint">
                Bisa pilih beberapa gambar sekaligus (Ctrl+Klik). Format: JPG/PNG/WebP, maksimal 2MB per gambar.
            </small>
        </div>

        {{-- Publish Checkbox - Custom Style --}}
        <div class="u-mb-8">
            <label class="u-a9">
                <input type="checkbox" name="is_published" id="isPublished" value="1" {{ old('is_published', $berita->is_published) ? 'checked' : '' }} style="display:none">
                <div class="u-a10" id="isPublishedBox">
                    <svg class="u-check-svg" id="isPublishedCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span class="u-check-label">Publikasikan</span>
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
                Update Berita
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

    <script src="{{ asset('assets/admin/js/admin-berita-edit.js') }}"></script>

    <script>
    function toggleAuthorInput(type) {
        var input = document.getElementById('authorInput');
        var hint = document.getElementById('authorHint');
        if (type === 'manual') {
            input.style.display = '';
            input.required = true;
            hint.textContent = 'Masukkan nama penulis secara manual';
        } else {
            input.style.display = 'none';
            input.required = false;
            input.value = '';
            hint.textContent = 'Penulis akan otomatis diambil dari akun yang login';
        }
    }

    // Multiple image preview
    document.getElementById('imagesInput').addEventListener('change', function(e) {
        var grid = document.getElementById('imagesPreviewGrid');
        grid.innerHTML = '';
        Array.from(e.target.files).forEach(function(file, i) {
            if (!file.type.startsWith('image/')) return;
            var reader = new FileReader();
            reader.onload = function(ev) {
                var div = document.createElement('div');
                div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;aspect-ratio:4/3;background:#f8fafc;';
                div.innerHTML = '<img src="' + ev.target.result + '" style="width:100%;height:100%;object-fit:cover;">' +
                    '<span style="position:absolute;bottom:4px;left:4px;background:rgba(0,0,0,0.6);color:#fff;font-size:0.7rem;padding:2px 6px;border-radius:4px;">BARU ' + (i+1) + '</span>';
                grid.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
    </script>

@endpush

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
