<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi handler inline yang tersisa di view SIDONGAN
 * (top-header, sidebar, notifications, notification-item,
 * disposisi/form) ke atribut data-* + delegation.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

function patch($rel, array $replacements) {
    $path = __DIR__ . '/../' . $rel;
    $t = file_get_contents($path);
    $before = $t;
    foreach ($replacements as $pair) {
        [$old, $new] = $pair;
        $t = str_replace($old, $new, $t);
    }
    if ($t !== $before) {
        file_put_contents($path, $t);
    }
    echo $rel . ' -> onclick: ' . substr_count($t, 'onclick')
        . ', onchange: ' . substr_count($t, 'onchange') . "\n";
}

// ---- top-header ----
patch('resources/views/sidongan/partials/top-header.blade.php', [
    ['onclick="toggleNotificationPopup()"', 'data-action="toggle-notification-popup"'],
    ['onclick="toggleUserMenu()"', 'data-action="toggle-user-menu"'],
    ['onclick="markAllAsRead()"', 'data-action="mark-all-read"'],
    [
        "onclick=\"markNotificationReadAndRedirect({{ \$notif->id }}, '{{ route('sidongan.documents.show', \$notif->related_id) }}')\"",
        'data-notif-id="{{ $notif->id }}" data-notif-url="{{ route(\'sidongan.documents.show\', $notif->related_id) }}"',
    ],
]);

// ---- sidebar ----
patch('resources/views/sidongan/partials/sidebar.blade.php', [
    ['onclick="toggleSuratMenu(event)"', 'data-action="toggle-surat-menu"'],
]);

// ---- notifications/index ----
patch('resources/views/sidongan/notifications/index.blade.php', [
    ['onclick="hapusSemuaNotifikasi()"', 'data-action="hapus-semua-notifikasi"'],
    [
        "onclick=\"markAsRead({{ \$notification->id }}, this)\"",
        'data-notif-id="{{ $notification->id }}"',
    ],
]);

// ---- notification-item ----
patch('resources/views/sidongan/dashboard/components/notification-item.blade.php', [
    [
        "onclick=\"markNotificationReadAndRedirect({{ \$notif->id }}, '{{ route('sidongan.documents.show', \$notif->related_id) }}')\"",
        'data-notif-id="{{ $notif->id }}" data-notif-url="{{ route(\'sidongan.documents.show\', $notif->related_id) }}"',
    ],
]);

// ---- disposisi/form onchange ----
patch('resources/views/sidongan/disposisi/form.blade.php', [
    ['onchange="toggleRoleStyle(this)"', ''],
    ['onchange="toggleCustomAction()"', ''],
]);
