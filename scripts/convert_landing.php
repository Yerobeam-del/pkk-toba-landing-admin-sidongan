<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi handler inline di view landing ke atribut data-*
 * (diproses oleh assets/landing/js/delegation.js).
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

function patch($rel, array $pairs) {
    $path = __DIR__ . '/../' . $rel;
    $t = file_get_contents($path);
    $before = $t;
    foreach ($pairs as [$old, $new]) {
        $t = str_replace($old, $new, $t);
    }
    if ($t !== $before) {
        file_put_contents($path, $t);
    }
    echo $rel . ' -> onclick: ' . substr_count($t, 'onclick')
        . ', onchange: ' . substr_count($t, 'onchange')
        . ', onmouseover: ' . substr_count($t, 'onmouseover') . "\n";
}

// Navigasi umum
$navPairs = [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    ["onclick=\"navigateTo('struktur'); return false;\"", 'data-nav="struktur"'],
    ["onclick=\"navigateTo('aplikasi'); return false;\"", 'data-nav="aplikasi"'],
    ["onclick=\"navigateTo('berita'); return false;\"", 'data-nav="berita"'],
    ["onclick=\"navigateTo('sk'); return false;\"", 'data-nav="sk"'],
    ["onclick=\"navigateTo('template'); return false;\"", 'data-nav="template"'],
    ["onclick=\"navigateTo('beranda'); return false;\"", 'data-nav="beranda"'],
    ["onclick=\"if(typeof navigateTo==='function')navigateTo('beranda'); return false;\"", 'data-nav="beranda"'],
];

// ---- news-detail ----
patch('resources/views/modules/landing/news-detail.blade.php', [[
    "onclick=\"window.location.href='{{ url('/#berita') }}'; return false;\"",
    '',
]]);

// ---- floating-btn ----
patch('resources/views/modules/landing/partials/floating-btn.blade.php', [[
    'onclick="toggleFloatingMenu(event)"',
    'data-action="toggle-floating"',
]]);

// ---- hero ----
patch('resources/views/modules/landing/sections/hero.blade.php', [[
    'onclick="scrollToQuickAccess(); return false;"',
    'data-action="scroll-quick-access"',
]]);

// ---- news-home (hover) ----
patch('resources/views/modules/landing/sections/news-home.blade.php', [
    ["onmouseover=\"this.style.transform='translateY(-5px)'\"", ''],
    ["onmouseout=\"this.style.transform='translateY(0)'\"", ''],
]);

// ---- page-aplikasi ----
patch('resources/views/modules/landing/sections/page-aplikasi.blade.php', [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    ["onclick=\"scrollCarousel('track-aplikasi', -1)\"", 'data-carousel="track-aplikasi" data-dir="-1"'],
    ["onclick=\"scrollCarousel('track-aplikasi', 1)\"", 'data-carousel="track-aplikasi" data-dir="1"'],
    ["onclick=\"openModal('modal-aplikasi')\"", 'data-open-modal="modal-aplikasi"'],
    ["onclick=\"scrollCarousel('track-layanan', -1)\"", 'data-carousel="track-layanan" data-dir="-1"'],
    ["onclick=\"scrollCarousel('track-layanan', 1)\"", 'data-carousel="track-layanan" data-dir="1"'],
    ["onclick=\"openModal('modal-layanan')\"", 'data-open-modal="modal-layanan"'],
    ["onclick=\"closeModalOutside(event, 'modal-aplikasi')\"", 'data-close-outside="modal-aplikasi"'],
    ["onclick=\"closeModal('modal-aplikasi')\"", 'data-close-modal="modal-aplikasi"'],
    ["onclick=\"closeModalOutside(event, 'modal-layanan')\"", 'data-close-outside="modal-layanan"'],
    ["onclick=\"closeModal('modal-layanan')\"", 'data-close-modal="modal-layanan"'],
]);

// ---- page-berita ----
patch('resources/views/modules/landing/sections/page-berita.blade.php', [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    ['onchange="changeNewsSort(this.value)"', ''],
    ['onchange="changeNewsPerPage(this.value)"', ''],
    ["onclick=\"changeNewsPage('prev')\"", 'data-news-page="prev"'],
    ["onclick=\"changeNewsPage('next')\"", 'data-news-page="next"'],
]);

// ---- page-desa ----
patch('resources/views/modules/landing/sections/page-desa.blade.php', [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    [
        "onmouseover=\"this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 25px rgba(15,107,99,0.4)'\"",
        '',
    ],
    [
        "onmouseout=\"this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(15,107,99,0.3)'\"",
        '',
    ],
]);

// ---- page-sk ----
patch('resources/views/modules/landing/sections/page-sk.blade.php', [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    ['onchange="changePerPage(this.value)"', ''],
    ["onclick=\"changeSKPage('prev')\"", 'data-sk-page="prev"'],
    ["onclick=\"changeSKPage('next')\"", 'data-sk-page="next"'],
    ["onclick=\"if(typeof navigateTo==='function')navigateTo('beranda'); return false;\"", 'data-nav="beranda"'],
    ['onclick="clearSearchAndShowAll()"', 'data-action="clear-search-sk"'],
]);

// ---- page-struktur ----
patch('resources/views/modules/landing/sections/page-struktur.blade.php', [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    ["onclick=\"togglePokja('{{ \$pokja['id'] }}')\"", 'data-pokja="{{ $pokja[\'id\'] }}"'],
]);

// ---- page-template ----
patch('resources/views/modules/landing/sections/page-template.blade.php', [
    ["onclick=\"navigateTo('beranda')\"", 'data-nav="beranda"'],
    ['onchange="changeTemplatePerPage(this.value)"', ''],
    ["onclick=\"changeTemplatePage('prev')\"", 'data-template-page="prev"'],
    ["onclick=\"changeTemplatePage('next')\"", 'data-template-page="next"'],
    ['onclick="clearTemplateSearchAndShowAll()"', 'data-action="clear-search-template"'],
    ['onclick="closeTemplatePreview()"', 'data-close-template-preview="1"'],
]);

// ---- page-tentang ----
patch('resources/views/modules/landing/sections/page-tentang.blade.php', [[
    "onclick=\"navigateTo('beranda'); return false;\"",
    'data-nav="beranda"',
]]);

// ---- quick-access ----
patch('resources/views/modules/landing/sections/quick-access.blade.php', $navPairs);
