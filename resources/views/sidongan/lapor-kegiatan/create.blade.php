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
    <div style="background: linear-gradient(135deg, #0891b2, #14b8a6); padding: 1.5rem 2rem; border-radius: 1rem; margin-bottom: 1.5rem; color: white; box-shadow: 0 4px 20px rgba(8, 145, 178, 0.2);">
        <div class="sd-page-header u-a89">
            <div class="u-flex-center-gap-3">
                <div class="u-icon-badge-sm">
                    <i class="fas fa-clipboard-list" style="font-size: 1.5rem; color: white;"></i>
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
        <div class="detail-surat-card" style="background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden; position: sticky; top: 1rem;">
            <div style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe); padding: 1.25rem 1.5rem; border-bottom: 2px solid #bae6fd;">
                <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    <span style="font-size: 0.8rem; font-family: monospace; background: #0891b2; color: white; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700;">
                        {{ $document->agenda_number }}
                    </span>
                    <span style="font-size: 0.8rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-weight: 600; background: #dbeafe; color: #1e40af;">
                        {{ $document->status === 'berjalan' ? 'Sedang Berjalan' : ucfirst(str_replace('_', ' ', $document->status)) }}
                    </span>
                </div>
                <h2 style="font-size: 1.125rem; font-weight: 700; color: #0c4a6e; margin: 0; line-height: 1.4;">
                    {{ $document->subject ?? $document->title }}
                </h2>
            </div>

            <div class="u-p-6">
                <div class="u-mb-6">
                    <h3 class="section-title u-section-title">
                        <i class="fas fa-envelope u-a25"></i>
                        Data Surat
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
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
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div class="info-row u-row-divider">
                            <span class="info-label u-text-sm-muted">Nomor Agenda</span>
                            <span class="info-value" style="font-size: 0.85rem; font-weight: 600; color: #0891b2; font-family: monospace;">{{ $document->agenda_number }}</span>
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
                    <span style="display: block; font-size: 0.8rem; color: #64748b; margin-bottom: 0.5rem; font-weight: 600;">Saran Sekretaris:</span>
                    <div style="background: #eff6ff; border-radius: 0.5rem; padding: 1rem; font-size: 0.85rem; color: #1e40af; border: 1px solid #bfdbfe; line-height: 1.6;">
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
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 3rem; height: 3rem; background: #fee2e2; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-file-pdf u-text-danger-lg"></i>
                            </div>
                            <div class="u-flex-1-min">
                                <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0 0 0.125rem 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
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
                            <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                @if(isset($dispo['target_roles']))
                                    @foreach($dispo['target_roles'] as $role)
                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.6rem; background: #dbeafe; color: #1e40af; border-radius: 0.375rem; font-size: 0.7rem; font-weight: 600;">
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
                            <span style="display: inline-block; padding: 0.375rem 0.75rem; background: #f3e8ff; color: #7c3aed; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;">
                                {{ $dispo['action'] }}
                            </span>
                        </div>
                        @endif
                        
                        @if(isset($dispo['comment']) && $dispo['comment'])
                        <div>
                            <span class="u-field-note-xs">Komentar:</span>
                            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.75rem; font-size: 0.8rem; color: #475569; font-style: italic; line-height: 1.5;">
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
            <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #dcfce7, #bbf7d0); border-bottom: 2px solid #86efac;">
                <div class="u-flex-center-gap-3">
                    <div style="width: 2.5rem; height: 2.5rem; background: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(22,163,74,0.3);">
                        <i class="fas fa-plus" style="color: white; font-size: 1rem;"></i>
                    </div>
                    <div>
                        <h2 class="form-header" style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;">Formulir Laporan Kegiatan</h2>
                        <p style="font-size: 0.8rem; color: #166534; margin: 0;">Lengkapi semua informasi kegiatan</p>
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
                    <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #fef2f2; border: 1px solid #fecaca; border-left: 4px solid #ef4444; border-radius: 0.5rem;">
                        <div class="u-p-6">
                            <i class="fas fa-circle-exclamation u-text-danger"></i>
                            <strong style="color: #b91c1c; font-size: 0.9rem;">Laporan sebelumnya untuk surat ini ditolak oleh Ketua</strong>
                        </div>
                        @if($previousReport->catatan_verifikasi)
                            <p style="margin: 0; font-size: 0.875rem; color: #7f1d1d; line-height: 1.6;">
                                Catatan: &ldquo;{{ $previousReport->catatan_verifikasi }}&rdquo;
                            </p>
                        @else
                            <p style="margin: 0; font-size: 0.875rem; color: #7f1d1d;">
                                Tidak ada catatan yang disertakan.
                            </p>
                        @endif
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #991b1b;">
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
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;">
                        @error('kegiatan_nama') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal Kegiatan --}}
                    <div class="u-mb-5">
                        <label class="u-label-slate">
                            Tanggal Kegiatan <span class="u-text-danger">*</span>
                        </label>
                        <input type="date" name="kegiatan_tanggal" required value="{{ old('kegiatan_tanggal', optional($previousReport->kegiatan_tanggal)->format('Y-m-d')) }}"
                            style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;">
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
                                <span style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem;">Jam Mulai</span>
                                <input type="time" name="start_time" id="startTime" required value="{{ old('start_time', $previousReport ? substr($previousReport->start_time, 0, 5) : '') }}"
                                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;">
                            </div>
                            <div>
                                <span style="display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem;">Jam Selesai</span>
                                <input type="time" name="end_time" id="endTime" required value="{{ old('end_time', $previousReport ? substr($previousReport->end_time, 0, 5) : '') }}"
                                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; box-sizing: border-box;">
                            </div>
                        </div>

                        {{-- Umpan balik langsung: durasi bila urutannya benar, peringatan bila terbalik.
                             Validasi server (after:start_time) tetap jadi penjaga terakhir. --}}
                        <p id="durasiKegiatan" role="status" aria-live="polite"
                           style="display: none; margin-top: 0.5rem; font-size: 0.8rem; padding: 0.5rem 0.75rem; border-radius: 0.375rem;"></p>

                        @error('start_time') <p class="u-error-text">{{ $message }}</p> @enderror
                        @error('end_time') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lokasi Kegiatan --}}
                    <div style="margin-bottom: 1.5rem; padding: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                        <h3 class="u-section-title">
                            <i class="fas fa-map-marker-alt u-a25"></i>
                            Lokasi Kegiatan
                        </h3>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;" class="location-grid">
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

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;" class="location-grid">
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
                                    style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; resize: vertical; box-sizing: border-box;">{{ old('alamat_lengkap', $previousReport->alamat_lengkap ?? '') }}</textarea>
                            @error('alamat_lengkap') <p class="u-error-text">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="u-mb-5">
                        <label class="u-label-slate">
                            Deskripsi Kegiatan <span class="u-text-danger">*</span>
                        </label>
                        <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail kegiatan yang dilaksanakan, peserta, hasil yang dicapai, dll..." required
                                style="width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; transition: all 0.2s; resize: vertical; box-sizing: border-box;">{{ old('deskripsi', $previousReport->deskripsi ?? '') }}</textarea>
                        @error('deskripsi') <p class="u-error-text">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dokumentasi Foto --}}
                    <div class="u-mb-6">
                        <label class="u-label-slate">
                            Dokumentasi Kegiatan (Foto) <span style="color: #64748b; font-weight: 400;">(Maksimal 10 foto)</span>
                        </label>
                        <div id="dropZone" style="border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.25s ease; background: #f8fafc;">
                            
                            {{-- Default State --}}
                            <div id="uploadPlaceholder">
                                <i style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;" class="fas fa-cloud-upload-alt"></i>
                                <p style="font-size: 0.95rem; color: #475569; margin: 0 0 0.5rem 0; font-weight: 600;">Klik untuk memilih file atau seret foto ke sini</p>
                                <p class="u-text-xs-muted-flat2">JPG, PNG, HEIC (Maks. 5MB per file - Maksimal 10 foto)</p>
                            </div>

                            {{-- File Preview State --}}
                            <div class="u-hidden" id="filePreview">
                                <div id="fileListContainer"></div>
                                <div id="fileButtons" style="display: flex; gap: 0.5rem; margin-top: 1rem; flex-wrap: wrap;">
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
                        <div id="fileCounter" style="margin-top: 0.75rem; padding: 0.75rem 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.5rem; display: none;">
                            <p style="font-size: 0.85rem; color: #1e40af; margin: 0; font-weight: 600;">
                                <i class="fas fa-info-circle u-mr-2"></i>
                                <span id="counterText">0 dari 10 foto dipilih</span>
                            </p>
                        </div>
                        
                        @error('fotos.*') <p style="font-size: 0.75rem; color: #ef4444; margin-top: 0.5rem;">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.25rem; border-top: 1px solid #e2e8f0;">
                        <button type="reset" class="sd-btn-reset">
                            <i class="fas fa-sync-alt"></i>
                            <span>Reset</span>
                        </button>

                        <div class="btn-group" style="display: flex; gap: 0.75rem;">
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
