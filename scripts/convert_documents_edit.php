<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi handler inline yang tersisa di
 *   - resources/views/sidongan/documents/edit.blade.php
 *   - resources/views/sidongan/lapor-kegiatan/index.blade.php
 * ke atribut data-* + CSS :focus/:hover.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

// ============ documents/edit ============
$f = __DIR__ . '/../resources/views/sidongan/documents/edit.blade.php';
$t = file_get_contents($f);
$before = $t;

// onfocus (tanpa Blade)
$t = preg_replace("/\s+onfocus=\"this\.style\.borderColor='[^']*'; this\.style\.boxShadow='[^']*'\"/", '', $t);
// onblur (mungkin berisi Blade {{ }} dengan tanda kutip)
$t = preg_replace("/\s+onblur=\"this\.style\.borderColor='(?:\{\{[^}]*\}\}|[^'])*'; this\.style\.boxShadow='[^']*'\"/", '', $t);

// onchange & ondblclick
$t = preg_replace('/\s+onchange="validateDates\(\); updateAgendaPreviewEdit\(\)"/', '', $t);
$t = preg_replace('/\s+onchange="updateAgendaPreviewEdit\(\)"/', '', $t);
$t = preg_replace("/\s+ondblclick=\"enableEditAgenda\(this\)\"/", '', $t);

// onclick
$t = str_replace('onclick="confirmDeleteFile(event)"', 'data-action="confirm-delete-file"', $t);
$t = str_replace('onclick="changeFile()"', 'data-action="change-file"', $t);
$t = str_replace('onclick="return validateForm()"', '', $t);

// onmouseover/onmouseout
$t = preg_replace('/\s+onmouseover="this\.style\.[^"]*"/', '', $t);
$t = preg_replace('/\s+onmouseout="this\.style\.[^"]*"/', '', $t);

// sd-btn-back header: pindahkan style inline ke kelas
$t = preg_replace(
    '/class="sd-btn-back" style="display: inline-flex; align-items: center; gap: 0\.5rem; padding: 0\.75rem 1\.25rem; background: rgba\(255,255,255,0\.25\); color: white; text-decoration: none; border-radius: 0\.5rem; font-weight: 600; transition: all 0\.25s ease; backdrop-filter: blur\(4px\); border: 1px solid rgba\(255, 255, 255, 0\.3\);"/',
    'class="sd-btn-back"',
    $t
);

// Tombol lihat file
$t = preg_replace(
    '/<a href="\{\{ asset\(\x27storage\/\x27 \. \$document->file_path\) \}\}" target="_blank" style="display: inline-flex; align-items: center; gap: 0\.3rem; padding: 0\.4rem 0\.8rem; background: #dbeafe; color: #2563eb; text-decoration: none; border-radius: 0\.375rem; font-size: 0\.75rem; font-weight: 600;">/',
    '<a href="{{ asset(\'storage/\' . $document->file_path) }}" target="_blank" class="sd-file-view">',
    $t
);
// Tombol hapus file
$t = preg_replace(
    '/<button type="button" onclick="confirmDeleteFile\(event\)" style="display: inline-flex; align-items: center; gap: 0\.3rem; padding: 0\.4rem 0\.8rem; background: #fee2e2; color: #ef4444; border: none; border-radius: 0\.375rem; font-size: 0\.75rem; font-weight: 600; cursor: pointer;">/',
    '<button type="button" data-action="confirm-delete-file" class="sd-file-delete">',
    $t
);
// Tombol ganti file
$t = preg_replace(
    '/<button type="button" onclick="changeFile\(\)" style="margin-top: 0\.5rem; padding: 0\.4rem 0\.8rem; background: white; border: 1px solid #e2e8f0; color: #64748b; border-radius: 0\.375rem; font-size: 0\.75rem; cursor: pointer;">/',
    '<button type="button" data-action="change-file" class="sd-file-change">',
    $t
);
// Tombol batal
$t = preg_replace(
    '/<a href="\{\{ route\(\x27sidongan\.documents\.index\x27\) \}\}" style="padding: 0\.75rem 1\.5rem; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 0\.5rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0\.2s; display: inline-flex; align-items: center; gap: 0\.5rem;">/',
    '<a href="{{ route(\'sidongan.documents.index\') }}" class="sd-btn-cancel-edit">',
    $t
);
// Tombol submit
$t = preg_replace(
    '/<button type="submit" style="display: inline-flex; align-items: center; gap: 0\.5rem; padding: 0\.75rem 1\.5rem; background: linear-gradient\(135deg, #3b82f6, #2563eb\); color: white; border: none; border-radius: 0\.5rem; font-weight: 600; cursor: pointer; transition: all 0\.2s;">/',
    '<button type="submit" class="sd-btn-submit-edit">',
    $t
);

if ($t !== $before) {
    file_put_contents($f, $t);
}
echo "documents/edit -> onclick: " . substr_count($t, 'onclick')
    . ", onfocus: " . substr_count($t, 'onfocus')
    . ", onmouseover: " . substr_count($t, 'onmouseover') . "\n";

// ============ lapor-kegiatan/index ============
$f = __DIR__ . '/../resources/views/sidongan/lapor-kegiatan/index.blade.php';
$t = file_get_contents($f);
$before = $t;

// onfocus/onblur #0ea5e9
$t = preg_replace("/\s+onfocus=\"this\.style\.borderColor='[^']*'; this\.style\.boxShadow='[^']*'\"/", '', $t);
$t = preg_replace("/\s+onblur=\"this\.style\.borderColor='[^']*'; this\.style\.boxShadow='[^']*'\"/", '', $t);
// onchange auto-submit filterForm
$t = preg_replace("/\s+onchange=\"document\.getElementById\('filterForm'\)\.submit\(\)\"/", '', $t);

// Kartu laporan: ganti hover JS dgn custom property CSS
$t = preg_replace(
    '/\s+onmouseover="this\.style\.background=\x27\{\{ \$theme\[\x27bg\x27\] \}\}\x27; this\.style\.borderLeftColor=\x27\{\{ \$theme\[\x27btn\x27\] \}\}\x27; this\.style\.transform=\x27translateX\(4px\)\x27; this\.style\.boxShadow=\x270 4px 12px rgba\(0,0,0,0\.1\)\x27"/',
    '',
    $t
);
$t = preg_replace(
    '/\s+onmouseout="this\.style\.background=\x27white\x27; this\.style\.borderLeftColor=\x27transparent\x27; this\.style\.transform=\x27translateX\(0\)\x27; this\.style\.boxShadow=\x27none\x27"/',
    '',
    $t
);
// custom properties pada kartu
$t = preg_replace(
    '/(class="laporan-item animate-slide-in" \s*style="padding: 1\.5rem 1\.75rem; border-bottom: \{\{ \$loop->last \? \x27none\x27 : \x271px solid #f3f4f6\x27 \}\}; \s*transition: all 0\.3s cubic-bezier\(0\.4, 0, 0\.2, 1\);\s*border-left: 3px solid transparent;\s*position: relative;\s*overflow: hidden;")/',
    '$1 --lk-bg: {{ $theme[\'bg\'] }}; --lk-border: {{ $theme[\'btn\'] }};',
    $t
);

// Pagination & tombol hover
$t = preg_replace('/\s+onmouseover="this\.style\.[^"]*"/', '', $t);
$t = preg_replace('/\s+onmouseout="this\.style\.[^"]*"/', '', $t);

// Pagination classes
$t = str_replace(
    'style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"',
    'class="sd-page-btn sd-page-btn-white"',
    $t
);
$t = str_replace(
    'style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #0ea5e9; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"',
    'class="sd-page-btn sd-page-btn-sky"',
    $t
);
$t = str_replace(
    '<span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #0ea5e9; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">',
    '<span class="sd-page-btn sd-page-btn-sky">',
    $t
);

if ($t !== $before) {
    file_put_contents($f, $t);
}
echo "lapor-kegiatan/index -> onclick: " . substr_count($t, 'onclick')
    . ", onchange: " . substr_count($t, 'onchange')
    . ", onfocus: " . substr_count($t, 'onfocus')
    . ", onmouseover: " . substr_count($t, 'onmouseover')
    . ", --lk-bg: " . substr_count($t, '--lk-bg') . "\n";
