@extends('admin.layouts.app')
@section('title', 'Tambah Berita')
@section('page-title', 'Tambah Berita Baru')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .berita-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }

    .berita-header h1 {
        font-size: 1.25rem !important;
    }

    .berita-header .btn {
        width: 100% !important;
        justify-content: center !important;
    }

    .form-grid-2 {
        grid-template-columns: 1fr !important;
    }

    .ck-editor__editable {
        min-height: 300px !important;
    }
}
</style>

{{-- Header --}}
<div class="berita-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Tambah Berita</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Buat artikel berita baru untuk website PKK Kabupaten Toba</p>
    </div>
    <a href="{{ route('admin.berita.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark);white-space:nowrap;flex-shrink:0">← Kembali</a>
</div>

{{-- Form Card --}}
<div class="card">
    <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Judul --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Judul Berita *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required placeholder="Contoh: PKK Toba Gelar Sosialisasi Kesehatan Ibu dan Anak">
        </div>

        {{-- Kategori & Tanggal --}}
        <div class="form-grid-2" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem">
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Kategori *</label>
                <input type="text" name="category" class="form-control" value="{{ old('category') }}" required placeholder="Contoh: Kegiatan, Program, Prestasi">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Kategori untuk pengelompokan berita</small>
            </div>
            <div>
                <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Tanggal Publikasi</label>
                <input type="date" name="published_at" class="form-control" value="{{ old('published_at', date('Y-m-d')) }}">
                <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">Tanggal berita akan ditampilkan</small>
            </div>
        </div>

        {{-- Excerpt dengan Character Counter --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Ringkasan (Excerpt) *</label>

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
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Konten Lengkap *</label>
            <textarea name="content" id="contentEditor" class="form-control" rows="10">{{ old('content') }}</textarea>
            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">
                Gunakan toolbar di atas untuk memformat teks, membuat list, atau menambahkan gambar.
            </small>
        </div>

        {{-- Image Upload --}}
        <div style="margin-bottom:1.5rem">
            <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Gambar Berita</label>
            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput">

            <div id="imagePreview" style="margin-top:1rem;display:none">
                <img id="previewImg" src="" style="width:100%;max-width:400px;height:auto;border-radius:12px;object-fit:cover;background:#f8fafc">
                <span style="display:block;font-size:0.8rem;color:var(--text-muted);margin-top:0.4rem">Preview Gambar</span>
            </div>

            <small style="color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem">
                Format: JPG/PNG/WebP, maksimal 2MB. Ukuran direkomendasikan: 1200x630px
            </small>
        </div>

        {{-- Publish Checkbox - Custom Style --}}
        <div style="margin-bottom:2rem">
            <label style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;padding:0.75rem 1rem;background:#f8fafc;border-radius:10px;transition:all 0.2s;width:fit-content" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                <input type="checkbox" name="is_published" id="isPublished" value="1" {{ old('is_published') ? 'checked' : '' }} style="display:none">
                <div id="isPublishedBox" style="width:22px;height:22px;border:2px solid #cbd5e1;border-radius:6px;background:#fff;transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg id="isPublishedCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="opacity:0;transform:scale(0.5);transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1)">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <span style="font-weight:600;color:#334155;font-size:0.9rem;user-select:none">Publikasikan sekarang</span>
            </label>
            <small style="color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.85rem">
                Jika tidak dicentang, berita akan tersimpan sebagai draft
            </small>
        </div>

        {{-- Action Buttons --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.04)">
            <a href="{{ route('admin.berita.index') }}" class="btn" style="background:#f8fafc;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
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
    <div style="margin-top:1.5rem;padding:1rem;background:#fef2f2;border-radius:10px;color:#991b1b">
        <strong style="display:block;margin-bottom:0.5rem;font-weight:600">Periksa kembali input berikut:</strong>
        <ul style="padding-left:1.25rem;margin:0;font-size:0.9rem">
            @foreach($errors->all() as $err) <li style="margin-bottom:0.25rem">{{ $err }}</li> @endforeach
        </ul>
    </div>
    @endif
</div>

@push('scripts')
<!-- CKEditor 5 Classic Build dengan Alignment -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Konfigurasi Alignment
    const alignmentConfig = {
        options: [
            { name: 'left', title: 'Rata Kiri', icon: 'left', value: 'left' },
            { name: 'center', title: 'Rata Tengah', icon: 'center', value: 'center' },
            { name: 'right', title: 'Rata Kanan', icon: 'right', value: 'right' },
            { name: 'justify', title: 'Rata Kiri-Kanan', icon: 'justify', value: 'justify' }
        ]
    };

    ClassicEditor
        .create(document.querySelector('#contentEditor'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'link', '|',
                'bulletedList', 'numberedList', '|',
                'alignment',
                '|',
                'blockQuote', '|',
                'undo', 'redo'
            ],

            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            },

            alignment: alignmentConfig,
            height: 500,
            allowedContent: true
        })
        .then(editor => {
            console.log('✅ CKEditor 5 initialized with alignment!', editor);
            window.editor = editor;
        })
        .catch(error => {
            console.error('❌ CKEditor 5 error:', error);
            console.error('Stack:', error.stack);
        });
});

// Checkbox animation handler
function updateCheckboxStyle(boxId, checkId, isChecked) {
    const box = document.getElementById(boxId);
    const check = document.getElementById(checkId);

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

// Initialize checkbox state
document.addEventListener('DOMContentLoaded', function() {
    const isPublishedCheckbox = document.getElementById('isPublished');
    if (isPublishedCheckbox) {
        updateCheckboxStyle('isPublishedBox', 'isPublishedCheck', isPublishedCheckbox.checked);

        isPublishedCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('isPublishedBox', 'isPublishedCheck', this.checked);
        });
    }
});

// Character Counter untuk Excerpt
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('excerptInput');
    const counter = document.getElementById('excerptCounter');
    const maxLength = 160;

    function updateCounter() {
        const currentLength = textarea.value.length;
        counter.textContent = `${currentLength}/${maxLength}`;

        if (currentLength >= maxLength) {
            counter.style.color = '#ef4444';
        } else if (currentLength >= maxLength * 0.85) {
            counter.style.color = '#f59e0b';
        } else {
            counter.style.color = 'var(--text-muted, #6b7280)';
        }
    }

    textarea.addEventListener('input', updateCounter);
    updateCounter();
});

// Script untuk Image Preview
document.getElementById('imageInput')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran gambar terlalu besar. Maksimal 2MB.');
            e.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush

@endsection
