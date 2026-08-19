<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi satu kali: pindahkan skrip inline + handler pada
 * resources/views/sidongan/lapor-kegiatan/create.blade.php
 * ke berkas eksternal + atribut data-*.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$f = __DIR__ . '/../resources/views/sidongan/lapor-kegiatan/create.blade.php';
$blade = file_get_contents($f);

// ---------- 1. Ekstrak isi <script> ----------
if (!preg_match('/\r?\n<script>\r?\n(.*?)<\/script>\r?\n/s', $blade, $m)) {
    echo "Blok script tidak ditemukan!\n";
    exit(1);
}
$js = $m[1];
// Normalisasi CRLF -> LF untuk berkas JS
$js = str_replace("\r\n", "\n", $js);

// ---------- 2. Ganti data Blade di dalam JS ----------
// objek tersimpan -> baca dari atribut data-wilayah-tersimpan
$tersimpanOld = <<<'JSO'
        const tersimpan = {
            provinsi:  @json(old('provinsi',  $previousReport->provinsi ?? '')),
            kabupaten: @json(old('kabupaten', $previousReport->kabupaten ?? '')),
            kecamatan: @json(old('kecamatan', $previousReport->kecamatan ?? '')),
            kelurahan: @json(old('kelurahan', $previousReport->kelurahan ?? '')),
        };
JSO;
$tersimpanNew = <<<'JSO'
        const formWilayah = document.getElementById('laporanForm');
        const tersimpan = JSON.parse(
            (formWilayah && formWilayah.getAttribute('data-wilayah-tersimpan')) || '{}'
        );
JSO;
if (str_contains($js, 'const tersimpan = {')) {
    $js = str_replace($tersimpanOld, $tersimpanNew, $js);
} else {
    echo "Peringatan: blok tersimpan tidak ditemukan persis.\n";
    // Fallback regex
    $js = preg_replace('/const tersimpan = \{(?:.*?)\};/s', $tersimpanNew, $js, 1);
}

// ---------- 3. Ubah tombol remove (buatan JS) ----------
$js = str_replace(
    'onclick="removeFile(${index})"',
    'data-remove-index="${index}"',
    $js
);
$js = str_replace(
    'onmouseover="this.style.background=\'#fecaca\'" onmouseout="this.style.background=\'#fee2e2\'"',
    '',
    $js
);

// ---------- 4. Tambahkan delegation untuk tombol ----------
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

// ---------- 5. Tulis berkas JS ----------
$jsFile = __DIR__ . '/../public/assets/sidongan/js/lapor-kegiatan-create.js';
$header = "/* ============================================================\n"
    . " * Dikembangkan oleh Institut Teknologi Del\n"
    . " * ============================================================\n"
    . " * Lapor Kegiatan (Buat) — upload multi-file, dropdown wilayah\n"
    . " * bertingkat, dan umpan balik durasi kegiatan.\n"
    . " *\n"
    . " * Dikembangkan oleh Institut Teknologi Del\n"
    . " * ============================================================ */\n\n";
file_put_contents($jsFile, $header . $js);

// ---------- 6. Ubah blade ----------
// 6a. Form: tambah id + data-wilayah-tersimpan
$blade = str_replace(
    '<form action="{{ route(\'sidongan.lapor_kegiatan.store\') }}" method="POST" enctype="multipart/form-data">',
    '<form id="laporanForm" action="{{ route(\'sidongan.lapor_kegiatan.store\') }}" method="POST" enctype="multipart/form-data"'
        . "\r\n      data-wilayah-tersimpan='{{ json_encode([\r\n          \x27provinsi\x27 => old(\x27provinsi\x27, $previousReport->provinsi ?? \x27\x27),\r\n          \x27kabupaten\x27 => old(\x27kabupaten\x27, $previousReport->kabupaten ?? \x27\x27),\r\n          \x27kecamatan\x27 => old(\x27kecamatan\x27, $previousReport->kecamatan ?? \x27\x27),\r\n          \x27kelurahan\x27 => old(\x27kelurahan\x27, $previousReport->kelurahan ?? \x27\x27),\r\n      ]) }}'>",
    $blade
);

// 6b. Tombol addMore
$blade = preg_replace(
    '/<button type="button" id="addMoreBtn" onclick="addMoreFiles\(\)" style="([^"]*)" onmouseover="[^"]*" onmouseout="[^"]*">/',
    '<button type="button" id="addMoreBtn" data-action="add-more" class="sd-btn-add-more">',
    $blade
);

// 6c. Tombol changeFiles
$blade = preg_replace(
    '/<button type="button" onclick="changeFiles\(\)" style="([^"]*)" onmouseover="[^"]*" onmouseout="[^"]*">/',
    '<button type="button" data-action="change-files" class="sd-btn-change">',
    $blade
);

// 6d. Tombol reset / batal / submit: beri kelas
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

// 6e. Tombol "Lihat Dokumen" (ikon) — beri kelas
$blade = preg_replace(
    '/<a href="\{\{ asset\(\x27storage\/\x27 \. \$document->file_path\) \}\}" target="_blank" \s*style="display: inline-flex; align-items: center; justify-content: center; width: 2\.5rem; height: 2\.5rem; background: #dbeafe; color: #2563eb; border-radius: 0\.375rem; text-decoration: none; transition: all 0\.2s; flex-shrink: 0;" \s*onmouseover="[^"]*" \s*onmouseout="[^"]*" \s*title="Lihat Dokumen">/',
    '<a href="{{ asset(\'storage/\' . $document->file_path) }}" target="_blank" class="sd-doc-view" title="Lihat Dokumen">',
    $blade
);

// 6f. Tombol kembali (header, sd-btn-back): hapus handler hover
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

// 6g. Semua onfocus/onblur tersisa -> hapus
$blade = preg_replace(
    '/\s+onfocus="this\.style\.borderColor=\x27[^\x27]*\x27; this\.style\.boxShadow=\x27[^\x27]*\x27"/',
    '',
    $blade
);
$blade = preg_replace(
    '/\s+onblur="this\.style\.borderColor=\x27[^\x27]*\x27; this\.style\.boxShadow=\x27[^\x27]*\x27"/',
    '',
    $blade
);

// 6h. Sisa onmouseover/onmouseout -> hapus
$blade = preg_replace('/\s+onmouseover="this\.style\.[^"]*"/', '', $blade);
$blade = preg_replace('/\s+onmouseout="this\.style\.[^"]*"/', '', $blade);

// 6i. Ganti blok script dengan @push include
$blade = preg_replace(
    "/\r?\n<script>\r?\n.*?<\/script>\r?\n/s",
    "\r\n@push('scripts')\r\n    <script src=\"{{ asset('assets/sidongan/js/lapor-kegiatan-create.js') }}\"></script>\r\n@endpush\r\n",
    $blade,
    1,
    $count
);

file_put_contents($f, $blade);
echo "Selesai. script: $count | onclick: " . substr_count($blade, 'onclick')
    . " | onchange: " . substr_count($blade, 'onchange')
    . " | onfocus: " . substr_count($blade, 'onfocus')
    . " | onblur: " . substr_count($blade, 'onblur')
    . " | onmouseover: " . substr_count($blade, 'onmouseover')
    . " | onmouseout: " . substr_count($blade, 'onmouseout') . "\n";
