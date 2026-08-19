{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="card">
    <div class="card-body">
        <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 1.5rem 0;">Alur Proses Surat di SIDONGAN</h3>
        
        <div class="workflow-container">
            {{-- Step 1: Pengirim (BISA DARI SIAPA SAJA) --}}
            <div class="workflow-step">
                <div class="workflow-icon" style="background: #e0e7ff;">
                    <i class="fas fa-paper-plane" style="color: #4f46e5;"></i>
                </div>
                <p class="workflow-title">Pengirim</p>
                <p class="workflow-desc">Pihak Bersangkutan</p>
            </div>
            
            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            {{-- Step 2: Sekretaris --}}
            <div class="workflow-step">
                <div class="workflow-icon" style="background: #dbeafe;">
                    <i class="fas fa-user-edit" style="color: #2563eb;"></i>
                </div>
                <p class="workflow-title">Sekretaris</p>
                <p class="workflow-desc">Agenda & Upload</p>
            </div>
            
            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            {{-- Step 3: Ketua PKK --}}
            <div class="workflow-step">
                <div class="workflow-icon" style="background: #fee2e2;">
                    <i class="fas fa-user-tie" style="color: #dc2626;"></i>
                </div>
                <p class="workflow-title">Ketua PKK</p>
                <p class="workflow-desc">Disposisi</p>
            </div>
            
            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            {{-- Step 4: Pelaksana --}}
            <div class="workflow-step">
                <div class="workflow-icon" style="background: #d1fae5;">
                    <i class="fas fa-users u-a80"></i>
                </div>
                <p class="workflow-title">Pelaksana</p>
                <p class="workflow-desc">Kegiatan & Laporan</p>
            </div>
            
            <div class="workflow-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
            
            {{-- Step 5: Ketua PKK Verifikasi --}}
            <div class="workflow-step">
                <div class="workflow-icon" style="background: #e9d5ff;">
                    <i class="fas fa-check-double" style="color: #7c3aed;"></i>
                </div>
                <p class="workflow-title">Ketua PKK</p>
                <p class="workflow-desc">Verifikasi</p>
            </div>
        </div>
    </div>
</div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-components-workflow.css') }}">
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
