{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Kelola Tentang Kami')
@section('page-title', 'Tentang Kami')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-tentang-index.css') }}">


{{-- Header Section --}}
<div class="tentang-header u-header-row">
    <div class="u-flex-1-min">
        <h1 class="u-page-title-tight">Tentang Kami</h1>
        <p class="u-muted">Kelola informasi halaman tentang kami</p>
    </div>
</div>

{{-- Form Card --}}
<div class="card" style="padding:0;overflow:hidden">
    <form action="{{ route('admin.tentang.update') }}" method="POST">
        @csrf
        
        {{-- Section: Informasi Umum --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <h3 class="section-title u-a69">Informasi Umum</h3>
            </div>
            <p class="u-a70">Edit judul dan deskripsi halaman</p>
            
            <div class="u-grid-gap-6">
                <div>
                    <label class="u-label">Judul Halaman *</label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $tentang->judul) }}" required placeholder="Contoh: Tentang Kami">
                </div>
                
                <div>
                    <label class="u-label">Subjudul *</label>
                    <input type="text" name="subjudul" class="form-control" value="{{ old('subjudul', $tentang->subjudul) }}" required placeholder="Contoh: Informasi tentang PKK Kabupaten Toba">
                </div>
                
                <div>
                    <label class="u-label">Heading Utama *</label>
                    <input type="text" name="heading" class="form-control" value="{{ old('heading', $tentang->heading) }}" required placeholder="Contoh: Memberdayakan Keluarga, Mensejahterakan Masyarakat">
                </div>
                
                <div>
                    <label class="u-label">Deskripsi *</label>
                    <textarea name="deskripsi" class="form-control" rows="4" required placeholder="Deskripsi lengkap tentang organisasi PKK">{{ old('deskripsi', $tentang->deskripsi) }}</textarea>
                </div>
            </div>
        </div>
        
        {{-- Section: Daftar Program --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                <h3 class="section-title u-a69">Daftar Program</h3>
            </div>
            <p class="u-a70">Tambahkan atau edit program-program PKK</p>
            
            <div class="u-grid-gap-3" id="programsContainer">
                @foreach(old('programs', $tentang->program_list) as $index => $program)
                <div class="program-item" style="display:flex;gap:0.75rem;align-items:center">
                    <input type="text" name="programs[]" class="form-control u-flex-1" value="{{ $program }}" 
                           placeholder="Nama program" required>
                    <button type="button" data-action="remove-program" 
                            title="Hapus program" aria-label="Hapus program"
                            style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:var(--text-muted);border:none;border-radius:6px;cursor:pointer;transition:all 0.2s">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
            
            <button type="button" data-action="add-program" 
                    style="padding:0.6rem 1rem;background:transparent;color:var(--text-muted);border:1px dashed rgba(0,0,0,0.15);border-radius:8px;cursor:pointer;margin-top:1rem;display:inline-flex;align-items:center;gap:0.5rem;font-weight:500;transition:all 0.2s;width:100%;justify-content:center">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Program
            </button>
        </div>
        
        {{-- Section: Google Maps --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f0f9ff">
            <div class="u-a68">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <h3 class="section-title u-a69">Lokasi Google Maps</h3>
            </div>
            <p class="u-a70">Embed peta lokasi kantor PKK</p>
            
            <div class="u-grid-gap-6">
                <div>
                    <label class="u-label">Embed Code Google Maps *</label>
                    <textarea name="maps_embed_code" class="form-control" rows="4" required 
                              placeholder='<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;"></iframe>'>{{ old('maps_embed_code', $tentang->maps_embed_code) }}</textarea>
                    <small style="color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.85rem;display:flex;align-items:flex-start;gap:0.5rem">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px">
                            <path d="M9 18h6"/>
                            <path d="M10 22h4"/>
                            <path d="M12 2v2"/>
                            <path d="M12 6a6 6 0 0 1 6 6c0 3.31-2.69 6-6 6s-6-2.69-6-6a6 6 0 0 1 6-6z"/>
                            <path d="M12 10v4"/>
                            <path d="M10 12h4"/>
                        </svg>
                        <span>Cara mendapatkan: Buka Google Maps → Cari lokasi → Share → Embed a map → Copy HTML</span>
                    </small>
                </div>
                
                <div>
                    <label class="u-label">Link Google Maps (Opsional)</label>
                    <input type="url" name="maps_link" class="form-control" value="{{ old('maps_link', $tentang->maps_link) }}" 
                           placeholder="https://goo.gl/maps/xxx">
                </div>
                
                {{-- Preview Maps --}}
                <div class="maps-preview-container" style="margin-top:0.5rem;padding:1rem;background:#fff;border-radius:10px;border:1px solid rgba(0,0,0,0.06);overflow:hidden">
                    <label style="font-weight:600;margin-bottom:0.75rem;display:block;font-size:0.9rem">Preview Peta:</label>
                    <div style="border-radius:8px;overflow:hidden">
                        {!! $tentang->maps_embed_code !!}
                    </div>
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

    <script src="{{ asset('assets/admin/js/admin-tentang-index.js') }}"></script>


@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
