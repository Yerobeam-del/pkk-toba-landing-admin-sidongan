<?php

/*
 * ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Skrip pemeliharaan: memindahkan style inline yang berulang
 * ke berkas CSS utilitas (public/assets/shared/css/utilities.css)
 * lalu mengganti style="..." pada Blade dengan class="...".
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 */

// Nama kelas yang dipilih manual untuk pola paling umum
// (kunci = isi atribut style persis seperti di markup).
$named = [
    'font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem' => 'u-label',
    'display: block; font-size: 0.875rem; font-weight: 600; color: #334155; margin-bottom: 0.5rem;' => 'u-label-slate',
    'display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;' => 'u-label-gray',
    'display:block;margin-bottom:0.5rem;font-weight:600' => 'u-label-simple',
    'font-weight:600;display:block;margin-bottom:0.5rem' => 'u-label-simple',
    'font-weight:600;display:block;margin-bottom:0.5rem;font-size:0.9rem;color:#334155' => 'u-label-slate',
    'display:block;font-weight:600;margin-bottom:0.5rem;color:var(--text-dark)' => 'u-label-dark',
    'font-weight:600' => 'u-semibold',
    'flex:1' => 'u-flex-1',
    'flex: 1;' => 'u-flex-1',
    'flex:1;min-width:0' => 'u-flex-1-min',
    'flex: 1; min-width: 0;' => 'u-flex-1-min',
    'flex:1;min-width:200px' => 'u-flex-1-min-200',
    'flex-shrink: 0;' => 'u-shrink-0',
    'color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem' => 'u-hint',
    'color:var(--text-muted);display:block;margin-top:0.5rem;font-size:0.85rem' => 'u-hint-sm',
    'color:var(--text-muted);display:block;margin-top:0.4rem;font-size:0.8rem;line-height:1.5' => 'u-hint-line',
    'color:var(--text-muted);display:block;margin-top:0.25rem' => 'u-hint-tight',
    'color:var(--text-muted);margin:0;font-size:0.9rem' => 'u-muted',
    'color:var(--text-muted)' => 'u-muted-plain',
    'display:none' => 'u-hidden',
    'display: none;' => 'u-hidden',
    'color: #ef4444;' => 'u-text-danger',
    'color:#ef4444' => 'u-text-danger',
    'color: #ef4444; font-size: 1.25rem;' => 'u-text-danger-lg',
    'font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem;' => 'u-error-text',
    'color:#ef4444;display:block;margin-top:0.3rem;font-size:0.8rem' => 'u-error-block',
    'color: #dc2626; font-size: 0.875rem;' => 'u-error-msg',
    'font-size: 0.8rem; color: #991b1b; font-weight: 500;' => 'u-text-danger-soft',
    'margin-bottom:1.5rem' => 'u-mb-6',
    'margin-bottom: 1.5rem;' => 'u-mb-6',
    'margin-bottom:2rem' => 'u-mb-8',
    'margin-bottom: 2rem;' => 'u-mb-8',
    'margin-bottom:1rem' => 'u-mb-4',
    'margin-bottom: 1.25rem;' => 'u-mb-5',
    'margin-bottom:0.25rem' => 'u-mb-1',
    'margin-bottom: 0.75rem;' => 'u-mb-3',
    'margin: 0;' => 'u-m-0',
    'position:relative' => 'u-relative',
    'position: relative;' => 'u-relative',
    'position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none' => 'u-select-chevron',
    'position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; font-size: 0.75rem;' => 'u-select-chevron-right',
    'position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);text-decoration:none' => 'u-position-right',
    'position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted)' => 'u-position-left',
    'position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);' => 'u-deco-circle-tr',
    'position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;' => 'u-deco-circle-bl',
    'font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0' => 'u-subtitle',
    'font-size: 0.875rem; opacity: 0.95; margin: 0 0 0.25rem 0; font-weight: 500;' => 'u-subtitle-sm',
    'font-size: 0.875rem; opacity: 0.95; margin: 0;' => 'u-subtitle-flat',
    'font-size:1.85rem;font-weight:800;margin:0;line-height:1.1' => 'u-h1-hero',
    'font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0' => 'u-page-title',
    'font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px' => 'u-page-title-tight',
    'display:flex;align-items:flex-start;gap:1rem' => 'u-flex-start-gap-4',
    'display:flex;align-items:center;gap:0.5rem' => 'u-flex-center-gap-2',
    'display: flex; align-items: center; gap: 0.5rem;' => 'u-flex-center-gap-2',
    'display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;' => 'u-flex-center-gap-4-rel',
    'display:flex;align-items:center;gap:0.75rem' => 'u-flex-center-gap-3',
    'display: flex; align-items: center; gap: 0.75rem;' => 'u-flex-center-gap-3',
    'display: flex; align-items: center; gap: 1rem;' => 'u-flex-center-gap-4',
    'display:flex;align-items:center;gap:1rem' => 'u-flex-center-gap-4',
    'display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;' => 'u-flex-center-gap-2-wrap',
    'display:flex;align-items:center;gap:0.5rem;flex-shrink:0' => 'u-flex-center-gap-2-shrink',
    'display:inline-flex;align-items:center;gap:0.5rem' => 'u-inline-flex-center-gap-2',
    'display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0' => 'u-inline-flex-gap-2-nowrap',
    'display:inline-flex;align-items:center;gap:0.4rem;font-weight:500;font-size:0.9rem' => 'u-inline-flex-gap-2-semibold',
    'display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem' => 'u-grid-2',
    'display:grid;grid-template-columns:1fr 1fr;gap:1.5rem' => 'u-grid-2-plain',
    'display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;' => 'u-grid-2-gap-4',
    'display:grid;gap:1.5rem' => 'u-grid-gap-6',
    'display:grid;gap:0.75rem' => 'u-grid-gap-3',
    'display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem' => 'u-header-row',
    'display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap' => 'u-header-row-wrap',
    'display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem' => 'u-header-row-plain',
    'display: flex; justify-content: space-between; padding-bottom: 0.75rem; border-bottom: 1px solid #f1f5f9;' => 'u-row-divider',
    'display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border)' => 'u-row-divider-border',
    'display:flex;gap:0.75rem;justify-content:flex-end;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.04)' => 'u-form-actions',
    'width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0' => 'u-icon-badge',
    'width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);' => 'u-icon-badge-lg',
    'width: 4rem; height: 4rem; background: rgba(255,255,255,0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);' => 'u-icon-badge-md',
    'width: 3rem; height: 3rem; background: rgba(255, 255, 255, 0.25); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);' => 'u-icon-badge-sm',
    'width:40px;height:40px;background:rgba(255,255,255,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0' => 'u-icon-badge-square',
    'width:14px;height:14px' => 'u-icon-sm',
    'width:18px;height:18px;cursor:pointer' => 'u-icon-btn-sm',
    'font-size: 0.85rem; color: #64748b;' => 'u-text-sm-muted',
    'font-size: 0.875rem; color: #64748b;' => 'u-text-sm-muted-2',
    'font-size:0.85rem;color:var(--text-muted)' => 'u-text-muted-sm',
    'font-size:0.75rem;color:var(--text-muted)' => 'u-text-muted-xs',
    'font-size:0.8rem;color:var(--text-muted)' => 'u-text-muted-xs2',
    'font-size: 0.875rem; color: #94a3b8;' => 'u-text-xs-muted',
    'font-size: 0.75rem; color: #64748b; margin: 0;' => 'u-text-xs-muted-flat',
    'font-size: 0.75rem; color: #94a3b8; margin: 0;' => 'u-text-xs-muted-flat2',
    'font-size: 0.875rem;' => 'u-text-sm',
    'font-size: 0.65rem;' => 'u-text-xs',
    'font-size: 0.6rem;' => 'u-text-xxs',
    'font-size: 2rem;' => 'u-text-2xl',
    'font-size: 1.75rem;' => 'u-text-1xl',
    'font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;' => 'u-text-muted-lead',
    'display: block; font-size: 0.8rem; font-weight: 500; color: #475569; margin-bottom: 0.375rem;' => 'u-field-note',
    'display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem; font-weight: 600;' => 'u-field-note-xs',
    'padding: 1.5rem;' => 'u-p-6',
    'padding: 1rem;' => 'u-p-4',
    'padding: 0;' => 'u-p-0',
    'padding:0' => 'u-p-0',
    'padding: 0 1.5rem;' => 'u-px-6',
    'text-align:right' => 'u-text-right',
    'width:100%;height:100%;object-fit:cover;' => 'u-cover',
    'width:100%;height:100%;object-fit:cover' => 'u-cover',
    'width: 100%; height: 100%; object-fit: cover; border-radius: 50%;' => 'u-cover-round',
    'margin-right: 0.35rem;' => 'u-mr-1',
    'margin-right: 0.5rem;' => 'u-mr-2',
    'font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;' => 'u-section-title',
    'font-size: 0.9rem; font-weight: 700; color: #0891b2; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;' => 'u-section-title-sm',
    'font-size: 0.9rem; font-weight: 600; color: #334155; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;' => 'u-section-title-slate',
    'display: block; font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 0.75rem;' => 'u-section-title-dark',
    'font-weight:600;color:var(--text-dark);font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis' => 'u-ellipsis-title',
    'font-weight:600;color:#334155;font-size:0.9rem;user-select:none' => 'u-check-label',
    'font-weight:600;color:#334155;font-size:0.9rem;display:block' => 'u-check-label-block',
    'font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.5rem 0;text-transform:uppercase;letter-spacing:0.5px' => 'u-eyebrow',
    'font-size:0.75rem;font-weight:600;color:var(--text-muted);margin:0 0 0.25rem 0;text-transform:uppercase;letter-spacing:0.5px' => 'u-eyebrow-tight',
    'font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem' => 'u-eyebrow-xs',
    'font-size:0.75rem;opacity:0.7;margin:0.25rem 0 0 0' => 'u-caption',
    'margin-top:1.5rem;padding:1rem;background:#fef2f2;border-radius:10px;color:#991b1b' => 'u-alert-danger-box',
    'display: flex; align-items: center; gap: 0.375rem; margin-top: 0.375rem; padding: 0.5rem 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem;' => 'u-inline-alert-danger',
    'padding:0.5rem 2.5rem 0.5rem 0.75rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-size:0.9rem;min-width:80px;transition:all 0.2s;cursor:pointer;background:white;appearance:none;-webkit-appearance:none;-moz-appearance:none' => 'u-select-mini',
    'padding:0.5rem 0.75rem 0.5rem 2.5rem;border:1px solid rgba(0,0,0,0.06);border-radius:8px;font-size:0.9rem;width:100%;transition:all 0.2s' => 'u-input-icon-left',
    'width:100%;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;background:#fff;font-size:0.9rem;transition:border-color 0.2s' => 'u-input-block',
    'background:rgba(0,0,0,0.05);color:var(--text-muted);padding:2px 8px;border-radius:12px;font-size:0.75rem' => 'u-badge-soft',
    'display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534' => 'u-badge-green',
    'background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff' => 'u-badge-blue',
    'background:linear-gradient(135deg,#38a169,#2f855a);color:#fff' => 'u-badge-green-solid',
    'background:#f8fafc;color:#ef4444;padding:0.6rem;min-width:40px;border-radius:8px' => 'u-delete-btn',
    'padding:0.75rem;background:#f8fafc;border-radius:8px' => 'u-box-soft',
    'padding:1.5rem;margin-bottom:1.5rem' => 'u-box-padded',
    'background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 1rem;' => 'u-box-slate',
    'background: white; border-radius: 0.75rem; box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; overflow: hidden;' => 'u-card-white',
    'background: #e2e8f0;' => 'u-bg-slate-200',
    'padding-left:1.25rem;margin:0;font-size:0.9rem' => 'u-list-indent',
    'margin: 0.25rem 0 0 0; padding-left: 1.25rem;' => 'u-list-indent-sm',
    'display:flex;align-items:flex-end;gap:0.25rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem;overflow-x:auto' => 'u-tabs-row',
    'padding:0.875rem 1rem;text-align:left;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid rgba(0,0,0,0.08)' => 'u-th',
    'padding: 1rem; text-align: left; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; cursor: pointer; white-space: nowrap;' => 'u-th-plain',
    'padding: 1.25rem 1.75rem; border-top: 1px solid #e5e7eb; background: #f9fafb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;' => 'u-card-footer',
    'font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem 0;' => 'u-h3',
    'font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;' => 'u-h3-slate',
    'font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;' => 'u-h2-slate',
    'font-size: 0.85rem; font-weight: 500; color: #0f172a;' => 'u-text-sm-strong',
    'font-size: 0.85rem; font-weight: 500; color: #0f172a; text-align: right; max-width: 60%;' => 'u-text-sm-strong-right',
    'opacity:0;transform:scale(0.5);transition:all 0.25s cubic-bezier(0.4, 0, 0.2, 1)' => 'u-check-svg',
    'opacity:0;transform:scale(0.5);transition:all 0.25s' => 'u-check-svg-fast',
    'font-family:monospace;background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:0.85rem' => 'u-code-chip',
    'width: 1.5rem; height: 1.5rem; background: #f0f9ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;' => 'u-dot-blue',
];

// ==== 1. Kumpulkan semua berkas Blade ====
$files = [];
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(__DIR__ . '/../resources/views', FilesystemIterator::SKIP_DOTS)
);
foreach ($it as $file) {
    if ($file->isFile() && str_ends_with(strtolower($file->getFilename()), '.blade.php')) {
        $files[] = $file->getPathname();
    }
}

$counts = [];
foreach ($files as $path) {
    $content = file_get_contents($path);
    preg_match_all('/style="([^"]*)"/', $content, $m);
    foreach ($m[1] as $style) {
        if (str_contains($style, '{{')) {
            continue; // dinamis (Blade) — jangan sentuh
        }
        $counts[$style] = ($counts[$style] ?? 0) + 1;
    }
}
arsort($counts);

// ==== 2. Bangun pemetaan style → class ====
$map = [];
$usedClasses = [];
$auto = 0;
foreach ($counts as $style => $cnt) {
    if ($cnt < 3) {
        break; // hentikan di pola yang jarang
    }
    if (isset($named[$style])) {
        $cls = $named[$style];
    } else {
        $auto++;
        $cls = 'u-a' . $auto;
    }
    $map[$style] = $cls;
    $usedClasses[$cls][] = $style;
}

// ==== 3. Tulis berkas CSS utilitas ====
$eol = "\n";
$css  = '/* ============================================================' . $eol;
$css .= ' * Dikembangkan oleh Institut Teknologi Del' . $eol;
$css .= ' * ============================================================' . $eol;
$css .= ' * Utilitas CSS hasil ekstraksi style inline dari Blade.' . $eol;
$css .= ' * Dibuat otomatis oleh scripts/extract_inline_styles.php' . $eol;
$css .= ' *' . $eol;
$css .= ' * Dikembangkan oleh Institut Teknologi Del' . $eol;
$css .= ' * ============================================================ */' . $eol . $eol;

foreach ($map as $style => $cls) {
    $css .= '/* ' . $cls . ' — ' . str_replace('*/', '* /', html_entity_decode($style, ENT_QUOTES)) . ' */' . $eol;
    $css .= '.' . $cls . ' { ' . html_entity_decode($style, ENT_QUOTES) . ' }' . $eol . $eol;
}

file_put_contents(__DIR__ . '/../public/assets/shared/css/utilities.css', $css);

// ==== 4. Ganti style="..." → class="..." di semua Blade ====
$tagRe = '/<[a-zA-Z][^>]*>/s';

$changedFiles = 0;
$changedAttrs = 0;

foreach ($files as $path) {
    $content = file_get_contents($path);
    if (! str_contains($content, 'style="')) {
        continue;
    }

    $newContent = preg_replace_callback($tagRe, function ($tag) use ($map, &$changedAttrs) {
        $t = $tag[0];

        // Jangan sentuh tag yang tidak punya style
        if (! preg_match('/\sstyle="([^"]*)"/', $t, $m)) {
            return $t;
        }
        $style = $m[1];
        if (! isset($map[$style])) {
            return $t;
        }

        $cls = $map[$style];
        $t = preg_replace('/\sstyle="[^"]*"/', '', $t, 1);

        // Gabungkan dengan class yang sudah ada
        if (preg_match('/class="([^"]*)"/', $t, $cm)) {
            $existing = trim($cm[1]);
            $tokens = preg_split('/\s+/', $existing);
            if (! in_array($cls, $tokens, true)) {
                $tokens[] = $cls;
            }
            $t = str_replace($cm[0], 'class="' . implode(' ', $tokens) . '"', $t);
        } else {
            // Sisipkan class tepat setelah nama tag
            $t = preg_replace('/^(<\w+)/', '$1 class="' . $cls . '"', $t, 1, $count);
            if ($count === 0) {
                return $tag[0]; // gagal — biarkan apa adanya
            }
        }

        $changedAttrs++;
        return $t;
    }, $content);

    if ($newContent !== $content) {
        file_put_contents($path, $newContent);
        $changedFiles++;
    }
}

echo "Selesai. Pola dipetakan: " . count($map) . " | Kelas auto: {$auto} | Berkas diubah: {$changedFiles} | Atribut diubah: {$changedAttrs}\n";
