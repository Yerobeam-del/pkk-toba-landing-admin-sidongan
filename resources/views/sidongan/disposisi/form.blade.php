@extends('sidongan.layouts.app')
@section('title', 'Form Disposisi - SIDONGAN')

@section('content')
@php
    $backUrl = session('disposisi_form_back_url', route('sidongan.documents.show', $document));
    
    // Validasi ketat
    if (str_contains($backUrl, '/disposisi/form') || 
        str_contains($backUrl, '/disposisi-print')) {
        $backUrl = route('sidongan.documents.show', $document);
    }
@endphp

<style>
    /* Container */
    .disposisi-container {
        padding: 0 1.5rem;
    }
    
    /* Header */
    .disposisi-header {
        background: linear-gradient(135deg, #0891b2, #14b8a6);
        padding: 1.5rem 2rem;
        border-radius: 1rem;
        margin-bottom: 1.5rem;
        color: white;
        box-shadow: 0 4px 20px rgba(8, 145, 178, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .disposisi-header-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .disposisi-header-icon {
        width: 3rem;
        height: 3rem;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .disposisi-header-icon i {
        font-size: 1.5rem;
        color: white;
    }
    
    .disposisi-header h1 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
    }
    
    .disposisi-header p {
        font-size: 0.875rem;
        opacity: 0.95;
        margin: 0;
    }
    
    .disposisi-header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: rgba(255,255,255,0.25);
        color: white;
        text-decoration: none;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.25s ease;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .btn-back:hover {
        background: rgba(255,255,255,0.35);
        transform: translateY(-2px);
    }
    
    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1.3fr;
        gap: 1.5rem;
        align-items: start;
    }
    
    /* Card Box */
    .card-box {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }
    
    .card-box:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    
    /* Role Option */
    .role-option {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .role-option:hover {
        border-color: #cbd5e1;
        background: white;
    }
    
    .role-option input[type="checkbox"] {
        display: none;
    }
    
    .custom-box {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #94a3b8;
        border-radius: 0.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    
    .custom-box svg {
        width: 12px;
        height: 12px;
        color: white;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s;
    }
    
    .role-option input[type="checkbox"]:checked + .custom-box {
        background-color: #0891b2;
        border-color: #0891b2;
    }
    
    .role-option input[type="checkbox"]:checked + .custom-box svg {
        opacity: 1;
        transform: scale(1);
    }
    
    .role-option.active {
        background-color: #f0f9ff;
        border-color: #0891b2;
    }
    
    .role-option.active .role-text {
        color: #0c4a6e;
        font-weight: 600;
    }
    
    /* Custom Select */
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }
    
    .custom-select-wrapper select {
        width: 100%;
        padding: 0.75rem 3rem 0.75rem 1rem;
        appearance: none;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background: white;
        transition: all 0.2s;
    }
    
    .custom-select-wrapper select:focus {
        border-color: #0891b2;
        box-shadow: 0 0 0 3px rgba(8,145,178,0.1);
        outline: none;
    }
    
    .arrow-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #64748b;
    }
    
    /* Animations */
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-slide-in {
        animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    
    /* Responsive */
    @media (max-width: 968px) {
        .disposisi-container {
            padding: 0 1rem;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .disposisi-header {
            padding: 1.25rem 1.5rem;
            flex-direction: column;
            align-items: stretch;
        }
        
        .disposisi-header-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .btn-back {
            width: 100%;
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .disposisi-container {
            padding: 0 0.75rem;
        }
        
        .disposisi-header h1 {
            font-size: 1.125rem;
        }
        
        .disposisi-header p {
            font-size: 0.8rem;
        }
        
        .role-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="disposisi-container">
    {{-- HEADER BAR --}}
    <div class="disposisi-header animate-slide-in">
        <div class="disposisi-header-title">
            <div class="disposisi-header-icon">
                <i class="fas fa-share-square"></i>
            </div>
            <div>
                <h1>Form Disposisi</h1>
                <p>Tentukan tujuan disposisi dan instruksi untuk surat ini</p>
            </div>
        </div>
        <div class="disposisi-header-actions">
            <a href="{{ $backUrl }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- CONTENT GRID --}}
    <div class="content-grid">
        
        {{-- KOLOM KIRI: Info Surat --}}
        <div class="card-box animate-slide-in">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 2px solid #bae6fd; background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #0c4a6e; margin: 0 0 0.5rem 0; line-height: 1.4;">
                    {{ $document->subject ?? $document->title }}
                </h3>
                <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                    <span style="font-size: 0.8rem; font-family: monospace; background: #0891b2; color: white; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-weight: 700;">
                        {{ $document->agenda_number }}
                    </span>
                    <span style="font-size: 0.8rem; color: #64748b;">{{ $document->document_number }}</span>
                </div>
            </div>

            <div style="padding: 1.5rem;">
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Pengirim</span>
                        <span style="font-size: 0.9rem; font-weight: 500; color: #0f172a; text-align: right; max-width: 65%;">{{ $document->sender }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Tanggal Surat</span>
                        <span style="font-size: 0.9rem; font-weight: 500; color: #0f172a;">{{ $document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d M Y') : '-' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Dibuat oleh</span>
                        <span style="font-size: 0.9rem; font-weight: 500; color: #0f172a;">{{ $document->creator->name ?? 'Sekretaris PKK' }}</span>
                    </div>
                </div>

                <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 0.5rem; padding: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-lightbulb" style="color: #d97706; font-size: 0.9rem;"></i>
                        <span style="font-size: 0.8rem; font-weight: 700; color: #92400e;">Saran Sekretaris</span>
                    </div>
                    <p style="font-size: 0.875rem; color: #78350f; margin: 0; line-height: 1.6; font-style: italic;">
                        "{{ $document->suggestion ?? 'Tidak ada saran yang diberikan.' }}"
                    </p>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Form Disposisi --}}
        <form action="{{ route('sidongan.disposisi.store', $document) }}" method="POST" class="card-box animate-slide-in" style="padding: 0;">
            @csrf
            
            {{-- ERROR MESSAGE --}}
            @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; margin: 1.5rem 1.5rem 0 1.5rem; border-radius: 0.5rem;">
                <strong style="display: block; margin-bottom: 0.5rem;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> Gagal Menyimpan Disposisi:
                </strong>
                <ul style="margin: 0; padding-left: 1.25rem; list-style-type: disc; font-size: 0.875rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div style="padding: 1.5rem;">
                {{-- Target Roles --}}
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                        <i class="fas fa-users" style="color: #0891b2; margin-right: 0.5rem;"></i>
                        Disposisikan ke <span style="color: #ef4444;">*</span>
                    </label>
                    <div class="role-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
                        @php
                            $roles = [
                                'sekretaris' => 'Sekretaris PKK',
                                'bendahara' => 'Bendahara PKK',
                                'staf_ahli_1' => 'Staf Ahli I',
                                'staf_ahli_2' => 'Staf Ahli II',
                                'pengurus_1' => 'Ketua Pengurus I',
                                'pengurus_2' => 'Ketua Pengurus II',
                                'pengurus_3' => 'Ketua Pengurus III',
                                'pengurus_4' => 'Ketua Pengurus IV',
                            ];
                        @endphp
                        @foreach($roles as $value => $label)
                        <label class="role-option">
                            <input type="checkbox" name="target_roles[]" value="{{ $value }}" onchange="toggleRoleStyle(this)">
                            <div class="custom-box">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <span class="role-text" style="font-size: 0.875rem; color: #475569;">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div style="height: 1px; background: #e2e8f0; margin-bottom: 1.5rem;"></div>

                {{-- Action Select --}}
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                        <i class="fas fa-tasks" style="color: #0891b2; margin-right: 0.5rem;"></i>
                        Tindakan/Instruksi <span style="color: #ef4444;">*</span>
                    </label>
                    <div class="custom-select-wrapper">
                        <select name="action" id="action" required onchange="toggleCustomAction()">
                            <option value="" disabled {{ old('action') ? '' : 'selected' }}>Pilih Tindakan</option>
                            <option value="Untuk diketahui" {{ old('action') == 'Untuk diketahui' ? 'selected' : '' }}>Untuk diketahui</option>
                            <option value="Untuk dilaksanakan" {{ old('action') == 'Untuk dilaksanakan' ? 'selected' : '' }}>Untuk dilaksanakan</option>
                            <option value="Untuk diproses lebih lanjut" {{ old('action') == 'Untuk diproses lebih lanjut' ? 'selected' : '' }}>Untuk diproses lebih lanjut</option>
                            <option value="Untuk diarsipkan" {{ old('action') == 'Untuk diarsipkan' ? 'selected' : '' }}>Untuk diarsipkan</option>
                            <option value="Untuk dikoordinasikan" {{ old('action') == 'Untuk dikoordinasikan' ? 'selected' : '' }}>Untuk dikoordinasikan</option>
                            <option value="Lainnya" {{ old('action') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <div class="arrow-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </div>
                    </div>
                    
                    {{-- Custom Action Input --}}
                    <div id="customActionContainer" style="display: {{ old('action') == 'Lainnya' ? 'block' : 'none' }}; margin-top: 0.75rem; animation: slideDown 0.3s ease;">
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #64748b; margin-bottom: 0.375rem;">
                            <i class="fas fa-edit" style="margin-right: 0.35rem;"></i> Masukkan Tindakan/Instruksi Lainnya:
                        </label>
                        <input type="text" name="custom_action" id="customActionInput" 
                            value="{{ old('custom_action') }}"
                            placeholder="Ketik tindakan/instruksi lainnya..."
                            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f97316; border-radius: 0.5rem; font-size: 0.875rem; background: #fff7ed; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#ea580c'; this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                            onblur="this.style.borderColor='#f97316'; this.style.boxShadow='none'">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                            <i class="fas fa-info-circle"></i> Teks ini akan digunakan sebagai tindakan disposisi
                        </p>
                    </div>
                </div>

                {{-- Comment --}}
                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;">
                        <i class="fas fa-comment-alt" style="color: #0891b2; margin-right: 0.5rem;"></i>
                        Komentar Tambahan
                    </label>
                    <textarea name="comment" id="comment" rows="4" placeholder="Catatan tambahan..."
                              style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; font-family: inherit; resize: vertical; transition: all 0.2s;"
                              onfocus="this.style.borderColor='#0891b2'; this.style.boxShadow='0 0 0 3px rgba(8,145,178,0.1)'" 
                              onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"></textarea>
                </div>
            </div>

            <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ $backUrl }}" 
                style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: white; border: 1px solid #e2e8f0; color: #475569; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; transition: all 0.2s;"
                onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                    Batal
                </a>
                <button type="submit" 
                        style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0891b2, #14b8a6); color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; box-shadow: 0 2px 8px rgba(8,145,178,0.2); transition: all 0.2s;"
                        onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(8,145,178,0.3)'" 
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(8,145,178,0.2)'">
                    <i class="fas fa-paper-plane"></i> Kirim Disposisi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRoleStyle(checkbox) {
    const label = checkbox.closest('.role-option');
    if (checkbox.checked) label.classList.add('active');
    else label.classList.remove('active');
}

function toggleCustomAction() {
    const actionSelect = document.getElementById('action');
    const customContainer = document.getElementById('customActionContainer');
    const customInput = document.getElementById('customActionInput');
    
    if (actionSelect.value === 'Lainnya') {
        customContainer.style.display = 'block';
        customInput.required = true;
        setTimeout(() => customInput.focus(), 100);
    } else {
        customContainer.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    if (document.querySelectorAll('input[name="target_roles[]"]:checked').length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu tujuan disposisi!');
        return false;
    }
    
    const actionSelect = document.getElementById('action');
    const customInput = document.getElementById('customActionInput');
    
    if (actionSelect.value === 'Lainnya' && !customInput.value.trim()) {
        e.preventDefault();
        alert('Silakan masukkan tindakan/instruksi lainnya!');
        customInput.focus();
        return false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    toggleCustomAction();
});
</script>
@endsection