<div class="card">
    <div class="card-body">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0 0 1rem 0;">Aksi Cepat</h3>
        <div class="quick-actions-grid">
            @if($currentUser && $currentUser->hasSidonganRole('sekretaris'))
                <a href="{{ route('sidongan.documents.create') }}" class="quick-action-item">
                    <div class="quick-action-icon" style="background: #dbeafe;">
                        <i class="fas fa-plus" style="color: #2563eb;"></i>
                    </div>
                    <div class="quick-action-text">
                        <div class="quick-action-title">Buat Surat Baru</div>
                        <div class="quick-action-desc">Input surat masuk</div>
                    </div>
                </a>
            @endif

            @if($currentUser && $currentUser->hasSidonganRole('ketua'))
                <a href="{{ route('sidongan.disposisi') }}" class="quick-action-item">
                    <div class="quick-action-icon" style="background: #ffedd5;">
                        <i class="fas fa-tasks" style="color: #ea580c;"></i>
                    </div>
                    <div class="quick-action-text">
                        <div class="quick-action-title">Disposisi Surat</div>
                        <div class="quick-action-desc">Tindak lanjuti surat</div>
                    </div>
                </a>

                <a href="{{ route('sidongan.verifikasi') }}" class="quick-action-item">
                    <div class="quick-action-icon" style="background: #d1fae5;">
                        <i class="fas fa-check-double" style="color: #059669;"></i>
                    </div>
                    <div class="quick-action-text">
                        <div class="quick-action-title">Verifikasi Laporan</div>
                        <div class="quick-action-desc">Setujui laporan</div>
                    </div>
                </a>
            @endif

            @if($currentUser && ($currentUser->hasSidonganRole('bendahara') || $currentUser->isSidonganPokja()))
                <a href="{{ route('sidongan.lapor_kegiatan.create') }}" class="quick-action-item">
                    <div class="quick-action-icon" style="background: #dcfce7;">
                        <i class="fas fa-clipboard-list" style="color: #16a34a;"></i>
                    </div>
                    <div class="quick-action-text">
                        <div class="quick-action-title">Lapor Kegiatan</div>
                        <div class="quick-action-desc">Laporkan aktivitas</div>
                    </div>
                </a>
            @endif

            <a href="{{ route('sidongan.documents.index') }}" class="quick-action-item">
                <div class="quick-action-icon" style="background: #f3e8ff;">
                    <i class="fas fa-list" style="color: #9333ea;"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Daftar Surat</div>
                    <div class="quick-action-desc">Lihat semua surat</div>
                </div>
            </a>

            <a href="{{ route('sidongan.arsip') }}" class="quick-action-item">
                <div class="quick-action-icon" style="background: #fef3c7;">
                    <i class="fas fa-archive" style="color: #d97706;"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Arsip Surat</div>
                    <div class="quick-action-desc">Dokumen tersimpan</div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
        background: white;
        text-decoration: none;
        transition: all 0.2s;
    }

    .quick-action-item:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }

    .quick-action-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quick-action-text {
        flex: 1;
        min-width: 0;
    }

    .quick-action-title {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.95rem;
    }

    .quick-action-desc {
        font-size: 0.75rem;
        color: #64748b;
    }
</style>