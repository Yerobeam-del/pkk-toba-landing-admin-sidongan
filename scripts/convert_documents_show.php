<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi satu kali: pindahkan skrip inline + handler pada
 * resources/views/sidongan/documents/show.blade.php ke
 * berkas eksternal + atribut data-*.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$f = __DIR__ . '/../resources/views/sidongan/documents/show.blade.php';
$t = file_get_contents($f);
$before = $t;

// 1. Tangkap blok @php ... @endphp di dalam <script>
if (!preg_match('/@php\r?\n(.*?)@endphp/s', $t, $m)) {
    echo "Blok @php tidak ditemukan!\n";
    exit(1);
}
$phpBlock = "@php\n" . $m[1] . "@endphp\n";

// 2. Ganti blok <script> dengan @push include
$pattern = "/\r?\n<script>\r?\n.*?<\/script>\r?\n/s";
$t = preg_replace($pattern, "\r\n@push('scripts')\r\n    <script src=\"{{ asset('assets/sidongan/js/documents-show.js') }}\"></script>\r\n@endpush\r\n", $t, 1, $count);
if ($count !== 1) {
    echo "Blok script tidak ditemukan!\n";
    exit(1);
}

// 3. Overlay: sisipkan blok @php + data atribut
$overlayOld = '<div id="galleryOverlay" class="dl-gallery-overlay" onclick="closeGallery(event)">';
$overlayNew = $phpBlock .
    '<div id="galleryOverlay" class="dl-gallery-overlay"' . "\r\n" .
    '     data-fotos=\'{{ json_encode($document->file_path ? [$document->file_path] : []) }}\'' . "\r\n" .
    '     data-report-fotos=\'{{ json_encode($allReportFotos) }}\'' . "\r\n" .
    '     data-storage="{{ asset(\'storage\') }}">';
$t = str_replace($overlayOld, $overlayNew, $t);

// 4. Foto item laporan -> data-report-id / data-index
$t = preg_replace(
    '/onclick="openReportGallery\(\{\{ \$report->id \}\}, \{\{ \$index \}\}\)"/',
    'data-report-id="{{ $report->id }}" data-index="{{ $index }}"',
    $t
);

// 5. Handler galeri lain
$t = str_replace('onclick="closeGallery()"', '', $t);
$t = str_replace('onclick="event.stopPropagation()"', '', $t);
$t = str_replace('onclick="navigateGallery(-1)"', '', $t);
$t = str_replace('onclick="navigateGallery(1)"', '', $t);

if ($t === $before) {
    echo "Tidak ada perubahan!\n";
    exit(1);
}

file_put_contents($f, $t);
echo "Selesai. onclick: " . substr_count($t, 'onclick') . "\n";
