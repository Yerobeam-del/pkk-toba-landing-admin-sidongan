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
                    <i class="fas fa-users" style="color: #059669;"></i>
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

<style>
    .workflow-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }

    .workflow-step {
        text-align: center;
        min-width: 100px;
        flex-shrink: 0;
    }

    .workflow-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.5rem;
    }

    .workflow-icon i {
        font-size: 1.25rem;
    }

    .workflow-title {
        font-size: 0.75rem;
        font-weight: 600;
        color: #334155;
        margin: 0;
    }

    .workflow-desc {
        font-size: 0.7rem;
        color: #94a3b8;
        margin: 0;
    }

    .workflow-arrow {
        color: #cbd5e1;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    /* Mobile: Vertical Layout */
    @media (max-width: 768px) {
        .workflow-container {
            flex-direction: column;
            align-items: stretch;
            gap: 0;
        }

        .workflow-step {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-align: center; /* ← UBAH dari left ke center */
            min-width: auto;
            padding: 0.75rem 0;
            justify-content: center; /* ← TAMBAH: centerkan horizontal */
        }

        .workflow-icon {
            margin: 0;
            flex-shrink: 0;
        }

        .workflow-arrow {
            transform: rotate(90deg);
            margin: 0.5rem 0;
            align-self: center;
        }

        .workflow-title {
            font-size: 0.85rem;
        }

        .workflow-desc {
            font-size: 0.75rem;
        }
    }
</style>