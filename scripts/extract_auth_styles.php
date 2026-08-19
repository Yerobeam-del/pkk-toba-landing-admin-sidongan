<?php
// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// ============================================================
// Ekstrak <style> dari 6 halaman auth ke file CSS eksternal,
// ubah url("{{ asset(...) }}") menjadi path relatif.
// ============================================================

$root = __DIR__ . '/..';

$pages = [
    // [blade, cssPath, imagePaths]
    ['resources/views/auth/login.blade.php',            'public/assets/auth/css/auth-login.css',            'public/assets/auth/css/'],
    ['resources/views/auth/forgot-password.blade.php',  'public/assets/auth/css/auth-forgot-password.css',  'public/assets/auth/css/'],
    ['resources/views/auth/reset-password.blade.php',   'public/assets/auth/css/auth-reset-password.css',   'public/assets/auth/css/'],
    ['resources/views/sidongan-auth/login.blade.php',           'public/assets/sidongan-auth/css/auth-login.css',           'public/assets/sidongan-auth/css/'],
    ['resources/views/sidongan-auth/forgot-password.blade.php', 'public/assets/sidongan-auth/css/auth-forgot-password.css', 'public/assets/sidongan-auth/css/'],
    ['resources/views/sidongan-auth/reset-password.blade.php',  'public/assets/sidongan-auth/css/auth-reset-password.css',  'public/assets/sidongan-auth/css/'],
];

$header = <<<'CSS'
/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * CSS halaman: auth (diekstrak dari blok <style> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
CSS;

function preserve_eol($path, $content) {
    $existing = is_file($path) ? file_get_contents($path) : '';
    $crlf = (strpos($existing, "\r\n") !== false);
    $out = $content;
    if ($crlf) {
        $out = str_replace(["\r\n", "\n"], ["\n", "\r\n"], $out);
    }
    return $out;
}

foreach ($pages as [$bladeRel, $cssRel, $cssDir]) {
    $blade = "$root/$bladeRel";
    $t = file_get_contents($blade);
    if (strpos($t, '<style>') === false) {
        echo "SKIP (no style block): $bladeRel\n";
        continue;
    }
    // Ekstrak isi <style>...</style>
    $m = null;
    if (!preg_match('/<style>(.*?)<\/style>/s', $t, $m)) {
        echo "ERROR parsing style: $bladeRel\n";
        continue;
    }
    $cssBody = $m[1];

    // Konversi url("{{ asset('...') }}") -> url("../../...")
    $cssBody = preg_replace_callback(
        "/url\(\"\{\{\s*asset\('assets\/([^']+)'\)\s*\}\}\"\)/",
        function ($mm) {
            $assetPath = $mm[1];
            // dari public/assets/{auth|sidongan-auth}/css/ ke public/assets/
            return 'url("../../' . $assetPath . '")';
        },
        $cssBody
    );

    $cssContent = $header . "\n" . $cssBody;
    if (substr(trim($cssBody), -1) !== '}') {
        $cssContent .= "\n";
    }
    $cssFile = "$root/$cssRel";
    @mkdir(dirname($cssFile), 0777, true);
    file_put_contents($cssFile, preserve_eol($cssFile, $cssContent));

    // Ganti blok <style> dengan <link>
    $link = "    <link rel=\"stylesheet\" href=\"{{ asset('" . substr($cssRel, 7) . "') }}\">\n";
    $t2 = preg_replace('/<style>.*?<\/style>/s', $link, $t, 1);
    file_put_contents($blade, preserve_eol($blade, $t2));

    // Verifikasi tidak ada sisa Blade di CSS
    $remaining = preg_match('/\{\{/', $cssBody) ? 'BLADE REMAINS' : 'clean';
    echo "$bladeRel -> $cssRel ($remaining)\n";
}
echo "DONE\n";
