<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip pemeliharaan tanda tangan pengembang.
 *
 * Menyisipkan blok kredit "Dikembangkan oleh Institut Teknologi Del"
 * (header + footer + penanda tersembunyi) ke setiap berkas kode:
 * PHP, Blade, JavaScript, dan CSS.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$MARKER = 'Dikembangkan oleh Institut Teknologi Del';

$roots = [
    __DIR__ . '/../app',
    __DIR__ . '/../resources',
    __DIR__ . '/../routes',
    __DIR__ . '/../config',
    __DIR__ . '/../database',
    __DIR__ . '/../public',
];

$extensions = ['php', 'blade.php', 'js', 'css'];

function collectFiles(array $roots, array $extensions): array
{
    $files = [];
    foreach ($roots as $root) {
        if (! is_dir($root)) {
            continue;
        }
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            if (! $file->isFile()) {
                continue;
            }
            $path = $file->getPathname();
            $relative = str_replace('\\', '/', $path);
            if (str_contains($relative, '/vendor/') || str_contains($relative, '/node_modules/')) {
                continue;
            }
            foreach ($extensions as $ext) {
                if (str_ends_with(strtolower($relative), $ext)) {
                    $files[] = $path;
                    break;
                }
            }
        }
    }
    return $files;
}

function detectEol(string $content): string
{
    return str_contains($content, "\r\n") ? "\r\n" : "\n";
}

function headerFor(string $relative, string $eol): string
{
    $ext = pathinfo($relative, PATHINFO_EXTENSION);

    if (in_array($ext, ['js', 'css'], true)) {
        return '/* ============================================================'
            . $eol . ' * Dikembangkan oleh Institut Teknologi Del'
            . $eol . ' * ============================================================ */'
            . $eol;
    }

    // Blade (HTML) — komentar Blade disembunyikan dari output browser
    if (str_ends_with(strtolower($relative), '.blade.php')) {
        return '{{-- ============================================================'
            . $eol . '     Dikembangkan oleh Institut Teknologi Del'
            . $eol . '     ============================================================ --}}'
            . $eol;
    }

    // PHP
    return '/* ============================================================'
        . $eol . ' * Dikembangkan oleh Institut Teknologi Del'
        . $eol . ' * ============================================================ */'
        . $eol;
}

function footerFor(string $relative, string $eol): string
{
    if (str_ends_with(strtolower($relative), '.blade.php')) {
        return $eol . '{{-- Dikembangkan oleh Institut Teknologi Del --}}';
    }
    return $eol . '/* Dikembangkan oleh Institut Teknologi Del */';
}

$files = collectFiles($roots, $extensions);
sort($files);

$updated = 0;
$skipped = 0;

foreach ($files as $path) {
    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }
    if (str_contains($content, $MARKER)) {
        $skipped++;
        continue;
    }

    $eol = detectEol($content);
    $relative = str_replace('\\', '/', $path);
    $relative = str_replace(__DIR__ . '/../', '', $relative);
    $header = headerFor($relative, $eol);
    $footer = footerFor($relative, $eol);

    if (str_ends_with(strtolower($relative), '.blade.php')) {
        // Blade: header di bagian paling atas
        $new = $header . $content;
    } elseif (str_ends_with(strtolower($relative), '.php')) {
        // PHP: sisipkan setelah tag <?php
        if (preg_match('/^(\xEF\xBB\xBF)?<\?php\s*/i', $content, $m, PREG_OFFSET_CAPTURE)) {
            $offset = strlen($m[0][0]);
            $new = substr($content, 0, $offset) . $eol . $eol . $header
                . ltrim(substr($content, $offset), " \t") . $eol;
        } else {
            $new = $header . $content;
        }
    } else {
        // JS / CSS: header di bagian paling atas
        $new = $header . $content;
    }

    // Footer selalu ditambahkan di akhir
    $new = rtrim($new, "\r\n ") . $footer . $eol;

    if (file_put_contents($path, $new) !== false) {
        $updated++;
    }
}

echo "Selesai. Berkas diperbarui: {$updated}, sudah memiliki tanda tangan: {$skipped}\n";
