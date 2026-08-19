<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip pemeliharaan: memulihkan style asli untuk kelas utilitas
 * otomatis (u-aN) yang dipakai berkas yang memiliki perubahan
 * belum di-commit, dengan mencocokkan isi elemen ke git HEAD.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$root = dirname(__DIR__);
chdir($root);
$cssPath = $root . '/public/assets/shared/css/utilities.css';

$files = [
    'resources/views/sidongan/dashboard/components/document-item.blade.php',
    'resources/views/sidongan/documents/index.blade.php',
    'resources/views/sidongan/documents/show.blade.php',
    'resources/views/sidongan/lapor-kegiatan/create.blade.php',
    'resources/views/sidongan/lapor-kegiatan/index.blade.php',
    'resources/views/sidongan/lapor-kegiatan/show.blade.php',
];

$css = file_get_contents($cssPath);
$existing = [];
if (preg_match_all('/^\.([\w-]+) \{/m', $css, $m)) {
    $existing = array_flip($m[1]);
}

// 1. Kumpulkan elemen berkas kerja yang memakai kelas yang belum ada di CSS
$missing = []; // class => [file, line, nextLines]

foreach ($files as $rel) {
    $work = file_get_contents($root . '/' . $rel);
    $lines = preg_split('/\r?\n/', $work);
    foreach ($lines as $i => $line) {
        if (! preg_match('/class="([^"]*u-a\d+[^"]*)"/', $line, $cm)) {
            continue;
        }
        foreach (preg_split('/\s+/', trim($cm[1])) as $cls) {
            if (! preg_match('/^u-a\d+$/', $cls) || isset($existing[$cls])) {
                continue;
            }
            // Konteks: baris elemen + 2 baris berikutnya (untuk pencocokan isi)
            $ctx = $line . "\n" . ($lines[$i + 1] ?? '') . "\n" . ($lines[$i + 2] ?? '');
            $missing[$cls][] = ['file' => $rel, 'ctx' => $ctx];
        }
    }
}

// 2. Cari style di versi HEAD: cocokkan konteks tanpa atribut class
$results = [];
foreach ($missing as $cls => $hits) {
    foreach ($hits as $hit) {
        $head = shell_exec('git show HEAD:' . $hit['file']);
        if ($head === null) {
            continue;
        }
        $ctx = preg_replace('/\sclass="[^"]*"/', '', $hit['ctx'], 1);
        // Potong ke 2 baris pertama untuk toleransi terhadap perubahan baris-bawah
        $ctxLines = explode("\n", $ctx);
        $needle = implode("\n", array_slice($ctxLines, 0, 2));

        // Cari baris dengan style yang mengandung elemen yang sama
        if (preg_match('/<([a-zA-Z][\w-]*)([^>]*)style="([^"]*)"[^>]*>/', $needle, $nm)) {
            $tagName = preg_quote($nm[1], '/');
            $attrs = $nm[2];
            $inner = trim($ctxLines[1] ?? '');
            $innerPat = $inner !== '' ? preg_quote(substr($inner, 0, 80), '/') : null;

            $pat = '/<(\s*)' . $tagName . '([^>]*style="([^"]*)"[^>]*\/?)>/';
            if (preg_match_all($pat, $head, $hm, PREG_SET_ORDER)) {
                foreach ($hm as $cand) {
                    if ($innerPat === null) {
                        $results[$cls] = $cand[3];
                        break 2;
                    }
                    // Ambil konteks setelah kandidat untuk dibandingkan
                    $pos = strpos($head, $cand[0]);
                    $after = substr($head, $pos, 400);
                    if (preg_match('/' . $innerPat . '/', $after)) {
                        $results[$cls] = $cand[3];
                        break 2;
                    }
                }
            }
        }
    }
}

// 3. Tulis aturan yang berhasil dipulihkan
$eol = "\n";
$added = 0;
foreach ($results as $cls => $style) {
    if (isset($existing[$cls])) {
        continue;
    }
    $css .= '/* ' . $cls . ' — ' . str_replace('*/', '* /', html_entity_decode($style, ENT_QUOTES)) . ' (dipulihkan dari HEAD) */' . $eol;
    $css .= '.' . $cls . ' { ' . html_entity_decode($style, ENT_QUOTES) . ' }' . $eol . $eol;
    $added++;
}

file_put_contents($cssPath, $css);

echo "Kelas hilang: " . count($missing) . " | Berhasil dipulihkan: {$added}\n";
foreach ($missing as $cls => $v) {
    echo '  ' . $cls . ': ' . (isset($results[$cls]) ? 'OK → ' . $results[$cls] : 'BELUM') . "\n";
}
