<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Konversi handler inline pada resources/views/admin/partials/table.blade.php
 * ke atribut data-* + kelas CSS.
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

$f = __DIR__ . '/../resources/views/admin/partials/table.blade.php';
$t = file_get_contents($f);
$before = $t;

// ---- 1. Tombol hapus (desktop & mobile) -> data attributes ----
$t = preg_replace(
    "/onclick=\"confirmDeleteItem\(\{\{ \\\$item->id \}\}, \x27\{\{ addslashes\(data_get\(\\\$item, \x27name\x27\)\) \}\}\x27\)\"/",
    'data-delete-item="{{ $item->id }}" data-delete-title="{{ addslashes(data_get($item, \'name\')) }}"',
    $t
);
$t = preg_replace(
    "/onclick=\"confirmDeleteItem\(\{\{ \\\$item->id \}\}, \x27\{\{ addslashes\(data_get\(\\\$item, \x27name\x27\) \?\? data_get\(\\\$item, \x27title\x27\) \?\? data_get\(\\\$item, \x27subject\x27\)\) \}\}\x27\)\"/",
    'data-delete-item="{{ $item->id }}" data-delete-title="{{ addslashes(data_get($item, \'name\') ?? data_get($item, \'title\') ?? data_get($item, \'subject\')) }}"',
    $t
);

// ---- 2. Tombol aksi desktop: inline style -> kelas ----
$actionStyle = 'style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; transition: all 0.2s; cursor: pointer;"';
$t = str_replace($actionStyle, '', $t);
$t = str_replace('class="action-btn"', 'class="action-btn"', $t); // keep

// ---- 3. Tombol hapus desktop: inline style -> kelas ----
$t = str_replace(
    'style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s;"',
    '',
    $t
);

// ---- 4. Tombol aksi mobile: hapus style flex ----
$t = str_replace(
    'style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;border:none;cursor:pointer;transition:all 0.2s"',
    '',
    $t
);

// ---- 5. Pagination: inline style -> kelas sd-paginate ----
$t = preg_replace(
    '/style="padding:0\.5rem 0\.9rem;background:#fff;color:var\(--text-dark\);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0\.875rem;font-weight:500;min-width:40px;display:inline-flex;align-items:center;justify-content:center;gap:0\.25rem;transition:all 0\.2s"/',
    'class="sd-paginate"',
    $t
);
$t = preg_replace(
    '/style="padding:0\.5rem 0\.7rem;background:#fff;color:var\(--text-dark\);border:1px solid #e2e8f0;border-radius:8px;text-decoration:none;font-size:0\.8rem;min-width:36px;display:inline-flex;align-items:center;justify-content:center;transition:all 0\.2s"/',
    'class="sd-paginate"',
    $t
);

// ---- 6. Hapus semua onmouseover/onmouseout ----
$t = preg_replace('/\s+onmouseover="this\.style\.[^"]*"/', '', $t);
$t = preg_replace('/\s+onmouseout="this\.style\.[^"]*"/', '', $t);

if ($t !== $before) {
    file_put_contents($f, $t);
}
echo "table.blade -> onclick: " . substr_count($t, 'onclick')
    . ", onmouseover: " . substr_count($t, 'onmouseover')
    . ", sd-paginate: " . substr_count($t, 'sd-paginate')
    . ", data-delete-item: " . substr_count($t, 'data-delete-item') . "\n";
