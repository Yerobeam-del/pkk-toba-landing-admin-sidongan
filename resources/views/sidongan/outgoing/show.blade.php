{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Detail Surat Keluar - SIDONGAN')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-outgoing.css') }}">
@endpush

@section('content')
@php
    $currentUser = auth()->guard('sidongan')->user();
    $isSekretaris = $currentUser && $currentUser->isSidonganSekretaris();
    $isKetua = $currentUser && $currentUser->isSidonganKetua();

    $statusConfig = [
        'draft' => ['class' => 'so-status-draft', 'label' => 'Draft', 'icon' => 'fa-pen'],
        'diajukan' => ['class' => 'so-status-diajukan', 'label' => 'Diajukan ke Ketua', 'icon' => 'fa-paper-plane'],
        'perlu_revisi' => ['class' => 'so-status-perlu_revisi', 'label' => 'Perlu Revisi', 'icon' => 'fa-rotate-left'],
        'disetujui' => ['class' => 'so-status-disetujui', 'label' => 'Disetujui', 'icon' => 'fa-check'],
        'dikirim' => ['class' => 'so-status-dikirim', 'label' => 'Dikirim', 'icon' => 'fa-envelope-open-text'],
        'diarsipkan' => ['class' => 'so-status-diarsipkan', 'label' => 'Diarsipkan', 'icon' => 'fa-archive'],
    ];
    $status = $statusConfig[$letter->status] ?? ['class' => 'so-status-draft', 'label' => $letter->status, 'icon' => 'fa-tag'];
@endphp
<div class="so-page">
    {{-- Header --}}
    <div class="so-header">
        <div class="so-header-top">
            <div>
                <h1>Detail Surat Keluar</h1>
                <p>
                    {{ $letter->outgoing_number ?: 'Draft Surat Keluar' }}
                    | Referensi Surat Masuk: {{ $letter->incomingDocument->agenda_number ?? '-' }}
                </p>
            </div>
            <div class="so-header-actions">
                <a href="{{ route('sidongan.outgoing.index') }}" class="so-btn so-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
                @if($letter->isEditable() && $isSekretaris)
                    <a href="{{ route('sidongan.outgoing.edit', $letter) }}" class="so-btn so-btn-primary">
                        <i class="fas fa-edit"></i>
                        <span>Edit Surat</span>
                    </a>
                    <a href="{{ route('sidongan.outgoing.word', $letter) }}" class="so-btn so-btn-primary">
                        <i class="fas fa-file-word"></i>
                        <span>Unduh Word</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Status & Subject --}}
    <div class="so-status-section">
        <div class="so-status-badges">
            <span class="so-badge-number">{{ $letter->outgoing_number ?: 'DRAFT' }}</span>
            <span class="so-badge-status {{ $status['class'] }}">
                <i class="fas {{ $status['icon'] }} u-text-xs"></i>
                {{ $status['label'] }}
            </span>
        </div>
        <h2 class="so-subject-title">{{ $letter->subject }}</h2>
    </div>

    {{-- Isi Surat --}}
    <div class="so-card">
        <div class="so-card-header so-card-header-blue">
            <h3>
                <i class="fas fa-file-signature"></i>
                Isi Surat Keluar
            </h3>
        </div>
        <div class="so-card-body">
            <div class="so-info-grid">
                <div>
                    <div class="so-info-row">
                        <span class="so-info-label">Kepada</span>
                        <span class="so-info-value">{{ $letter->recipient }}</span>
                    </div>
                    @if($letter->recipient_address)
                    <div class="so-info-row">
                        <span class="so-info-label">Alamat</span>
                        <span class="so-info-value">{{ $letter->recipient_address }}</span>
                    </div>
                    @endif
                    <div class="so-info-row">
                        <span class="so-info-label">Sifat</span>
                        <span class="so-info-value">{{ $letter->nature }}</span>
                    </div>
                    <div class="so-info-row">
                        <span class="so-info-label">Tanggal Surat</span>
                        <span class="so-info-value">
                            {{ $letter->letter_date ? $letter->letter_date->locale('id')->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>
                    @if($letter->attachment_description)
                    <div class="so-info-row">
                        <span class="so-info-label">Lampiran</span>
                        <span class="so-info-value">{{ $letter->attachment_description }}</span>
                    </div>
                    @endif
                    @if($letter->hasAttachment())
                    <div class="so-info-row">
                        <span class="so-info-label">File Lampiran</span>
                        <span class="so-info-value">
                            <a href="{{ route('sidongan.outgoing.attachment', $letter) }}" class="so-attachment-link">
                                <i class="fas fa-file-pdf"></i>
                                {{ $letter->attachment_name }}
                                <span class="so-cell-muted">({{ $letter->attachmentSizeHuman() }})</span>
                            </a>
                        </span>
                    </div>
                    @endif
                </div>
                <div>
                    <div class="so-info-row">
                        <span class="so-info-label">Nomor Surat Keluar</span>
                        <span class="so-info-value so-info-value-mono">{{ $letter->outgoing_number ?? 'Belum diterbitkan' }}</span>
                    </div>
                    <div class="so-info-row">
                        <span class="so-info-label">Dibuat oleh</span>
                        <span class="so-info-value">{{ $letter->creator->name ?? 'Sekretaris PKK' }}</span>
                    </div>
                    @if($letter->approver)
                    <div class="so-info-row">
                        <span class="so-info-label">Disetujui oleh</span>
                        <span class="so-info-value">{{ $letter->approver->name }}</span>
                    </div>
                    @endif
                    @if($letter->cc)
                    <div class="so-info-row">
                        <span class="so-info-label">Tembusan</span>
                        <span class="so-info-value">{{ $letter->cc }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="so-letter-body">{{ $letter->body }}</div>

            <div class="so-signature">
                <div>{{ $letter->signatory_title }}</div>
                <br><br>
                <div class="so-signature-name"><u>{{ $letter->signatory_name }}</u></div>
            </div>
        </div>
    </div>

    {{-- Alur Persetujuan --}}
    <div class="so-card">
        <div class="so-card-header so-card-header-orange">
            <h3>
                <i class="fas fa-stream"></i>
                Alur Persetujuan
            </h3>
        </div>
        <div class="so-card-body">
            <div class="so-timeline">
                @php
                    /* Riwayat asli (audit trail) — setiap aktivitas tercatat
                       saat kejadian, lengkap dengan pelakunya. */
                    $timeline = $letter->activities->map(function ($act) {
                        $display = \App\Models\OutgoingLetterActivity::display($act->action);
                        return [
                            'icon' => $display['icon'],
                            'color' => $display['color'],
                            'role' => $display['role'],
                            'title' => $act->user->name ?? ($act->user_id ? 'Pengguna #' . $act->user_id : 'Sistem'),
                            'date' => $act->created_at,
                            'desc' => $act->description ?? 'Memperbarui Surat Keluar',
                        ];
                    });
                @endphp
                @forelse($timeline as $item)
                <div class="so-timeline-item">
                    <div class="so-timeline-icon-col">
                        <div class="so-timeline-icon {{ $item['color'] }}">
                            <i class="{{ $item['icon'] }}"></i>
                        </div>
                        @if(!$loop->last)
                            <div class="so-timeline-line"></div>
                        @endif
                    </div>
                    <div class="so-timeline-content">
                        <div class="so-timeline-header">
                            <div>
                                <h4 class="so-timeline-title">{{ $item['title'] }}</h4>
                                <span class="so-timeline-role-badge">{{ $item['role'] }}</span>
                            </div>
                            <span class="so-timeline-date">{{ $item['date']->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                        </div>
                        <p class="so-timeline-desc">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @empty
                    <p class="so-timeline-desc">Belum ada riwayat aktivitas.</p>
                @endforelse
            </div>

            @if($letter->revision_note && $letter->status !== 'perlu_revisi')
            <div class="so-revision-box">
                <div class="so-revision-box-label">
                    <i class="fas fa-comment-dots"></i>
                    Catatan revisi terakhir
                </div>
                <p class="so-revision-box-text">"{{ $letter->revision_note }}"</p>
            </div>
            @endif

            <div class="so-ref-box" style="margin-top:1.25rem; margin-bottom:0">
                <span class="so-ref-item">
                    <i class="fas fa-inbox u-mr-1"></i> Surat Masuk sumber:
                    <a href="{{ route('sidongan.documents.show', $letter->incomingDocument) }}"><strong>{{ $letter->incomingDocument->agenda_number }}</strong></a>
                </span>
            </div>
        </div>
    </div>

    {{-- Impor dari Word --}}
    @if($letter->isEditable() && $isSekretaris)
    <div class="so-card">
        <div class="so-card-header so-card-header-blue">
            <h3>
                <i class="fas fa-file-word"></i>
                Impor Isi dari Word
            </h3>
        </div>
        <div class="so-card-body">
            <p class="so-import-hint">
                <i class="fas fa-circle-info u-mr-1"></i>
                Unduh Word di atas, edit isi surat di Microsoft Word (bagian antara penanda [MULAI ISI SURAT] dan [AKHIR ISI SURAT]), lalu unggah kembali di sini. Kop, nomor, dan tanda tangan tetap diatur sistem.
            </p>
            <form method="POST" action="{{ route('sidongan.outgoing.import-word', $letter) }}" enctype="multipart/form-data" class="so-import-form">
                @csrf
                <input type="file" name="body_file" id="body_file" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required class="so-form-input so-form-file">
                <button type="submit" class="so-btn so-btn-primary">
                    <i class="fas fa-upload"></i>
                    <span>Impor Isi Surat</span>
                </button>
            </form>
            @if($errors->has('body_file'))
            <div class="so-error-box" style="margin-top:1rem">
                <i class="fas fa-exclamation-circle u-mr-1"></i>
                {{ $errors->first('body_file') }}
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Actions --}}
    <div class="so-card">
        <div class="so-card-body">
            <div class="so-actions-row">
                @if(in_array($letter->status, ['draft', 'perlu_revisi']) && $isSekretaris)
                    <form method="POST" action="{{ route('sidongan.outgoing.submit', $letter) }}" class="so-form-inline">
                        @csrf
                        <button type="submit" class="so-btn so-btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            <span>Ajukan ke Ketua</span>
                        </button>
                    </form>
                @endif

                @if($letter->status === 'diajukan' && $isKetua)
                    <form method="POST" action="{{ route('sidongan.outgoing.approve', $letter) }}" class="so-form-inline" data-approve-letter>
                        @csrf
                        <button type="submit" class="so-btn so-btn-approve">
                            <i class="fas fa-check"></i>
                            <span>Setujui &amp; Terbitkan Nomor</span>
                        </button>
                    </form>
                    <button type="button" class="so-btn so-btn-revision" onclick="document.getElementById('revision-form').hidden=false">
                        <i class="fas fa-rotate-left"></i>
                        <span>Minta Revisi</span>
                    </button>
                @endif

                @if(in_array($letter->status, ['disetujui', 'dikirim', 'diarsipkan']))
                    <a href="{{ route('sidongan.outgoing.pdf', $letter) }}" target="_blank" rel="noopener noreferrer" class="so-btn so-btn-print">
                        <i class="fas fa-file-pdf"></i>
                        <span>Lihat PDF</span>
                    </a>
                @endif

                @if($letter->status === 'disetujui' && $isSekretaris)
                    <form method="POST" action="{{ route('sidongan.outgoing.sent', $letter) }}" class="so-form-inline">
                        @csrf
                        <button type="submit" class="so-btn so-btn-submit">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Tandai Terkirim</span>
                        </button>
                    </form>
                @endif

                @if(in_array($letter->status, ['disetujui', 'dikirim']) && $isSekretaris)
                    <form method="POST" action="{{ route('sidongan.outgoing.archive', $letter) }}" class="so-form-inline">
                        @csrf
                        <button type="submit" class="so-btn so-btn-archive">
                            <i class="fas fa-archive"></i>
                            <span>Arsipkan</span>
                        </button>
                    </form>
                @endif
            </div>

            <form id="revision-form" hidden method="POST" action="{{ route('sidongan.outgoing.revision', $letter) }}" style="margin-top:1.25rem">
                @csrf
                <div class="so-form-group">
                    <label class="so-form-label" for="revision_note">Catatan Revisi</label>
                    <textarea name="revision_note" id="revision_note" rows="3" required class="so-form-textarea" placeholder="Jelaskan bagian yang perlu diperbaiki Sekretaris"></textarea>
                </div>
                <div class="so-form-footer">
                    <button type="submit" class="so-btn so-btn-revision">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Permintaan Revisi</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * Konfirmasi persetujuan Surat Keluar — aksi ini menerbitkan
 * nomor surat permanen yang tidak dapat dipakai ulang.
 * ============================================================ */
(function () {
    'use strict';

    var approveForm = document.querySelector('form[data-approve-letter]');
    if (!approveForm) return;

    approveForm.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        var subject = @json(Str::limit($letter->subject, 80));

        Toast.confirm(
            'Nomor surat akan diterbitkan secara permanen dan tidak dapat dipakai ulang atau diubah.<br><br>' +
            '<strong>' + subject + '</strong><br>' +
            '<small style="color:#64748b;">Penerima: ' + @json($letter->recipient) + '</small>',
            {
                title: 'Setujui Surat Keluar?',
                confirmText: 'Ya, Setujui & Terbitkan Nomor',
                cancelText: 'Batal',
                type: 'warning'
            }
        ).then(function (confirmed) {
            if (!confirmed) return;
            approveForm.removeAttribute('data-approve-letter');
            approveForm.submit();
        });
    });
})();
</script>
@endpush
{{-- Dikembangkan oleh Institut Teknologi Del --}}
