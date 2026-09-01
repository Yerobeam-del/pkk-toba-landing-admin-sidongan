{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Tambah Anggota Struktur')
@section('page-title', 'Tambah Anggota')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-struktur-create.css') }}">


{{-- Header --}}
<div class="struktur-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title-tight">Tambah Anggota</h1>
        <p class="u-muted">Tambahkan data anggota baru ke dalam struktur organisasi</p>
    </div>
    <x-admin.back-button :href="route('admin.struktur.index')" />
</div>

{{-- Form Card --}}
<div class="card" style="border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1.5rem;">
    <form action="{{ route('admin.struktur.store') }}" method="POST" enctype="multipart/form-data" id="mainForm">
        @csrf
        
        {{-- Group & Position --}}
        <div class="form-grid-2 u-grid-2">
            <div>
                <label class="u-label-slate">1. Kelompok <span class="u-text-danger">*</span></label>
                <select name="group" id="groupSelect" class="form-control u-input-block" required>
                    <option value="">-- Pilih Kelompok --</option>
                    <option value="pengurus">Pengurus Inti</option>
                    <option value="pokja1">Pokja I</option>
                    <option value="pokja2">Pokja II</option>
                    <option value="pokja3">Pokja III</option>
                    <option value="pokja4">Pokja IV</option>
                </select>
                <small class="u-hint-line">Menentukan bagian struktur tempat anggota ini muncul</small>
            </div>
            <div>
                <label class="u-label-slate">2. Jabatan <span class="u-text-danger">*</span></label>
                <select name="position" id="positionSelect" class="form-control u-input-block" required>
                    <option value="">-- Pilih Kelompok Dulu --</option>
                </select>
                <small class="u-hint-line">Opsi jabatan menyesuaikan kelompok yang dipilih</small>
            </div>
        </div>

        {{-- Name --}}
        <div class="u-mb-6">
            <label class="u-label-slate">3. Nama Lengkap <span class="u-text-danger">*</span></label>
            <input type="text" name="name" class="form-control u-input-block" required placeholder="Contoh: INDAH KARUNIA PRATIWI SITUMEANG, SH">
        </div>

        {{-- Description --}}
        <div class="u-mb-6">
            <label class="u-label-slate">Deskripsi / Catatan (Opsional)</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Misal: NIP, riwayat singkat, atau catatan internal" style="width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:0.9rem;transition:border-color 0.2s;resize:vertical;min-height:80px"></textarea>
        </div>

        {{-- Photo Upload with Crop --}}
        <div class="u-mb-8">
            <label class="u-label-slate">Foto Anggota</label>
            <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*"  style="width:100%;padding:0.75rem;border:2px dashed #e2e8f0;border-radius:8px;background:#f8fafc;font-size:0.9rem;cursor:pointer;transition:all 0.2s">
            <small class="u-hint-line">JPG/PNG, maksimal 2MB. Klik foto untuk mengatur crop.</small>
            
            {{-- Preview Container --}}
            <div id="previewContainer" style="margin-top:1rem;padding:1rem;background:#f8fafc;border-radius:8px;border:1px solid rgba(0,0,0,0.06)">
                <div class="u-flex-center-gap-4">
                    <img id="photoPreview" style="width:80px;height:80px;border-radius:12px;object-fit:cover;background:#fff;cursor:pointer;display:none;box-shadow:0 2px 8px rgba(0,0,0,0.08)" data-action="open-crop">
                    <div class="u-flex-1-min">
                        <div style="font-weight:600;font-size:0.9rem;color:#334155;margin-bottom:0.25rem">Belum ada foto</div>
                        <div class="u-text-muted-sm">Klik foto untuk atur crop</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden input for cropped image --}}
        <input type="hidden" name="cropped_photo" id="croppedPhoto">

        {{-- Action Buttons --}}
        <div style="display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1.5rem;border-top:1px solid rgba(0,0,0,0.06)">
            <x-admin.cancel-button :href="route('admin.struktur.index')" />
            <button type="submit" class="btn btn-primary" id="submitBtn" style="background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;padding:0.75rem 2rem;border:none;border-radius:8px;font-weight:600;font-size:0.9rem;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:0.5rem;box-shadow:0 4px 12px rgba(20,184,166,0.3)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                Simpan Data
            </button>
        </div>
    </form>
</div>

{{-- Crop Modal --}}
<div id="cropModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.8);z-index:2000;align-items:center;justify-content:center;padding:1rem;backdrop-filter:blur(4px)">
    <div style="background:#fff;border-radius:16px;max-width:700px;width:100%;height:90vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.3);animation:modalSlideUp 0.3s ease">
        {{-- Header --}}
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);display:flex;justify-content:space-between;align-items:center;flex-shrink:0;background:#f8fafc">
            <h3 style="margin:0;font-size:1.1rem;font-weight:700;color:#1e293b">Atur Foto Profil</h3>
            <button data-action="close-crop" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--text-muted);padding:0.25rem;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:8px;transition:all 0.2s">&times;</button>
        </div>
        
        {{-- Image Container (Scrollable) --}}
        <div style="flex:1;overflow:hidden;position:relative;background:#f8fafc;padding:1.5rem;display:flex;align-items:center;justify-content:center">
            <div style="max-height:100%;overflow:auto;display:flex;align-items:center;justify-content:center;width:100%">
                <img id="cropImage" style="max-width:100%;display:block;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
            </div>
        </div>
        
        {{-- Controls (Fixed at bottom) --}}
        <div style="padding:1rem 1.5rem;background:#fff;border-top:1px solid rgba(0,0,0,0.06);flex-shrink:0">
            <div style="display:flex;gap:0.5rem;justify-content:center;margin-bottom:0.75rem;flex-wrap:wrap">
                <button type="button" data-action="rotate-crop" data-deg="-90" class="btn u-a13">↺ Putar Kiri</button>
                <button type="button" data-action="rotate-crop" data-deg="90" class="btn u-a13">Putar Kanan ↻</button>
                <button type="button" data-action="reset-crop" class="btn u-a13">Reset</button>
            </div>
            <div style="text-align:center;font-size:0.85rem;color:var(--text-muted);margin-bottom:1rem">
                Drag untuk geser, scroll untuk zoom
            </div>
            <div class="u-a61">
                <button type="button" data-action="close-crop" class="btn btn-cancel">Batal</button>
                <button type="button" data-action="apply-crop" class="btn btn-primary" style="background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;white-space:nowrap;padding:0.75rem 1.5rem;border-radius:8px;border:none;cursor:pointer;font-weight:600;font-size:0.9rem;transition:all 0.2s;box-shadow:0 4px 12px rgba(20,184,166,0.3)">Terapkan Crop</button>
            </div>
        </div>
    </div>
</div>

    <script src="{{ asset('assets/admin/js/admin-struktur-create.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
