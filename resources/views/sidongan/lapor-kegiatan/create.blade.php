{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('sidongan.layouts.app')
@section('title', 'Buat Laporan Kegiatan - SIDONGAN')

@section('content')
@php
    // ✅ Ambil URL kembali dari session
    $backUrl = session('lapor_kegiatan_back_url', route('sidongan.lapor_kegiatan.index'));
    
    // Validasi URL - pastikan bukan halaman create/edit/preview
    if (str_contains($backUrl, '/create') || 
        str_contains($backUrl, '/edit') || 
        str_contains($backUrl, '/disposisi-print')) {
        $backUrl = route('sidongan.lapor_kegiatan.index');
    }
@endphp

    <link rel="stylesheet" href="{{ asset('assets/sidongan/css/sidongan-lapor-kegiatan-create.css') }}">


<div class="lapor-container">
    {{-- HEADER --}}
    <div class="lk-page-header">
        <div class="sd-page-header u-a89">
            <div class="u-flex-center-gap-3">
                <div class="u-icon-badge-sm">
                    <i class="fas fa-clipboard-list lk-page-header-icon"></i>
                </div>
                <div>
                    <h1 class="form-header u-h3">Buat Laporan Kegiatan</h1>
                    <p class="u-subtitle-flat">Isi data kegiatan yang telah dilaksanakan</p>
                </div>
            </div>
            
            <div class="sd-header-actions">
                <a href="{{ $backUrl }}"
                   class="sd-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- LAYOUT 2 KOLOM --}}
    <div class="responsive-grid">
        
        {{-- KOLOM KIRI: Detail Surat --}}
        @if($document)
        <div class="detail-surat-card lk-detail-card">
            <div class="lk-detail-card-header">
                <div class="lk-detail-card-badges">
                    <span class="lk-badge-agenda">
                        {{ $document->agenda_number }}
                    </span>
                    <span class="lk-badge-status">
                        {{ $document->status === 'berjalan' ? 'Sedang Berjalan' : ucfirst(str_replace('_', ' ', $document->status)) }}
                    </span>
                </div>
                <h2 class="lk-detail-subject">
                    {{ $document->subject ?? $document->title }}
                </h2>
            </div>

            <div class="u-p-6">
                <div class="u-mb-6">
                    <h3 class="section-title u-section-title">
                        <i class="fas fa-envelope u-a25"></i>
                        Data Surat
                    </h3>
                    <div class="lk-info-stack">
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Pengirim</span>
                            <span class="info-value u-text-sm-strong-right">{{ $document->sender }}</span>
                        </div>
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Nomor Surat</span>
                            <span class="info-value u-text-sm-strong">{{ $document->document_number }}</span>
                        </div>
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Tanggal Surat</span>
                            <span class="info-value u-text-sm-strong">{{ $document->document_date ? \Carbon\Carbon::parse($document->document_date)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Perihal</span>
                            <span class="info-value u-text-sm-strong-right">{{ $document->subject }}</span>
                        </div>
                    </div>
                </div>

                <div class="u-mb-6">
                    <h3 class="section-title u-section-title">
                        <i class="fas fa-clipboard-list u-a25"></i>
                        Data Agenda
                    </h3>
                    <div class="lk-info-stack">
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Nomor Agenda</span>
                            <span class="info-value lk-agenda-number">{{ $document->agenda_number }}</span>
                        </div>
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Tanggal Agenda</span>
                            <span class="info-value u-text-sm-strong">{{ $document->created_at->locale('id')->translatedFormat('d F Y') }}</span>
                        </div>
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Dibuat oleh</span>
                            <span class="info-value u-text-sm-strong">{{ $document->creator->name ?? 'Sekretaris PKK' }}</span>
                        </div>
                    </div>
                </div>

                <div class="u-mb-6">
                    <span class="lk-saran-label">Saran Sekretaris:</span>
                    <div class="lk-saran-box">
                        {{ $document->suggestion ?? '-' }}
                    </div>
                </div>

                @if($document->file_path)
                <div class="u-mb-6">
                    <h3 class="section-title u-section-title-sm">
                        <i class="fas fa-paperclip u-a25"></i>
                        Lampiran Surat
                    </h3>
                    <div class="u-box-slate">
                        <div class="lk-lampiran-row">
                            <div class="lk-lampiran-icon-box">
                                <i class="fas fa-file-pdf u-text-danger-lg"></i>
                            </div>
                            <div class="u-flex-1-min">
                                <p class="lk-lampiran-name">
                                    {{ $document->file_name }}
                                </p>
                                <p class="u-text-xs-muted-flat">
                                    {{ $document->file_size ? round($document->file_size / 1024, 2) . ' KB' : 'File surat' }}
                                </p>
                            </div>
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" 
                               class="sd-doc-view"
                               title="Lihat Dokumen">
                                <i class="fas fa-eye u-text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                @if($document->disposisi_data)
                    @php
                        $dispo = is_string($document->disposisi_data) ? json_decode($document->disposisi_data, true) : $document->disposisi_data;
                    @endphp
                    <div class="u-box-slate">
                        <h3 class="section-title u-section-title-sm">
                            <i class="fas fa-share-alt u-a25"></i>
                            Disposisi Ketua
                        </h3>
                        
                        <div class="u-mb-3">
                            <span class="u-field-note-xs">Didisposisikan ke:</span>
                            <div class="lk-dispo-targets">
                                @if(isset($dispo['target_roles']))
                                    @foreach($dispo['target_roles'] as $role)
                                    <span class="lk-dispo-target-badge">
                                        <i class="fas fa-users u-text-xxs"></i>
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        @if(isset($dispo['action']))
                        <div class="u-mb-3">
                            <span class="u-field-note-xs">Tindakan:</span>
                            <span class="lk-dispo-action-badge">
                                {{ $dispo['action'] }}
                            </span>
                        </div>
                        @endif
                        
                        @if(isset($dispo['comment']) && $dispo['comment'])
                        <div>
                            <span class="u-field-note-xs">Komentar:</span>
                            <div class="lk-dispo-comment">
                                "{{ $dispo['comment'] }}"
                            </div>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- KOLOM KANAN: Form Laporan Kegiatan --}}
        <div class="u-card-white">
            <div class="lk-form-header">
                <div class="u-flex-center-gap-3">
                    <div class="lk-form-header-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div>
                        <h2 class="form-header lk-form-title">Formulir Laporan Kegiatan</h2>
                        <p class="lk-form-subtitle">Lengkapi semua informasi kegiatan</p>
                    </div>
                </div>
            </div>

            <form id="laporanForm" action="{{ route('sidongan.lapor_kegiatan.store') }}" method="POST" enctype="multipart/form-data"
      data-wilayah-tersimpan='{{ json_encode([
          'provinsi' => old('provinsi', $previousReport->provinsi ?? ''),
          'kabupaten' => old('kabupaten', $previousReport->kabupaten ?? ''),
          'kecamatan' => old('kecamatan', $previousReport->kecamatan ?? ''),
          'kelurahan' => old('kelurahan', $previousReport->kelurahan ?? ''),
      ]) }}'>
                @csrf
                
                @if($document)
                <input type="hidden" name="document_id" value="{{ $document->id }}">
                @endif

                <div class="u-p-6">

                    {{-- Catatan penolakan dari Ketua (revisi setelah ditolak).
                         Data laporan lama otomatis dimuat di form di bawah, dan
                         laporan baru akan dicatat atas nama yang mengisi. --}}
                    @if($previousReport)
                    <div class="lk-rejected-alert">
                        <div class="u-p-6">
                            <i class="fas fa-circle-exclamation u-text-danger"></i>
                            <strong class="lk-rejected-title">Laporan sebelumnya untuk surat ini ditolak oleh Ketua</strong>
                        </div>
                        @if($previousReport->catatan_verifikasi)
                            <p class="lk-rejected-note">
                                Catatan: &ldquo;{{ $previousReport->catatan_verifikasi }}&rdquo;
                            </p>
                        @else
                            <p class="lk-rejected-note-single">
                                Tidak ada catatan yang disertakan.
                            </p>
                        @endif
                        <p class="lk-rejected-hint">
                            Form di bawah sudah terisi data laporan sebelumnya &mdash; periksa, perbaiki sesuai catatan, lalu kirim ulang. Laporan baru akan dicatat atas nama Anda.
                        </p>
                    </div>
                    @endif

                    {{-- Nama Kegiatan --}}
                    <div class="u-mb-5">
                        <label class="u-label-slate">
                            Nama Kegiatan <span class="u-text-danger">*</span>
                        </label>
                        <input type="text" name="kegiatan_nama" placeholder="Contoh: Rapat Koordinasi Bulanan" required 
                            value="{{ old('kegiatan_nama', $previousReport->kegiatan_nama ?? ($document->subject ?? '')) }}"
                            class="lk-input">
                        @error('kegiatan_nama') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal Kegiatan --}}
                    <div class="u-mb-5">
                        <label class="u-label-slate">
                            Tanggal Kegiatan <span class="u-text-danger">*</span>
                        </label>
                        <input type="date" name="kegiatan_tanggal" required value="{{ old('kegiatan_tanggal', optional($previousReport->kegiatan_tanggal)->format('Y-m-d')) }}"
                            class="lk-input">
                        @error('kegiatan_tanggal') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Waktu Kegiatan: jam mulai & selesai disatukan karena berpasangan.
                         Sebelumnya Jam Mulai menempel di baris Tanggal sementara Jam Selesai
                         berdiri sendiri di baris berikutnya dengan lebar berbeda. --}}
                    <div class="u-mb-5">
                        <label class="u-label-slate">
                            Waktu Kegiatan <span class="u-text-danger">*</span>
                        </label>

                        <div class="time-grid u-grid-2-gap-4">
                            <div>
                                <span class="lk-time-label">Jam Mulai</span>
                                <input type="time" name="start_time" id="startTime" required value="{{ old('start_time', $previousReport ? substr($previousReport->start_time, 0, 5) : '') }}"
                                    class="lk-input">
                            </div>
                            <div>
                                <span class="lk-time-label">Jam Selesai</span>
                                <input type="time" name="end_time" id="endTime" required value="{{ old('end_time', $previousReport ? substr($previousReport->end_time, 0, 5) : '') }}"
                                    class="lk-input">
                            </div>
                        </div>

                        {{-- Umpan balik langsung: durasi bila urutannya benar, peringatan bila terbalik.
                             Validasi server (after:start_time) tetap jadi penjaga terakhir. --}}
                        <p id="durasiKegiatan" role="status" aria-live="polite"
                           class="lk-durasi-feedback"></p>

                        @error('start_time') <p class="u-error-text">{{ $message }}</p> @enderror
                        @error('end_time') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lokasi Kegiatan --}}
                    <div class="lk-location-section">
                        <h3 class="u-section-title">
                            <i class="fas fa-map-marker-alt u-a25"></i>
                            Lokasi Kegiatan
                        </h3>

                        <div class="lk-location-grid location-grid">
                            <div>
                                <label class="u-label-slate">
                                    Provinsi <span class="u-text-danger">*</span>
                                </label>
                                <select class="u-a50" name="provinsi" id="provinsiSelect" required>
                                    <option value="">Memuat data provinsi...</option>
                                </select>
                                @error('provinsi') <p class="u-error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="u-label-slate">
                                    Kab/Kota <span class="u-text-danger">*</span>
                                </label>
                                <select class="u-a50" name="kabupaten" id="kabupatenSelect" required>
                                    <option value="">Pilih provinsi terlebih dahulu</option>
                                </select>
                                @error('kabupaten') <p class="u-error-text">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="lk-location-grid location-grid">
                            <div>
                                <label class="u-label-slate">
                                    Kecamatan <span class="u-text-danger">*</span>
                                </label>
                                <select class="u-a50" name="kecamatan" id="kecamatanSelect" required>
                                    <option value="">Pilih kabupaten/kota terlebih dahulu</option>
                                </select>
                                @error('kecamatan') <p class="u-error-text">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="u-label-slate">
                                    Kelurahan/Desa <span class="u-text-danger">*</span>
                                </label>
                                <select class="u-a50" name="kelurahan" id="kelurahanSelect" required>
                                    <option value="">Pilih kecamatan terlebih dahulu</option>
                                </select>
                                @error('kelurahan') <p class="u-error-text">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="u-label-slate">
                                Alamat Lengkap <span class="u-text-danger">*</span>
                            </label>
                            <textarea name="alamat_lengkap" rows="3" required
                                    placeholder="Masukkan alamat lengkap kegiatan"
                                    class="lk-textarea">{{ old('alamat_lengkap', $previousReport->alamat_lengkap ?? '') }}</textarea>
                            @error('alamat_lengkap') <p class="u-error-text">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="u-mb-5">
                        <label class="u-label-slate">
                            Deskripsi Kegiatan <span class="u-text-danger">*</span>
                        </label>
                        <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail kegiatan yang dilaksanakan, peserta, hasil yang dicapai, dll..." required
                                class="lk-textarea">{{ old('deskripsi', $previousReport->deskripsi ?? '') }}</textarea>
                        @error('deskripsi') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dokumentasi Foto --}}
                    <div class="u-mb-6">
                        <label class="u-label-slate">
                            Dokumentasi Kegiatan (Foto) <span class="lk-foto-hint">(Maksimal 10 foto)</span>
                        </label>
                        <div id="dropZone" class="lk-dropzone">
                            
                            {{-- Default State --}}
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt lk-dropzone-icon"></i>
                                <p class="lk-dropzone-title">Klik untuk memilih file atau seret foto ke sini</p>
                                <p class="u-text-xs-muted-flat2">JPG, PNG, HEIC (Maks. 5MB per file - Maksimal 10 foto)</p>
                            </div>

                            {{-- File Preview State --}}
                            <div class="u-hidden" id="filePreview">
                                <div id="fileListContainer"></div>
                                <div id="fileButtons" class="lk-file-buttons">
                                    <button type="button" id="addMoreBtn" data-action="add-more" class="sd-btn-add-more">
                                        <i class="fas fa-plus"></i>
                                        Tambah Foto
                                    </button>
                                    <button type="button" data-action="change-files" class="sd-btn-change">
                                        <i class="fas fa-sync-alt"></i>
                                        Ganti File
                                    </button>
                                </div>
                            </div>

                            <input class="u-hidden" type="file" name="fotos[]" id="fileInput" accept="image/*, .heic" multiple>
                        </div>
                        
                        {{-- File Counter --}}
                        <div id="fileCounter" class="lk-file-counter">
                            <p class="lk-file-counter-text">
                                <i class="fas fa-info-circle u-mr-2"></i>
                                <span id="counterText">0 dari 10 foto dipilih</span>
                            </p>
                        </div>
                        
                        @error('fotos.*') <p class="lk-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="lk-form-footer">
                        <button type="reset" class="sd-btn-reset">
                            <i class="fas fa-sync-alt"></i>
                            <span>Reset</span>
                        </button>

                        <div class="btn-group lk-form-footer-btns">
                            <a href="{{ $backUrl }}" class="sd-btn-cancel">
                                Batal
                            </a>
                            <button type="submit" class="sd-btn-submit">
                                <i class="fas fa-paper-plane"></i>
                                <span>Kirim Laporan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/sidongan/js/lapor-kegiatan-create.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
