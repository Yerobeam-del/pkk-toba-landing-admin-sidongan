{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Partial form Surat Keluar. Variabel yang dibutuhkan:
     $document (Surat Masuk sumber), $letter (model atau instance baru)
     ============================================================ --}}
@php
    $isEdit = $letter->exists;
    $formAction = $isEdit ? route('sidongan.outgoing.update', $letter) : route('sidongan.documents.outgoing.store', $document);
    $templates = $templates ?? [];
@endphp
<div class="so-page">
    <div class="so-header">
        <div class="so-header-top">
            <div>
                <h1>{{ $isEdit ? 'Edit Surat Keluar' : 'Buat Surat Keluar' }}</h1>
                <p>Draft otomatis dari Surat Masuk {{ $document->agenda_number ?? '-' }}</p>
            </div>
            <div class="so-header-actions">
                <a href="{{ $isEdit ? route('sidongan.outgoing.show', $letter) : route('sidongan.documents.show', $document) }}" class="so-btn so-btn-back">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Referensi Surat Masuk --}}
    <div class="so-ref-box">
        <span class="so-ref-item"><i class="fas fa-inbox u-mr-1"></i> <strong>Surat Masuk:</strong> {{ $document->agenda_number ?? '-' }}</span>
        <span class="so-ref-item"><i class="fas fa-envelope u-mr-1"></i> <strong>Nomor Surat:</strong> {{ $document->document_number ?? '-' }}</span>
        <span class="so-ref-item"><i class="fas fa-user u-mr-1"></i> <strong>Pengirim:</strong> {{ $document->sender ?? '-' }}</span>
        <span class="so-ref-item"><i class="fas fa-tag u-mr-1"></i> <strong>Perihal:</strong> {{ $document->subject ?? $document->title ?? '-' }}</span>
    </div>

    @if(!$isEdit && count($templates) > 0)
    <div class="so-card">
        <div class="so-card-header so-card-header-orange">
            <h3>
                <i class="fas fa-layer-group"></i>
                Pilih Template Surat (Opsional)
            </h3>
        </div>
        <div class="so-card-body">
            <p class="so-template-hint">Pilih template untuk mengisi perihal dan isi surat secara otomatis. Ganti bagian dalam [kurung siku] sesuai kebutuhan.</p>
            <div class="so-template-grid" id="templatePicker" data-templates='@json($templates)'>
                @foreach($templates as $key => $template)
                <button type="button" class="so-template-card" data-template-key="{{ $key }}">
                    <div class="so-template-icon so-template-icon-{{ $template['color'] ?? 'blue' }}">
                        <i class="fas {{ $template['icon'] ?? 'fa-file-alt' }}"></i>
                    </div>
                    <div class="so-template-info">
                        <div class="so-template-label">{{ $template['label'] }}</div>
                        <div class="so-template-desc">{{ $template['description'] }}</div>
                    </div>
                    <div class="so-template-use">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        <span>Gunakan</span>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="so-card">
        <div class="so-card-header so-card-header-blue">
            <h3>
                <i class="fas fa-file-signature"></i>
                Data Surat Keluar
            </h3>
        </div>
        <div class="so-card-body">
            @if($errors->any())
            <div class="so-error-box">
                <i class="fas fa-exclamation-circle u-mr-1"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
                @csrf
                @if($isEdit) @method('PUT') @endif

                <h4 class="so-form-section-title">
                    <i class="fas fa-address-card"></i>
                    Penerima &amp; Identitas Surat
                </h4>
                <div class="so-form-grid">
                    <div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="recipient">Penerima <span class="text-danger">*</span></label>
                            <input type="text" name="recipient" id="recipient" value="{{ old('recipient', $letter->recipient) }}" required class="so-form-input" placeholder="Nama/instansi penerima">
                        </div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="recipient_address">Alamat Penerima</label>
                            <textarea name="recipient_address" id="recipient_address" rows="3" class="so-form-textarea" placeholder="Alamat lengkap penerima">{{ old('recipient_address', $letter->recipient_address) }}</textarea>
                        </div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="nature">Sifat Surat <span class="text-danger">*</span></label>
                            <select name="nature" id="nature" class="so-form-select">
                                @foreach(['Biasa', 'Penting', 'Segera'] as $opt)
                                <option value="{{ $opt }}" {{ old('nature', $letter->nature ?? 'Biasa') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="letter_date">Tanggal Surat <span class="text-danger">*</span></label>
                            <input type="date" name="letter_date" id="letter_date" value="{{ old('letter_date', optional($letter->letter_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required class="so-form-input">
                        </div>
                    </div>
                    <div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="subject">Perihal <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" value="{{ old('subject', $letter->subject) }}" required class="so-form-input" placeholder="Perihal surat">
                        </div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="attachment_description">Keterangan Lampiran</label>
                            <input type="text" name="attachment_description" id="attachment_description" value="{{ old('attachment_description', $letter->attachment_description) }}" class="so-form-input" placeholder="Contoh: 1 (satu) bendel jadwal kegiatan">
                        </div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="attachment_file">File Lampiran (PDF, maks 5MB)</label>
                            @if($letter->hasAttachment())
                            <div class="so-attachment-current">
                                <i class="fas fa-file-pdf"></i>
                                <span class="so-attachment-current-name">{{ $letter->attachment_name }}</span>
                                <span class="so-attachment-current-size">{{ $letter->attachmentSizeHuman() }}</span>
                                <label class="so-attachment-remove">
                                    <input type="checkbox" name="remove_attachment" value="1">
                                    Hapus lampiran
                                </label>
                            </div>
                            @endif
                            <input type="file" name="attachment_file" id="attachment_file" accept="application/pdf,.pdf" class="so-form-input so-form-file">
                            <small class="so-form-hint">File ini akan digabungkan ke PDF final surat setelah disetujui Ketua.</small>
                        </div>
                        <div class="so-form-group">
                            <label class="so-form-label" for="cc">Tembusan</label>
                            <textarea name="cc" id="cc" rows="3" class="so-form-textarea" placeholder="Tembusan surat (jika ada)">{{ old('cc', $letter->cc) }}</textarea>
                        </div>
                    </div>
                </div>

                <h4 class="so-form-section-title">
                    <i class="fas fa-align-left"></i>
                    Isi Surat &amp; Penandatangan
                </h4>
                <div class="so-form-group">
                    <label class="so-form-label" for="body">Isi Surat <span class="text-danger">*</span></label>
                    <textarea name="body" id="body" rows="12" required class="so-form-textarea" placeholder="Tulis isi surat di sini...">{{ old('body', $letter->body) }}</textarea>
                </div>
                <div class="so-form-grid">
                    <div class="so-form-group">
                        <label class="so-form-label" for="signatory_name">Nama Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="signatory_name" id="signatory_name" value="{{ old('signatory_name', $letter->signatory_name) }}" required class="so-form-input">
                    </div>
                    <div class="so-form-group">
                        <label class="so-form-label" for="signatory_title">Jabatan Penandatangan <span class="text-danger">*</span></label>
                        <input type="text" name="signatory_title" id="signatory_title" value="{{ old('signatory_title', $letter->signatory_title ?: 'Ketua TP PKK Kabupaten Toba') }}" required class="so-form-input">
                    </div>
                </div>

                <div class="so-form-footer">
                    <a href="{{ $isEdit ? route('sidongan.outgoing.show', $letter) : route('sidongan.documents.show', $document) }}" class="so-btn so-btn-muted">
                        <i class="fas fa-times"></i>
                        <span>Batal</span>
                    </a>
                    <button type="submit" class="so-btn so-btn-submit">
                        <i class="fas fa-save"></i>
                        <span>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Draft' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
