<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Pembersihan handler inline yang tersisa pada view:
 *  - onfocus/onblur ring (this.style.borderColor/boxShadow)
 *  - onmouseover/onmouseout (this.style.*)
 *  - onchange auto-submit filterForm
 * Lalu menambahkan @push skrip pemantik per halaman bila ada.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$files = array_slice($argv, 1);
if (!$files) {
    echo "Gunakan: php strip_handlers.php <file1> <file2> ...\n";
    exit(1);
}

foreach ($files as $f) {
    $path = __DIR__ . '/../' . $f;
    if (!is_file($path)) {
        echo "SKIP (tidak ada): $f\n";
        continue;
    }
    $t = file_get_contents($path);
    $before = $t;

    // 1. onfocus/onblur style ring (dengan/selain spasi setelah ;)
    $t = preg_replace('/\s+onfocus="this\.style\.borderColor=\x27[^\x27]*\x27;?\s*this\.style\.boxShadow=\x27[^\x27]*\x27"/', '', $t);
    $t = preg_replace('/\s+onblur="this\.style\.borderColor=\x27[^\x27]*\x27;?\s*this\.style\.boxShadow=\x27[^\x27]*\x27"/', '', $t);
    // onfocus/onblur hanya borderColor (tanpa boxShadow)
    $t = preg_replace('/\s+onfocus="this\.style\.borderColor=\x27[^\x27]*\x27"/', '', $t);
    $t = preg_replace('/\s+onblur="this\.style\.borderColor=\x27[^\x27]*\x27"/', '', $t);

    // 2. Semua onmouseover/onmouseout yang tersisa (style hack)
    $t = preg_replace('/\s+onmouseover="[^"]*"/', '', $t);
    $t = preg_replace('/\s+onmouseout="[^"]*"/', '', $t);

    // 3. onchange auto-submit (filterForm & this.form)
    $t = preg_replace('/\s+onchange="document\.getElementById\(\x27filterForm\x27\)\.submit\(\)"/', '', $t);
    $t = preg_replace('/\s+onchange="this\.form\.submit\(\)"/', '', $t);

    if ($t !== $before) {
        file_put_contents($path, $t);
        echo "DIUBAH: $f (onclick=" . substr_count($t, 'onclick')
            . " onchange=" . substr_count($t, 'onchange')
            . " onfocus=" . substr_count($t, 'onfocus')
            . " onmouseover=" . substr_count($t, 'onmouseover') . ")\n";
    } else {
        echo "TIDAK BERUBAH: $f\n";
    }
}
