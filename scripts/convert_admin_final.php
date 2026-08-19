<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi akhir handler inline admin yang tersisa
 * (pencocokan string eksak, tanpa spasi di sekitar titik PHP).
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

// ---- struktur/create: foto preview openCropModal ----
$f = __DIR__ . '/../resources/views/admin/struktur/create.blade.php';
$t = file_get_contents($f);
$t = str_replace('onclick="openCropModal()"', 'data-action="open-crop"', $t);
file_put_contents($f, $t);
echo 'struktur/create -> onclick: ' . substr_count($t, 'onclick') . "\n";

// ---- user-management/index: PHP-string buttons (tanpa spasi) ----
$f = __DIR__ . '/../resources/views/admin/user-management/index.blade.php';
$t = file_get_contents($f);

$oldTrue = <<<'SRC'
onclick="toggleStatus('.$item->id.', \''.addslashes($item->name).'\', true)"
SRC;
$newTrue = <<<'SRC'
data-toggle-status="1" data-toggle-status-id="'.$item->id.'" data-toggle-status-name="'.addslashes($item->name).'"
SRC;
$t = str_replace($oldTrue, $newTrue, $t);

$oldFalse = <<<'SRC'
onclick="toggleStatus('.$item->id.', \''.addslashes($item->name).'\', false)"
SRC;
$newFalse = <<<'SRC'
data-toggle-status="0" data-toggle-status-id="'.$item->id.'" data-toggle-status-name="'.addslashes($item->name).'"
SRC;
$t = str_replace($oldFalse, $newFalse, $t);

$oldReset = <<<'SRC'
onclick="showResetPasswordModal('.$item->id.', \''.addslashes($item->name).'\')"
SRC;
$newReset = <<<'SRC'
data-reset-password-id="'.$item->id.'" data-reset-password-name="'.addslashes($item->name).'"
SRC;
$t = str_replace($oldReset, $newReset, $t);

$t = str_replace('onclick="closeResetPasswordModal()"', 'data-action="close-reset-password"', $t);
file_put_contents($f, $t);
echo 'user-management/index -> onclick: ' . substr_count($t, 'onclick')
    . ', data-toggle-status: ' . substr_count($t, 'data-toggle-status')
    . ', data-reset-password-id: ' . substr_count($t, 'data-reset-password-id') . "\n";

// ---- sidongan-data/show: hapus onsubmit, beri id ----
$f = __DIR__ . '/../resources/views/admin/sidongan-data/show.blade.php';
$t = file_get_contents($f);
$t = preg_replace('/\s+onsubmit="event\.preventDefault\(\); const f = this; Toast\.confirm\([^"]*\); return false;"/', ' id="deleteReportForm"', $t);
file_put_contents($f, $t);
echo 'sidongan-data/show -> onsubmit: ' . substr_count($t, 'onsubmit') . ', id: ' . substr_count($t, 'id="deleteReportForm"') . "\n";
