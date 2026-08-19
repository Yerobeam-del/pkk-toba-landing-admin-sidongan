<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip pemeliharaan: memindahkan blok <script> tanpa interpelasi
 * Blade ke berkas JS eksternal (public/assets/<area>/js/<slug>.js).
 * Blok yang memakai {{ }} / @json dilaporkan untuk ditangani manual.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$root = dirname(__DIR__);

function areaFor(string $rel): string
{
    if (str_starts_with($rel, 'resources/views/admin')) {
        return 'admin';
    }
    if (str_starts_with($rel, 'resources/views/sidongan') || str_starts_with($rel, 'resources/views/sidongan-auth')) {
        return 'sidongan';
    }
    if (str_starts_with($rel, 'resources/views/modules/landing')) {
        return 'landing';
    }
    return 'shared';
}

function slugFor(string $rel): string
{
    $rel = preg_replace('#^resources/views/#', '', $rel);
    $rel = preg_replace('#\.blade\.php$#', '', $rel);
    $rel = str_replace('/', '-', $rel);
    $rel = preg_replace('/[^a-z0-9_-]/i', '', $rel);
    return strtolower($rel);
}

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root . '/resources/views', FilesystemIterator::SKIP_DOTS)
);

$changed = 0;
$manual = [];

foreach ($it as $file) {
    if (! $file->isFile() || ! str_ends_with(strtolower($file->getFilename()), '.blade.php')) {
        continue;
    }
    $path = $file->getPathname();
    $rel = str_replace('\\', '/', str_replace($root . '/', '', $path));
    $content = file_get_contents($path);

    if (! preg_match('/<script[\s>]/', $content)) {
        continue;
    }

    $isPage = str_contains($content, '@extends') || preg_match('/^\s*(<!DOCTYPE|<html)/im', $content);

    // Kumpulkan blok script (hanya yang tanpa src)
    $blocks = [];
    if (preg_match_all('/<script(?![^>]*\bsrc=)[^>]*>(.*?)<\/script>/s', $content, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[1] as $idx => $pair) {
            $code = $pair[0];
            $open = $m[0][$idx][1];
            $close = $m[0][$idx][1] + strlen($m[0][$idx][0]);

            if (str_contains($code, '{{') || str_contains($code, '{!!') || str_contains($code, '@json')) {
                $manual[] = $rel . ' (' . substr(trim($code), 0, 60) . '...)';
                continue;
            }
            $blocks[] = ['code' => $code, 'open' => $open, 'close' => $close];
        }
    }

    if (! $blocks) {
        continue;
    }

    $area = areaFor($rel);
    $slug = slugFor($rel);
    $jsDir = $root . '/public/assets/' . $area . '/js';
    if (! is_dir($jsDir)) {
        mkdir($jsDir, 0777, true);
    }
    $jsPath = $jsDir . '/' . $slug . '.js';

    $jsContent = '/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: ' . $rel . '
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

';
    foreach ($blocks as $b) {
        $jsContent .= $b['code'] . "\n\n";
    }
    file_put_contents($jsPath, $jsContent);

    // Ganti blok script (dari belakang)
    usort($blocks, fn ($a, $b) => $b['open'] <=> $a['open']);
    foreach ($blocks as $b) {
        $tag = "    <script src=\"{{ asset('assets/{$area}/js/{$slug}.js') }}\"></script>\n";
        if (! $isPage) {
            $tag = "    @once\n    @push('scripts')\n{$tag}    @endpush\n    @endonce\n";
        }
        $content = substr($content, 0, $b['open']) . $tag . substr($content, $b['close']);
    }

    file_put_contents($path, $content);
    $changed++;
    echo "✏️  {$rel} → assets/{$area}/js/{$slug}.js\n";
}

echo "\nBerkas diubah: {$changed}\n";
if ($manual) {
    echo "PERLU DITANGANI MANUAL (ada interpelasi Blade):\n";
    foreach (array_unique($manual) as $s) {
        echo "  - {$s}\n";
    }
}
