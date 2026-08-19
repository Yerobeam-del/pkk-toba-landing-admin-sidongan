<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi handler inline (panggilan fungsi) yang tersisa di
 * view admin ke atribut data-* + delegation.
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
        . ', onsubmit: ' . substr_count($t, 'onsubmit')
        . ', onfocus: ' . substr_count($t, 'onfocus') . "\n";
}

// ---- aplikasi partials ----
patch('resources/views/admin/aplikasi/partials/action-buttons.blade.php', [[
    "onclick=\"confirmDeleteApp({{ \$app->id }}, '{{ addslashes(\$app->name) }}')\"",
    'data-delete-app-id="{{ $app->id }}" data-delete-app-name="{{ addslashes($app->name) }}"',
]]);
patch('resources/views/admin/aplikasi/partials/app-card.blade.php', [[
    "onclick=\"confirmDeleteApp({{ \$app->id }}, '{{ addslashes(\$app->name) }}')\"",
    'data-delete-app-id="{{ $app->id }}" data-delete-app-name="{{ addslashes($app->name) }}"',
]]);

// ---- desa/index ----
patch('resources/views/admin/desa/index.blade.php', [[
    'onclick="location.reload()"',
    'data-action="reload-page"',
]]);

// ---- hero-sliders/index ----
patch('resources/views/admin/hero-sliders/index.blade.php', [
    ["onclick=\"editSlider({{ \$slider->id }})\"", 'data-edit-slider="{{ $slider->id }}"'],
    [
        "onclick=\"confirmDeleteWithToast({{ \$slider->id }}, 'Slide #{{ \$slider->id }}')\"",
        'data-delete-slider="{{ $slider->id }}" data-delete-slider-title="Slide #{{ $slider->id }}"',
    ],
    ['onclick="closeEditModal()"', 'data-action="close-edit-modal"'],
]);

// ---- partials/modal ----
patch('resources/views/admin/partials/modal.blade.php', [
    ['onclick="closeModal()"', 'data-action="close-modal"'],
]);

// ---- profile/edit ----
patch('resources/views/admin/profile/edit.blade.php', [
    ["onclick=\"document.getElementById('avatarInput').click()\"", 'data-action="pick-avatar"'],
    ["onclick=\"togglePassword('current_password',this)\"", 'data-action="toggle-password" data-target="current_password"'],
    ["onclick=\"togglePassword('password',this)\"", 'data-action="toggle-password" data-target="password"'],
    ["onclick=\"togglePassword('password_confirmation',this)\"", 'data-action="toggle-password" data-target="password_confirmation"'],
    ['onclick="closeCropper()"', 'data-action="close-cropper"'],
    ['onclick="cropAndSave()"', 'data-action="crop-save"'],
]);

// ---- sk/create & sk/edit ----
patch('resources/views/admin/sk/create.blade.php', [[
    'onclick="clearFile()"',
    'data-action="clear-file"',
]]);
patch('resources/views/admin/sk/edit.blade.php', [[
    'onclick="clearFile()"',
    'data-action="clear-file"',
]]);

// ---- template/create & template/edit ----
patch('resources/views/admin/template/create.blade.php', [[
    'onclick="clearFile()"',
    'data-action="clear-file"',
]]);
patch('resources/views/admin/template/edit.blade.php', [[
    'onclick="clearFile()"',
    'data-action="clear-file"',
]]);

// ---- tentang/index ----
patch('resources/views/admin/tentang/index.blade.php', [
    ['onclick="removeProgram(this)"', 'data-action="remove-program"'],
    ['onclick="addProgram()"', 'data-action="add-program"'],
]);

// ---- struktur/create ----
patch('resources/views/admin/struktur/create.blade.php', [
    ['onchange="updatePositions()"', ''],
    ['onchange="handlePhotoUpload(event)"', ''],
    ['onclick="closeCropModal()"', 'data-action="close-crop"'],
    ['onclick="rotateImage(-90)"', 'data-action="rotate-crop" data-deg="-90"'],
    ['onclick="rotateImage(90)"', 'data-action="rotate-crop" data-deg="90"'],
    ['onclick="resetCrop()"', 'data-action="reset-crop"'],
    ['onclick="applyCrop()"', 'data-action="apply-crop"'],
]);

// ---- struktur/edit ----
patch('resources/views/admin/struktur/edit.blade.php', [
    ['onchange="updatePositions()"', ''],
    ['onchange="handlePhotoUpload(event)"', ''],
    ['onclick="closeCropModal()"', 'data-action="close-crop"'],
    ['onclick="rotateImage(-90)"', 'data-action="rotate-crop" data-deg="-90"'],
    ['onclick="rotateImage(90)"', 'data-action="rotate-crop" data-deg="90"'],
    ['onclick="resetCrop()"', 'data-action="reset-crop"'],
    ['onclick="applyCrop()"', 'data-action="apply-crop"'],
    ['onclick="removePhoto()"', 'data-action="remove-photo"'],
    ['onclick="openCropModal()"', 'data-action="open-crop"'],
]);

// ---- user-management/create & edit ----
patch('resources/views/admin/user-management/create.blade.php', [
    ['onchange="togglePermissionSection()"', ''],
]);
patch('resources/views/admin/user-management/edit.blade.php', [
    ['onchange="togglePermissionSection()"', ''],
]);

// ---- user-management/show ----
patch('resources/views/admin/user-management/show.blade.php', [[
    "onclick=\"showResetPasswordModal({{ \$user->id }}, '{{ addslashes(\$user->name) }}')\"",
    'data-reset-password-id="{{ $user->id }}" data-reset-password-name="{{ addslashes($user->name) }}"',
]]);

// ---- user-management/index (PHP-string buttons) ----
// Teks sumber di blade: onclick="toggleStatus('.$item->id.', \''.addslashes($item->name).'\', true)"
$f = __DIR__ . '/../resources/views/admin/user-management/index.blade.php';
$t = file_get_contents($f);
$oldStatusTrue = "onclick=\"toggleStatus(' . \$item->id . ', \\'' . addslashes(\$item->name) . '\\', true)\"";
$newStatusTrue = "data-toggle-status=\"1\" data-toggle-status-id=\"' . \$item->id . '\" data-toggle-status-name=\"' . addslashes(\$item->name) . '\"";
$t = str_replace($oldStatusTrue, $newStatusTrue, $t);
$oldStatusFalse = "onclick=\"toggleStatus(' . \$item->id . ', \\'' . addslashes(\$item->name) . '\\', false)\"";
$newStatusFalse = "data-toggle-status=\"0\" data-toggle-status-id=\"' . \$item->id . '\" data-toggle-status-name=\"' . addslashes(\$item->name) . '\"";
$t = str_replace($oldStatusFalse, $newStatusFalse, $t);
$oldReset = "onclick=\"showResetPasswordModal(' . \$item->id . ', \\'' . addslashes(\$item->name) . '\\')\"";
$newReset = "data-reset-password-id=\"' . \$item->id . '\" data-reset-password-name=\"' . addslashes(\$item->name) . '\"";
$t = str_replace($oldReset, $newReset, $t);
$t = str_replace('onclick="closeResetPasswordModal()"', 'data-action="close-reset-password"', $t);
file_put_contents($f, $t);
echo 'user-management/index -> onclick: ' . substr_count($t, 'onclick')
    . ', data-toggle-status: ' . substr_count($t, 'data-toggle-status')
    . ', data-reset-password: ' . substr_count($t, 'data-reset-password-id') . "\n";

// ---- sieda-data/module (PHP-string button) ----
// Teks sumber: onclick="confirmDeleteItem(\''.$id.'\', \''.addslashes($name).'\')"
$f = __DIR__ . '/../resources/views/admin/sieda-data/module.blade.php';
$t = file_get_contents($f);
$oldDel = "onclick=\"confirmDeleteItem(\\'' . \$id . '\\', \\'' . addslashes(\$name) . '\\')\"";
$newDel = "data-delete-item=\"' . \$id . '\" data-delete-title=\"' . addslashes(\$name) . '\"";
$t = str_replace($oldDel, $newDel, $t);
file_put_contents($f, $t);
echo 'sieda-data/module -> onclick: ' . substr_count($t, 'onclick') . "\n";

// ---- sidongan-data/show (onsubmit Toast.confirm) ----
$f = __DIR__ . '/../resources/views/admin/sidongan-data/show.blade.php';
$t = file_get_contents($f);
$t = preg_replace(
    '/(<form[^>]*method="POST"[^>]*?) onsubmit="event\.preventDefault\(\); const f = this; Toast\.confirm\([^"]*"\)\.then\(function \(setuju\) \{ if \(setuju\) f\.submit\(\); \}\); return false;"/',
    '$1 id="deleteReportForm"',
    $t
);
file_put_contents($f, $t);
echo 'sidongan-data/show -> onsubmit: ' . substr_count($t, 'onsubmit') . "\n";
