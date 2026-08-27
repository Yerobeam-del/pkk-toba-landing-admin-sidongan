{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
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
                        <i class="fas fa-check-double u-a80"></i>
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
                    <div class="quick-action-title">Daftar Surat Masuk</div>
                    <div class="quick-action-desc">Lihat semua surat</div>
                </div>
            </a>

            <a href="{{ route('sidongan.arsip') }}" class="quick-action-item">
                <div class="quick-action-icon" style="background: #fef3c7;">
                    <i class="fas fa-archive u-a81"></i>
                </div>
                <div class="quick-action-text">
                    <div class="quick-action-title">Arsip Surat</div>
                    <div class="quick-action-desc">Dokumen tersimpan</div>
                </div>
            </a>
        </div>
    </div>
</div>

    @once
    @push('styles')
    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-dashboard-components-quick-actions.css') }}">
    @endpush
    @endonce

{{-- Dikembangkan oleh Institut Teknologi Del --}}
