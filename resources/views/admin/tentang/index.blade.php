@extends('admin.layouts.app')
@section('title', 'Kelola Tentang Kami')
@section('page-title', 'Tentang Kami')

@section('content')
<style>
/* Responsive untuk Mobile */
@media (max-width: 768px) {
    .tentang-header {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 1rem !important;
    }
    
    .tentang-header h1 {
        font-size: 1.25rem !important;
    }
    
    .section-title {
        font-size: 1rem !important;
    }
    
    .program-item {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .program-item button {
        width: 100% !important;
        margin-top: 0.5rem !important;
    }
    
    .maps-preview-container {
        overflow: hidden !important;
    }
    
    .maps-preview-container iframe {
        width: 100% !important;
        height: 300px !important;
    }
    
    .action-buttons {
        flex-direction: column-reverse !important;
    }
    
    .action-buttons .btn {
        width: 100% !important;
        justify-content: center !important;
    }
}
</style>

{{-- Header Section --}}
<div class="tentang-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem">
    <div style="flex:1;min-width:0">
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Tentang Kami</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola informasi halaman tentang kami</p>
    </div>
</div>

{{-- Form Card --}}
<div class="card" style="padding:0;overflow:hidden">
    <form action="{{ route('admin.tentang.update') }}" method="POST">
        @csrf
        
        {{-- Section: Informasi Umum --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                <h3 class="section-title" style="font-size:1.1rem;font-weight:700;color:var(--primary);margin:0">Informasi Umum</h3>
            </div>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0 0 1rem 0">Edit judul dan deskripsi halaman</p>
            
            <div style="display:grid;gap:1.5rem">
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Judul Halaman *</label>
                    <input type="text" name="judul" class="form-control" value="{{ old('judul', $tentang->judul) }}" required placeholder="Contoh: Tentang Kami">
                </div>
                
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Subjudul *</label>
                    <input type="text" name="subjudul" class="form-control" value="{{ old('subjudul', $tentang->subjudul) }}" required placeholder="Contoh: Informasi tentang PKK Kabupaten Toba">
                </div>
                
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Heading Utama *</label>
                    <input type="text" name="heading" class="form-control" value="{{ old('heading', $tentang->heading) }}" required placeholder="Contoh: Memberdayakan Keluarga, Mensejahterakan Masyarakat">
                </div>
                
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Deskripsi *</label>
                    <textarea name="deskripsi" class="form-control" rows="4" required placeholder="Deskripsi lengkap tentang organisasi PKK">{{ old('deskripsi', $tentang->deskripsi) }}</textarea>
                </div>
            </div>
        </div>
        
        {{-- Section: Daftar Program --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f8fafc">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                <h3 class="section-title" style="font-size:1.1rem;font-weight:700;color:var(--primary);margin:0">Daftar Program</h3>
            </div>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0 0 1rem 0">Tambahkan atau edit program-program PKK</p>
            
            <div id="programsContainer" style="display:grid;gap:0.75rem">
                @foreach(old('programs', $tentang->program_list) as $index => $program)
                <div class="program-item" style="display:flex;gap:0.75rem;align-items:center">
                    <input type="text" name="programs[]" class="form-control" value="{{ $program }}" 
                           placeholder="Nama program" required style="flex:1">
                    <button type="button" onclick="removeProgram(this)" 
                            title="Hapus program"
                            style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border:none;border-radius:6px;cursor:pointer;transition:all 0.2s"
                            onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444';this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='transparent';this.style.color='#94a3b8';this.style.transform='translateY(0)'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
                @endforeach
            </div>
            
            <button type="button" onclick="addProgram()" 
                    style="padding:0.6rem 1rem;background:transparent;color:var(--text-muted);border:1px dashed rgba(0,0,0,0.15);border-radius:8px;cursor:pointer;margin-top:1rem;display:inline-flex;align-items:center;gap:0.5rem;font-weight:500;transition:all 0.2s;width:100%;justify-content:center"
                    onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)';this.style.background='rgba(20,184,166,0.05)'"
                    onmouseout="this.style.borderColor='rgba(0,0,0,0.15)';this.style.color='var(--text-muted)';this.style.background='transparent'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Program
            </button>
        </div>
        
        {{-- Section: Google Maps --}}
        <div style="padding:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);background:#f0f9ff">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <h3 class="section-title" style="font-size:1.1rem;font-weight:700;color:var(--primary);margin:0">Lokasi Google Maps</h3>
            </div>
            <p style="color:var(--text-muted);font-size:0.9rem;margin:0 0 1rem 0">Embed peta lokasi kantor PKK</p>
            
            <div style="display:grid;gap:1.5rem">
                <div>
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Embed Code Google Maps *</label>
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
                    <label style="font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem">Link Google Maps (Opsional)</label>
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
            <a href="{{ route('admin.dashboard') }}" class="btn" style="background:#fff;color:var(--text-dark)">Batal</a>
            <button type="submit" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
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

<script>
function addProgram() {
    const container = document.getElementById('programsContainer');
    const div = document.createElement('div');
    div.className = 'program-item';
    div.style.cssText = 'display:flex;gap:0.75rem;align-items:center';
    div.innerHTML = `
        <input type="text" name="programs[]" class="form-control" placeholder="Nama program" required style="flex:1">
        <button type="button" onclick="removeProgram(this)" 
                title="Hapus program"
                style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border:none;border-radius:6px;cursor:pointer;transition:all 0.2s"
                onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.background='transparent';this.style.color='#94a3b8';this.style.transform='translateY(0)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </button>
    `;
    container.appendChild(div);
    
    // Focus ke input baru
    const newInput = div.querySelector('input');
    if (newInput) newInput.focus();
}

function removeProgram(btn) {
    const container = document.getElementById('programsContainer');
    const items = container.querySelectorAll('.program-item');
    
    if (items.length <= 1) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Minimal harus ada 1 program');
        } else {
            alert('Minimal harus ada 1 program');
        }
        return;
    }
    
    btn.parentElement.remove();
}
</script>

@endsection