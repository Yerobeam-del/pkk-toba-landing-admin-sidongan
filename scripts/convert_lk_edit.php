<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi satu kali: pindahkan skrip inline + handler pada
 * resources/views/sidongan/lapor-kegiatan/edit.blade.php
 * ke berkas eksternal + atribut data-*.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$f = __DIR__ . '/../resources/views/sidongan/lapor-kegiatan/edit.blade.php';
$blade = file_get_contents($f);

// ---------- 1. Ekstrak isi <script> ----------
if (!preg_match('/\r?\n<script>\r?\n(.*?)<\/script>\r?\n/s', $blade, $m)) {
    echo "Blok script tidak ditemukan!\n";
    exit(1);
}
$js = str_replace("\r\n", "\n", $m[1]);

// ---------- 2. Ganti data Blade di dalam JS ----------
$tersimpanOld = <<<'JSO'
        const tersimpan = {
            provinsi:  @json(old('provinsi',  $report->provinsi)),
            kabupaten: @json(old('kabupaten', $report->kabupaten)),
            kecamatan: @json(old('kecamatan', $report->kecamatan)),
            kelurahan: @json(old('kelurahan', $report->kelurahan)),
        };
JSO;
$tersimpanNew = <<<'JSO'
        const formWilayah = document.getElementById('laporanForm');
        const tersimpan = JSON.parse(
            (formWilayah && formWilayah.getAttribute('data-wilayah-tersimpan')) || '{}'
        );
JSO;
if (!str_contains($js, 'const tersimpan = {')) {
    echo "Peringatan: blok tersimpan tidak ditemukan.\n";
}
$js = str_replace($tersimpanOld, $tersimpanNew, $js);

// ---------- 3. Ubah tombol remove (buatan JS) ----------
$js = str_replace('onclick="removeFile(${index})"', 'data-remove-index="${index}"', $js);
$js = str_replace(
    'onmouseover="this.style.background=\'#fecaca\'" onmouseout="this.style.background=\'#fee2e2\'"',
    '',
    $js
);

// ---------- 4. Hapus duplikat changeFiles ----------
$dup = <<<'JSO'
    function changeFiles() {
        selectedFiles = [];
        fileInput.value = '';
        updateFileDisplay();
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }

    function addMoreFiles() {
        // Buka file picker tanpa menghapus file yang sudah ada
        fileInput.click();
    }

    function changeFiles() {
        // Reset semua file dan pilih file baru
        selectedFiles = [];
        fileInput.value = '';
        updateFileDisplay();
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }
JSO;
$dedup = <<<'JSO'
    function changeFiles() {
        // Reset semua file dan pilih file baru
        selectedFiles = [];
        fileInput.value = '';
        updateFileDisplay();
        setTimeout(() => {
            fileInput.click();
        }, 150);
    }

    function addMoreFiles() {
        // Buka file picker tanpa menghapus file yang sudah ada
        fileInput.click();
    }
JSO;
$js = str_replace($dup, $dedup, $js);

// ---------- 5. Delegation & focus ring ----------
$js .= <<<'JSO'

// ==========================================
// DELEGATION (menggantikan onclick inline)
// ==========================================
document.addEventListener('click', function (event) {
    // Tombol "Tambah Foto"
    const addBtn = event.target.closest('[data-action="add-more"]');
    if (addBtn) {
        addMoreFiles();
        return;
    }
    // Tombol "Ganti File"
    const changeBtn = event.target.closest('[data-action="change-files"]');
    if (changeBtn) {
        changeFiles();
        return;
    }
    // Tombol hapus satu file (dari daftar)
    const removeBtn = event.target.closest('[data-remove-index]');
    if (removeBtn) {
        removeFile(parseInt(removeBtn.getAttribute('data-remove-index'), 10));
    }
});

// ==========================================
// FOCUS RING (menggantikan onfocus/onblur inline)
// ==========================================
document.addEventListener('focusin', function (e) {
    const t = e.target;
    if (t && t.matches && t.matches('input[name="kegiatan_nama"], input[name="kegiatan_tanggal"], #startTime, #endTime, select[id$="Select"], textarea[name="alamat_lengkap"], textarea[name="deskripsi"]')) {
        t.classList.add('u-field-focused');
    }
});
document.addEventListener('focusout', function (e) {
    const t = e.target;
    if (t && t.matches && t.matches('input[name="kegiatan_nama"], input[name="kegiatan_tanggal"], #startTime, #endTime, select[id$="Select"], textarea[name="alamat_lengkap"], textarea[name="deskripsi"]')) {
        t.classList.remove('u-field-focused');
    }
});
JSO;

// ---------- 6. Tulis berkas JS ----------
$jsFile = __DIR__ . '/../public/assets/sidongan/js/lapor-kegiatan-edit.js';
$header = "/* ============================================================\n"
    . " * Dikembangkan oleh Institut Teknologi Del\n"
    . " * ============================================================\n"
    . " * Lapor Kegiatan (Edit) — upload multi-file, dropdown wilayah\n"
    . " * bertingkat, dan umpan balik durasi kegiatan.\n"
    . " *\n"
    . " * Dikembangkan oleh Institut Teknologi Del\n"
    . " * ============================================================ */\n\n";
file_put_contents($jsFile, $header . $js);

// ---------- 7. Ubah blade ----------
// 7a. Form: tambah id + data-wilayah-tersimpan
$formOld = '<form action="{{ route(\'sidongan.lapor_kegiatan.update\', $report->id) }}" method="POST" enctype="multipart/form-data">';
$formNew = '<form id="laporanForm" action="{{ route(\'sidongan.lapor_kegiatan.update\', $report->id) }}" method="POST" enctype="multipart/form-data"'
    . "\r\n      data-wilayah-tersimpan='{{ json_encode([\r\n          \x27provinsi\x27 => old(\x27provinsi\x27, $report->provinsi),\r\n          \x27kabupaten\x27 => old(\x27kabupaten\x27, $report->kabupaten),\r\n          \x27kecamatan\x27 => old(\x27kecamatan\x27, $report->kecamatan),\r\n          \x27kelurahan\x27 => old(\x27kelurahan\x27, $report->kelurahan),\r\n      ]) }}'>";
if (str_contains($blade, $formOld)) {
    $blade = str_replace($formOld, $formNew, $blade);
} else {
    echo "Peringatan: form tidak ditemukan untuk diberi data-wilayah-tersimpan.\n";
}

// 7b. addMoreBtn
$blade = preg_replace(
    '/<button type="button" id="addMoreBtn" onclick="addMoreFiles\(\)" style="([^"]*)" onmouseover="[^"]*" onmouseout="[^"]*">/',
    '<button type="button" id="addMoreBtn" data-action="add-more" class="sd-btn-add-more">',
    $blade
);
// 7c. changeFiles
$blade = preg_replace(
    '/<button type="button" onclick="changeFiles\(\)" style="([^"]*)" onmouseover="[^"]*" onmouseout="[^"]*">/',
    '<button type="button" data-action="change-files" class="sd-btn-change">',
    $blade
);
// 7d. reset / batal / submit
$blade = preg_replace(
    '/<button type="reset" \s*style="([^"]*)" \s*onmouseover="[^"]*" onmouseout="[^"]*">/',
    '<button type="reset" class="sd-btn-reset">',
    $blade
);
$blade = preg_replace(
    '/<a href="\{\{ \$backUrl \}\}" \s*style="display: inline-flex; align-items: center; gap: 0\.5rem; padding: 0\.75rem 1\.25rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0\.5rem; font-weight: 600; text-decoration: none; transition: all 0\.2s;" \s*onmouseover="[^"]*" onmouseout="[^"]*">/',
    '<a href="{{ $backUrl }}" class="sd-btn-cancel">',
    $blade
);
$blade = preg_replace(
    '/<button type="submit" \s*style="display: inline-flex; align-items: center; gap: 0\.5rem; padding: 0\.75rem 1\.5rem; background: linear-gradient\(135deg, #0891b2, #14b8a6\); color: white; border: none; border-radius: 0\.5rem; font-weight: 600; cursor: pointer; transition: all 0\.2s; box-shadow: 0 2px 8px rgba\(8,145,178,0\.2\);" \s*onmouseover="[^"]*" \s*onmouseout="[^"]*">/',
    '<button type="submit" class="sd-btn-submit">',
    $blade
);
// 7e. Lihat dokumen
$blade = preg_replace(
    '/<a href="\{\{ asset\([^)]*\. \$document->file_path\) \}\}" target="_blank" \s*style="display: inline-flex; align-items: center; justify-content: center; width: 2\.5rem; height: 2\.5rem; background: #dbeafe; color: #2563eb; border-radius: 0\.375rem; text-decoration: none; transition: all 0\.2s; flex-shrink: 0;" \s*onmouseover="[^"]*" \s*onmouseout="[^"]*" \s*title="Lihat Dokumen">/',
    '<a href="{{ asset(\'storage/\' . $document->file_path) }}" target="_blank" class="sd-doc-view" title="Lihat Dokumen">',
    $blade
);
// 7f. sd-btn-back header
$blade = preg_replace(
    '/\s+onmouseover="this\.style\.background=\x27rgba\(255,255,255,0\.35\)\x27; this\.style\.transform=\x27translateY\(-2px\)\x27"/',
    '',
    $blade
);
$blade = preg_replace(
    '/\s+onmouseout="this\.style\.background=\x27rgba\(255,255,255,0\.25\)\x27; this\.style\.transform=\x27translateY\(0\)\x27"/',
    '',
    $blade
);
// 7g. onfocus/onblur
$blade = preg_replace('/\s+onfocus="this\.style\.borderColor=\x27[^\x27]*\x27; this\.style\.boxShadow=\x27[^\x27]*\x27"/', '', $blade);
$blade = preg_replace('/\s+onblur="this\.style\.borderColor=\x27[^\x27]*\x27; this\.style\.boxShadow=\x27[^\x27]*\x27"/', '', $blade);
// 7h. sisa onmouseover/onmouseout
$blade = preg_replace('/\s+onmouseover="this\.style\.[^"]*"/', '', $blade);
$blade = preg_replace('/\s+onmouseout="this\.style\.[^"]*"/', '', $blade);
// 7i. Ganti blok script
$blade = preg_replace(
    "/\r?\n<script>\r?\n.*?<\/script>\r?\n/s",
    "\r\n@push('scripts')\r\n    <script src=\"{{ asset('assets/sidongan/js/lapor-kegiatan-edit.js') }}\"></script>\r\n@endpush\r\n",
    $blade,
    1,
    $count
);

file_put_contents($f, $blade);
echo "Selesai. script: $count | onclick: " . substr_count($blade, 'onclick')
    . " | onfocus: " . substr_count($blade, 'onfocus')
    . " | onblur: " . substr_count($blade, 'onblur')
    . " | onmouseover: " . substr_count($blade, 'onmouseover')
    . " | onmouseout: " . substr_count($blade, 'onmouseout') . "\n";
