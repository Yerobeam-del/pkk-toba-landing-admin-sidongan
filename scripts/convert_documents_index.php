<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi satu kali: pindahkan handler inline pada
 * resources/views/sidongan/documents/index.blade.php ke
 * atribut data-* (diproses oleh assets/sidongan/js/documents-index.js).
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$f = __DIR__ . '/../resources/views/sidongan/documents/index.blade.php';
$t = file_get_contents($f);
$before = $t;

// 1. deleteForm / archiveForm: tambahkan data-base-url
$t = str_replace(
    '<form class="u-hidden" id="deleteForm" method="POST">',
    '<form class="u-hidden" id="deleteForm" method="POST" data-base-url="{{ url(\'/sidongan/documents\') }}">',
    $t
);
$t = str_replace(
    '<form class="u-hidden" id="archiveForm" method="POST">',
    '<form class="u-hidden" id="archiveForm" method="POST" data-base-url="{{ url(\'/sidongan/documents\') }}">',
    $t
);

// 2. filterForm: data-base-url
$t = preg_replace_callback(
    '/(<form id="filterForm"[^>]*?)(>)/',
    function ($m) {
        if (str_contains($m[1], 'data-base-url')) {
            return $m[0];
        }
        return $m[1] . ' data-base-url="{{ route(\'sidongan.documents.index\') }}"' . $m[2];
    },
    $t
);

// 3. onchange auto-submit (select & date) -> hapus
$t = preg_replace(
    '/\s+onchange="document\.getElementById\(\'filterForm\'\)\.submit\(\)"/',
    '',
    $t
);

// 4. Tombol filter cepat -> data-filter-status
$t = preg_replace(
    '/onclick="applyQuickFilter\(\'([a-z_]+)\'\)"/',
    'data-filter-status="$1"',
    $t
);

// 5. Tombol reset -> data-action
$t = str_replace('onclick="resetFilters()"', 'data-action="reset-filters"', $t);
$t = str_replace('onclick="resetSorting()"', 'data-action="reset-sorting"', $t);

// 6. Sortable th -> data-sort-url
$t = preg_replace(
    '/onclick="window\.location\.href=\'(\{\{[^}]*\}\})\'"/',
    'data-sort-url="$1"',
    $t
);

// 7. Tombol hapus -> data-delete-id / data-delete-title
$t = preg_replace(
    '/onclick="confirmDelete\(\{\{ \$doc->id \}\}, \'\{\{ addslashes\(\$doc->subject \?\? \$doc->title\) \}\}\'\)"/',
    'data-delete-id="{{ $doc->id }}" data-delete-title="{{ addslashes($doc->subject ?? $doc->title) }}"',
    $t
);

// 8. Tombol arsipkan -> data-archive-id / data-archive-title
$t = preg_replace(
    '/onclick="confirmArchive\(\{\{ \$doc->id \}\}, \'\{\{ addslashes\(\$doc->subject \?\? \$doc->title\) \}\}\'\)"/',
    'data-archive-id="{{ $doc->id }}" data-archive-title="{{ addslashes($doc->subject ?? $doc->title) }}"',
    $t
);

// 9. onmouseover/onmouseout style hacks -> hapus (CSS :hover menggantikan)
$t = preg_replace(
    '/\s+onmouseover="this\.style\.background=\'[^\']*\'"/',
    '',
    $t
);
$t = preg_replace(
    '/\s+onmouseout="this\.style\.background=\'[^\']*\'"/',
    '',
    $t
);

// 10. Baris tabel: tambah class hover-row, pindahkan transition ke CSS
$t = preg_replace(
    '/<tr style="border-bottom: 1px solid #f1f5f9; transition: background 0\.2s;">/',
    '<tr class="sd-row">',
    $t
);

// 11. Icon buttons: beri kelas khusus warna
$t = str_replace(
    'class="sd-icon-btn"',
    'class="sd-icon-btn sd-icon-view"',
    $t
);
// edit: baris dengan background #fef3c7 -> sd-icon-edit
$t = preg_replace(
    '/(class="sd-icon-btn sd-icon-view"[^>]*?background: #fef3c7;)/',
    'sd-icon-edit $1',
    $t
);
// delete: background #fee2e2 -> sd-icon-delete
$t = preg_replace(
    '/(class="sd-icon-btn sd-icon-view"[^>]*?background: #fee2e2;)/',
    'sd-icon-delete $1',
    $t
);
// archive: background #ede9fe -> sd-icon-archive
$t = preg_replace(
    '/(class="sd-icon-btn sd-icon-view"[^>]*?background: #ede9fe;)/',
    'sd-icon-archive $1',
    $t
);

// 12. Pagination: class sd-page-btn (+ varian warna)
//     Biru: background #3b82f6 (prev/next)
$t = str_replace(
    'background: #3b82f6; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"',
    'background: #3b82f6; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;" class="sd-page-btn sd-page-btn-blue"',
    $t
);
//     Putih: background: white; color: #475569; border: 1px solid #e2e8f0
$t = str_replace(
    'background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"',
    'background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;" class="sd-page-btn sd-page-btn-white"',
    $t
);
//     Span halaman aktif (biru, bukan link)
$t = str_replace(
    '<span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #3b82f6; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">',
    '<span class="sd-page-btn sd-page-btn-blue">',
    $t
);

// 13. Ganti blok <script> inline dengan @push include
$pattern = "/\r?\n<script>\r?\n.*?<\/script>\r?\n/s";
$t = preg_replace($pattern, "\r\n@push('scripts')\r\n    <script src=\"{{ asset('assets/sidongan/js/documents-index.js') }}\"></script>\r\n@endpush\r\n", $t, 1, $count);

if ($t === $before) {
    echo "Tidak ada perubahan!\n";
    exit(1);
}

file_put_contents($f, $t);
echo "Selesai. script diganti: $count | onclick: " . substr_count($t, 'onclick') .
    " | onchange: " . substr_count($t, 'onchange') .
    " | onmouseover: " . substr_count($t, 'onmouseover') .
    " | onmouseout: " . substr_count($t, 'onmouseout') . "\n";
