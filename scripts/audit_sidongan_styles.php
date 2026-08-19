<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip audit: membandingkan kelas utilitas pada 6 berkas
 * SIDONGAN dengan style asli di versi HEAD, mencocokkan elemen
 * berdasarkan nama tag, atribut kunci (id/name/data-label), dan
 * isi teksnya.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$root = dirname(__DIR__);
chdir($root);

$cssPath = $root . '/public/assets/shared/css/utilities.css';
$css = file_get_contents($cssPath);

$classToStyle = [];
if (preg_match_all('/^\.([\w-]+) \{ (.*) \}$/m', $css, $m, PREG_SET_ORDER)) {
    foreach ($m as $r) {
        $classToStyle[$r[1]] = trim(html_entity_decode($r[2], ENT_QUOTES));
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

// Ekstrak elemen: tag, atribut, style, class, isi teks
function extractElements(string $html): array
{
    $els = [];
    if (preg_match_all('/<([a-zA-Z][\w-]*)([^>]*)>([^<]{0,80})/', $html, $m, PREG_SET_ORDER)) {
        foreach ($m as $r) {
            $tag = $r[1];
            $attrs = $r[2];
            $text = trim(html_entity_decode($r[3], ENT_QUOTES));
            $id = null;
            $name = null;
            $dataLabel = null;
            $style = null;
            $classes = [];
            if (preg_match('/\sid="([^"]*)"/', $attrs, $sm)) {
                $id = $sm[1];
            }
            if (preg_match('/\sname="([^"]*)"/', $attrs, $sm)) {
                $name = $sm[1];
            }
            if (preg_match('/\sdata-label="([^"]*)"/', $attrs, $sm)) {
                $dataLabel = $sm[1];
            }
            if (preg_match('/\sstyle="([^"]*)"/', $attrs, $sm)) {
                $style = trim(html_entity_decode($sm[1], ENT_QUOTES));
            }
            if (preg_match('/\sclass="([^"]*)"/', $attrs, $sm)) {
                $classes = preg_split('/\s+/', trim($sm[1]));
            }
            $els[] = [
                'tag' => $tag,
                'id' => $id,
                'name' => $name,
                'dataLabel' => $dataLabel,
                'style' => $style,
                'classes' => $classes,
                'text' => $text,
            ];
        }
    }
    return $els;
}

foreach ($files as $rel) {
    $work = file_get_contents($root . '/' . $rel);
    $head = shell_exec('git show HEAD:' . $rel);
    if ($work === false || $head === null) {
        continue;
    }

    $headEls = extractElements($head);
    $workEls = extractElements($work);

    echo "########## {$rel}\n";

    foreach ($workEls as $w) {
        // Cari kelas u-aN atau kelas mencurigakan (u-flex-1 pada <p> dll.)
        $auto = null;
        foreach ($w['classes'] as $c) {
            if (preg_match('/^u-a\d+$/', $c)) {
                $auto = $c;
            }
        }
        if ($auto === null) {
            continue;
        }

        // Cari elemen yang sama di HEAD
        $match = null;
        foreach ($headEls as $h) {
            if ($h['tag'] !== $w['tag']) {
                continue;
            }
            if ($w['id'] !== null && $h['id'] !== $w['id']) {
                continue;
            }
            if ($w['name'] !== null && $h['name'] !== $w['name']) {
                continue;
            }
            if ($w['dataLabel'] !== null && $h['dataLabel'] !== $w['dataLabel']) {
                continue;
            }
            if ($w['text'] !== '' && $h['text'] !== '' && $w['text'] !== $h['text']) {
                continue;
            }
            if ($h['style'] !== null) {
                $match = $h;
                break;
            }
        }

        if ($match === null) {
            echo "  {$auto} (tag={$w['tag']}, id={$w['id']}, text=\"{$w['text']}\"): TIDAK DITEMUKAN di HEAD\n";
            continue;
        }

        $curStyle = $classToStyle[$auto] ?? '(kelas tidak ada di utilities.css!)';
        $ok = $curStyle === $match['style'] ? '✓' : '✗';
        echo "  {$auto} {$ok} tag={$w['tag']} text=\"{$w['text']}\"\n";
        if ($ok !== '✓') {
            echo "      HEAD : {$match['style']}\n";
            echo "      KELAS: {$curStyle}\n";
        }
    }
}
