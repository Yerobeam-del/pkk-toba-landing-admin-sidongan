{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
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

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-disposisi-form.css') }}">


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
        <div class="disposisi-header-actions sd-header-actions">
            <a href="{{ $backUrl }}" class="btn-back sd-btn-back">
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

            <div class="u-p-6">
                <div class="u-mb-6">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                        <span class="u-a82">Pengirim</span>
                        <span style="font-size: 0.9rem; font-weight: 500; color: #0f172a; text-align: right; max-width: 65%;">{{ $document->sender }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                        <span class="u-a82">Tanggal Surat</span>
                        <span style="font-size: 0.9rem; font-weight: 500; color: #0f172a;">{{ $document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="u-a83">
                        <span class="u-a82">Dibuat oleh</span>
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
        <form action="{{ route('sidongan.disposisi.store', $document) }}" method="POST" class="card-box animate-slide-in u-p-0">
            @csrf
            
            {{-- ERROR MESSAGE --}}
            @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; margin: 1.5rem 1.5rem 0 1.5rem; border-radius: 0.5rem;">
                <strong style="display: block; margin-bottom: 0.5rem;">
                    <i class="fas fa-exclamation-circle u-mr-2"></i> Gagal Menyimpan Disposisi:
                </strong>
                <ul style="margin: 0; padding-left: 1.25rem; list-style-type: disc; font-size: 0.875rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="u-p-6">
                {{-- Target Roles --}}
                <div class="u-mb-6">
                    <label class="u-section-title-dark">
                        <i class="fas fa-users u-a84"></i>
                        Disposisikan ke <span class="u-text-danger">*</span>
                    </label>
                    <div class="role-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.75rem;">
                        @php
                            $roles = $roles ?? [
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
                            <input type="checkbox" name="target_roles[]" value="{{ $value }}" >
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
                <div class="u-mb-6">
                    <label class="u-section-title-dark">
                        <i class="fas fa-tasks u-a84"></i>
                        Tindakan/Instruksi <span class="u-text-danger">*</span>
                    </label>
                    <div class="custom-select-wrapper">
                        <select name="action" id="action" required >
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
                            <i class="fas fa-edit u-mr-1"></i> Masukkan Tindakan/Instruksi Lainnya:
                        </label>
                        <input type="text" name="custom_action" id="customActionInput" 
                            value="{{ old('custom_action') }}"
                            placeholder="Ketik tindakan/instruksi lainnya..."
                            style="width: 100%; padding: 0.75rem 1rem; border: 2px solid #f97316; border-radius: 0.5rem; font-size: 0.875rem; background: #fff7ed; transition: all 0.2s;">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                            <i class="fas fa-info-circle"></i> Teks ini akan digunakan sebagai tindakan disposisi
                        </p>
                    </div>
                </div>

                {{-- Comment --}}
                <div class="u-mb-5">
                    <label class="u-section-title-dark">
                        <i class="fas fa-comment-alt u-a84"></i>
                        Komentar Tambahan
                    </label>
                    <textarea name="comment" id="comment" rows="4" placeholder="Catatan tambahan..."
                              style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; font-family: inherit; resize: vertical; transition: all 0.2s;"></textarea>
                </div>
            </div>

            <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ $backUrl }}" 
                class="sd-btn-cancel">
                    Batal
                </a>
                <button type="submit" class="sd-btn-submit">
                    <i class="fas fa-paper-plane"></i> Kirim Disposisi
                </button>
            </div>
        </form>
    </div>
</div>

    <script src="{{ asset('assets/sidongan/js/sidongan-disposisi-form.js') }}"></script>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
