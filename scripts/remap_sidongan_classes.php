<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip pemeliharaan: memetakan ulang kelas utilitas otomatis
 * (u-aN) pada 6 berkas SIDONGAN yang punya perubahan belum
 * di-commit, agar konsisten dengan utilities.css saat ini.
 *
 * Pasangan style→class diambil dari git diff --unified=0:
 * baris "-" dengan style dimasukkan ke antrean, baris "+"
 * dengan kelas u-aN mengambil satu style dari antrean.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$root = dirname(__DIR__);
chdir($root);

$cssPath = $root . '/public/assets/shared/css/utilities.css';
$css = file_get_contents($cssPath);

$styleToClass = [];
if (preg_match_all('/^\.([\w-]+) \{ (.*) \}$/m', $css, $m, PREG_SET_ORDER)) {
    foreach ($m as $r) {
        $styleToClass[trim(html_entity_decode($r[2], ENT_QUOTES))] = $r[1];
    }
}

$files = [
    'resources/views/sidongan/dashboard/components/document-item.blade.php',
    'resources/views/sidongan/documents/index.blade.php',
    'resources/views/sidongan/documents/show.blade.php',
    'resources/views/sidongan/lapor-kegiatan/create.blade.php',
    'resources/views/sidongan/lapor-kegiatan/index.blade.php',
    'resources/views/sidongan/lapor-kegiatan/show.blade.php',
];

foreach ($files as $rel) {
    $work = file_get_contents($root . '/' . $rel);
    if ($work === false) {
        continue;
    }

    $diff = shell_exec('git diff --unified=0 HEAD -- ' . $rel);
    if ($diff === null) {
        continue;
    }

    // ---- Bangun pasangan u-aN → style asli ----
    $classToStyle = [];
    $queue = [];

    foreach (preg_split('/\r?\n/', $diff) as $line) {
        if (str_starts_with($line, '@@')) {
            $queue = []; // batas hunk — jangan sampai bocor antar hunk
            continue;
        }
        if (str_starts_with($line, '-') && ! str_starts_with($line, '---')) {
            $body = substr($line, 1);
            if (preg_match('/style="([^"]*)"/', $body, $sm)) {
                $queue[] = trim(html_entity_decode($sm[1], ENT_QUOTES));
            }
        } elseif (str_starts_with($line, '+') && ! str_starts_with($line, '+++')) {
            $body = substr($line, 1);
            if (preg_match('/class="([^"]*u-a\d+[^"]*)"/', $body, $pm)) {
                foreach (preg_split('/\s+/', trim($pm[1])) as $cls) {
                    if (preg_match('/^u-a\d+$/', $cls) && $queue) {
                        $classToStyle[$cls] = array_shift($queue);
                    }
                }
            }
        }
    }

    // ---- Terapkan pemetaan ulang ----
    $newContent = preg_replace_callback(
        '/<([a-zA-Z][\w-]*)([^>]*)>/s',
        function ($m) use ($classToStyle, $styleToClass) {
            $tag = $m[0];
            if (! preg_match('/class="([^"]*)"/', $tag, $cm)) {
                return $tag;
            }
            $tokens = preg_split('/\s+/', trim($cm[1]));
            $newTokens = [];
            $changed = false;
            $revertStyles = [];

            foreach ($tokens as $t) {
                if (preg_match('/^u-a\d+$/', $t) && isset($classToStyle[$t])) {
                    $style = $classToStyle[$t];
                    if (isset($styleToClass[$style])) {
                        $newTokens[] = $styleToClass[$style];
                    } else {
                        $revertStyles[] = $style;
                    }
                    $changed = true;
                } else {
                    $newTokens[] = $t;
                }
            }

            if (! $changed) {
                return $tag;
            }

            $classAttr = $newTokens ? 'class="' . implode(' ', $newTokens) . '"' : '';
            $tag = preg_replace('/\sclass="[^"]*"/', '', $tag, 1);

            // Sisipkan class baru
            if ($classAttr !== '') {
                if (preg_match('/class="([^"]*)"/', $tag, $ex)) {
                    $merged = array_values(array_unique(array_merge(
                        preg_split('/\s+/', trim($ex[1])),
                        $newTokens
                    )));
                    $tag = str_replace($ex[0], 'class="' . implode(' ', $merged) . '"', $tag);
                } else {
                    $tag = preg_replace('/^(<\w+)/', '$1 ' . $classAttr, $tag, 1);
                }
            }

            // Kembalikan style inline (jika tidak ada kelas pengganti)
            foreach ($revertStyles as $s) {
                if (! preg_match('/\sstyle="/', $tag)) {
                    $tag = preg_replace('/^(<\w+)/', '$1 style="' . $s . '"', $tag, 1);
                } else {
                    $tag = preg_replace('/\sstyle="([^"]*)"/', ' style="$1; ' . $s . '"', $tag, 1);
                }
            }

            return $tag;
        },
        $work
    );

    if ($newContent !== $work) {
        file_put_contents($root . '/' . $rel, $newContent);
        echo "✏️  {$rel}\n";
    } else {
        echo "•  {$rel} (tidak berubah)\n";
    }

    foreach ($classToStyle as $cls => $style) {
        $target = $styleToClass[$style] ?? 'INLINE';
        echo "   {$cls} → {$target}\n";
    }
}
