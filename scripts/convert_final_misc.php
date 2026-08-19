<?php
// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// ============================================================
// Convert sisa handler inline (errors, reset-password, navigation)
// ke data-attribute + delegasi JS eksternal.
// ============================================================

$root = __DIR__ . '/..';

function write_file_preserve($path, $content) {
    $existing = is_file($path) ? file_get_contents($path) : '';
    $crlf = (strpos($existing, "\r\n") !== false);
    $out = $content;
    if ($crlf) {
        $out = str_replace(["\r\n", "\n"], ["\n", "\r\n"], $out);
    }
    file_put_contents($path, $out);
}

// ---- 1. Error pages: replace onclick attributes ----
$errorFiles = [
    '401', '403', '404', '419', '429', '500', '502', '503',
];
foreach ($errorFiles as $code) {
    $f = "$root/resources/views/errors/$code.blade.php";
    $t = file_get_contents($f);
    $t = str_replace('onclick="history.back()"', 'data-error-action="back"', $t);
    $t = str_replace('onclick="location.reload()"', 'data-error-action="reload"', $t);
    write_file_preserve($f, $t);
    echo "errors/$code.blade.php -> handlers: "
        . substr_count($t, 'onclick') . "\n";
}

// ---- 2. Error pages: rewrite per-page JS with delegation ----
$delegation = <<<'JS'

        // Delegasi untuk tombol aksi halaman error
        // (diekstrak dari atribut inline onclick)
        document.querySelectorAll('[data-error-action]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var action = btn.getAttribute('data-error-action');
                if (action === 'back') {
                    if (window.history.length <= 1) {
                        window.location.href = '/';
                    } else {
                        window.history.back();
                    }
                } else if (action === 'reload') {
                    window.location.reload();
                }
            });
        });
    
JS;

foreach ($errorFiles as $code) {
    $f = "$root/public/assets/shared/js/errors-$code.js";
    if (!is_file($f)) continue;
    $old = file_get_contents($f);
    // strip the old inline block (keep header comment)
    $headerEnd = strpos($old, '*/');
    if ($headerEnd !== false) {
        $header = substr($old, 0, $headerEnd + 2);
        // Keep only the credit header, replace body with delegation
        $new = $header . "\n" . $delegation;
        write_file_preserve($f, $new);
        echo "errors-$code.js -> rewritten\n";
    }
}

// ---- 3. auth/reset-password.blade.php ----
$f = "$root/resources/views/auth/reset-password.blade.php";
$t = file_get_contents($f);
$t = str_replace('oninput="checkPasswordStrength(this.value)"', 'data-password-strength=""', $t);
$t = str_replace("onclick=\"togglePassword('password', this)\"", 'data-toggle-password="password"', $t);
$t = str_replace("onclick=\"togglePassword('password_confirmation', this)\"", 'data-toggle-password="password_confirmation"', $t);
write_file_preserve($f, $t);
echo "auth/reset-password.blade.php -> handlers: " . substr_count($t, 'onclick') . "\n";

// ---- 4. auth-reset-password.js: append wiring ----
$f = "$root/public/assets/shared/js/auth-reset-password.js";
$t = file_get_contents($f);
$wiring = <<<'JS'

        // ==========================================
        // WIRING DELEGASI (diekstrak dari atribut inline)
        // ==========================================
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                togglePassword(btn.getAttribute('data-toggle-password'), btn);
            });
        });

        document.querySelectorAll('[data-password-strength]').forEach(function (input) {
            input.addEventListener('input', function () {
                checkPasswordStrength(input.value);
            });
        });
JS;
if (strpos($t, 'data-toggle-password') === false) {
    write_file_preserve($f, $t . "\n" . $wiring . "\n");
    echo "auth-reset-password.js -> wiring appended\n";
}

// ---- 5. layouts/navigation.blade.php: logout links -> submit buttons ----
$f = "$root/resources/views/layouts/navigation.blade.php";
$t = file_get_contents($f);

$dropdownBtn = '<button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">';
$responsiveBtn = '<button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">';

$t = str_replace(
    '<x-dropdown-link :href="route(\'logout\')"' . "\n" . '                                    onclick="event.preventDefault();' . "\n" . '                                                this.closest(\'form\').submit();">' . "\n" . '                                {{ __(\'Log Out\') }}' . "\n" . '                            </x-dropdown-link>',
    $dropdownBtn . "\n" . '                                {{ __(\'Log Out\') }}' . "\n" . '                            </button>',
    $t
);
$t = str_replace(
    '<x-responsive-nav-link :href="route(\'logout\')"' . "\n" . '                            onclick="event.preventDefault();' . "\n" . '                                        this.closest(\'form\').submit();">' . "\n" . '                        {{ __(\'Log Out\') }}' . "\n" . '                    </x-responsive-nav-link>',
    $responsiveBtn . "\n" . '                        {{ __(\'Log Out\') }}' . "\n" . '                    </button>',
    $t
);
write_file_preserve($f, $t);
echo "layouts/navigation.blade.php -> onclick: " . substr_count($t, 'onclick') . "\n";

echo "DONE\n";
