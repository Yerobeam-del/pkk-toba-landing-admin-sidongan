<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip pemeliharaan: memindahkan blok <style> statis dari
 * setiap view Blade ke berkas CSS khusus halaman
 * (public/assets/<area>/css/<slug>.css) dan menggantinya
 * dengan <link>. Untuk partial, memakai @once + @push agar
 * hanya dimuat sekali.
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
$skippedData = [];

foreach ($it as $file) {
    if (! $file->isFile() || ! str_ends_with(strtolower($file->getFilename()), '.blade.php')) {
        continue;
    }
    $path = $file->getPathname();
    $rel = str_replace('\\', '/', str_replace($root . '/', '', $path));
    $content = file_get_contents($path);

    if (! preg_match('/<style[\s>]/', $content)) {
        continue;
    }

    $isPage = str_contains($content, '@extends') || preg_match('/^\s*(<!DOCTYPE|<html)/im', $content);

    // Kumpulkan blok style statis
    $blocks = [];
    if (preg_match_all('/<style[^>]*>(.*?)<\/style>/s', $content, $m, PREG_OFFSET_CAPTURE)) {
        foreach ($m[1] as $idx => $pair) {
            $style = $pair[0];
            $open = $m[0][$idx][1];
            $close = $m[0][$idx][1] + strlen($m[0][$idx][0]);

            if (str_contains($style, '{{')) {
                $skippedData[] = $rel;
                continue; // ada interpelasi Blade — ditangani manual
            }
            $blocks[] = ['style' => $style, 'open' => $open, 'close' => $close];
        }
    }

    if (! $blocks) {
        continue;
    }

    // Tulis CSS per halaman
    $area = areaFor($rel);
    $slug = slugFor($rel);
    $cssDir = $root . '/public/assets/' . $area . '/css';
    if (! is_dir($cssDir)) {
        mkdir($cssDir, 0777, true);
    }
    $cssPath = $cssDir . '/' . $slug . '.css';
    $cssContent = '/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * CSS halaman: ' . $rel . '
 * (diekstrak dari blok <style> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */

';
    foreach ($blocks as $b) {
        $cssContent .= $b['style'] . "\n";
    }
    file_put_contents($cssPath, $cssContent);

    // Ganti blok style (dari belakang agar offset aman)
    usort($blocks, fn ($a, $b) => $b['open'] <=> $a['open']);
    foreach ($blocks as $b) {
        $link = "    <link rel=\"stylesheet\" href=\"{{ asset('assets/{$area}/css/{$slug}.css') }}\">\n";
        if (! $isPage) {
            $link = "    @once\n    @push('styles')\n{$link}    @endpush\n    @endonce\n";
        }
        $content = substr($content, 0, $b['open']) . $link . substr($content, $b['close']);
    }

    file_put_contents($path, $content);
    $changed++;
    echo "✏️  {$rel} → assets/{$area}/css/{$slug}.css\n";
}

echo "\nBerkas diubah: {$changed}\n";
if ($skippedData) {
    echo "Dilewati (style dinamis, ditangani manual):\n";
    foreach (array_unique($skippedData) as $s) {
        echo "  - {$s}\n";
    }
}
