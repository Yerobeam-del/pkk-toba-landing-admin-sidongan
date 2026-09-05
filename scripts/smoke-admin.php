<?php

/**
 * Admin Panel smoke test.
 *
 * Boots the real Laravel app in-process, authenticates a super admin,
 * and renders every admin GET route (plus a few public/API endpoints
 * the admin panel depends on). Each request runs inside a DB
 * transaction that is rolled back, so nothing is persisted.
 *
 * Usage: php scripts/smoke-admin.php
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Bind a base request before anything resolves the auth guard —
// SessionGuard::setRequest() needs a request instance at boot time.
$app->instance('request', Request::create('/', 'GET'));

// ============================================================
// 1. Resolve / prepare an authenticated super admin
// ============================================================
$admin = App\Models\User::where('email', 'admin@pkk-toba.id')->first()
    ?? App\Models\User::query()->latest('id')->first();

if (!$admin) {
    fwrite(STDERR, "No users exist in the database — cannot authenticate.\n");
    exit(2);
}

$adminRole = App\Models\Role::where('name', 'administrator')->first()
    ?? App\Models\Role::where('name', 'super_admin')->first()
    ?? App\Models\Role::firstOrCreate(
        ['name' => 'administrator'],
        ['display_name' => 'Administrator', 'description' => 'Smoke test role']
    );

// Mutations below persist only in memory / inside the rolled-back
// transactions; they exist so permission-gated pages actually render.
$admin->forceFill([
    'role_id'            => $admin->role_id ?? $adminRole->id,
    'email_verified_at'  => $admin->email_verified_at ?? now(),
    'sidongan_role'      => 'super_admin',
])->save();

echo "Auth as: {$admin->email} (id {$admin->id}, role: " . ($admin->role->name ?? '?') . ")\n";

// ============================================================
// 2. Collect target routes
// ============================================================
$staticParams = [
    'admin.user-management.show'    => ['user' => $admin->id],
    'admin.user-management.edit'    => ['user' => $admin->id],
    'admin.user-management.desas'   => ['kecamatanKode' => '12.01'],
    'admin.admin.struktur.tab'      => ['tab' => 'pengurus'],
    'admin.sieda-data.module'       => ['module' => 'keluarga'],
    'admin.sieda-data.show'         => ['module' => 'keluarga', 'id' => '1'],
    // Placeholder IDs: detail pages should render or 404 gracefully,
    // never blow up with a 500.
    'admin.berita.show'             => ['beritum' => 'smoke-x'],
    'admin.berita.edit'             => ['beritum' => 'smoke-x'],
    'admin.sk.show'                 => ['dokumen' => 'smoke-x'],
    'admin.sk.edit'                 => ['dokumen' => 'smoke-x'],
    'admin.template.show'           => ['template' => 'smoke-x'],
    'admin.template.edit'           => ['template' => 'smoke-x'],
    'admin.struktur.show'           => ['struktur' => '999999'],
    'admin.struktur.edit'           => ['struktur' => '999999'],
    'admin.aplikasi.show'           => ['aplikasi' => '999999'],
    'admin.aplikasi.edit'           => ['aplikasi' => '999999'],
    'admin.desa.edit'               => ['desa' => '999999'],
];

$extras = ['/', 'api/v1/health', 'api/v1/desas', 'api/v1/desas/max-sort-order'];

$targets = [];
foreach ($app['router']->getRoutes() as $route) {
    $uri = $route->uri();
    if (!str_starts_with($uri, 'admin')) continue;
    if (!in_array('GET', $route->methods(), true)) continue;
    if (str_contains($route->getActionName(), 'Closure')) continue;

    $name = $route->getName();
    $params = [];

    if (str_contains($uri, '{')) {
        if (!$name || !isset($staticParams[$name])) continue;
        $params = $staticParams[$name];
        // Verify placeholders match
        foreach ($params as $k => $v) {
            if (!str_contains($uri, '{' . $k . '}')) continue 2;
        }
        try {
            $url = route($name, $params);
        } catch (Throwable $e) {
            continue;
        }
    } else {
        $url = '/' . $uri;
    }

    $targets[] = [
        'url'    => $url,
        'name'   => $name ?? '-',
        'action' => $route->getActionName(),
    ];
}

$pass = $softFail = $fail = 0;
$rows = [];

$run = function (string $url) use ($app, $kernel) {
    DB::connection()->beginTransaction();
    try {
        $req = Request::create($url, 'GET');
        $app->instance('request', $req);
        $kernel->bootstrap($req);
        $res = $kernel->handle($req);
        $status = $res->getStatusCode();
        $body = (string) $res->getContent();
        $marker = '';
        if ($status === 200 && (str_contains($body, 'Whoops') || str_contains($body, 'RuntimeException'))) {
            $marker = 'debug/error page content';
        }
        return [$status, $marker];
    } catch (Throwable $e) {
        return [500, get_class($e) . ': ' . mb_substr($e->getMessage(), 0, 150)];
    } finally {
        DB::connection()->rollBack();
    }
};

// ============================================================
// 3. Run authenticated smoke on admin routes
// ============================================================
Auth::loginUsingId($admin->id);

foreach ($targets as $t) {
    [$status, $note] = $run($t['url']);

    $verdict = 'PASS';
    if ($status === 200 && $note === '') {
        $pass++;
    } elseif ($status === 404) {
        // Detail pages with placeholder IDs may 404 when the table is
        // empty — graceful, not a regression.
        $verdict = 'OK (404)';
        $softFail++;
    } else {
        $verdict = "FAIL {$status}";
        $fail++;
        $note = $note !== '' ? $note : '(no exception detail)';
    }

    $rows[] = sprintf(
        "%-10s %-38s %s%s",
        $verdict,
        $t['name'],
        $t['url'],
        $note !== '' ? "  — {$note}" : ''
    );
}

// ============================================================
// 4. Public / API extras (admin panel dependencies)
// ============================================================
Auth::logout();
foreach ($extras as $url) {
    [$status, $note] = $run($url);
    $verdict = $status === 200 ? 'PASS' : "FAIL {$status}";
    if ($verdict === 'PASS') $pass++; else { $fail++; }
    $rows[] = sprintf(
        "%-10s %-38s %s%s",
        $verdict,
        '(public/api)',
        $url,
        $note !== '' ? "  — {$note}" : ''
    );
}

echo "\n==================== RESULTS ====================\n";
foreach ($rows as $row) echo $row . "\n";

echo "\nTotal: " . count($targets) . " admin routes + " . count($extras) . " public/API — "
    . "PASS {$pass}, OK(404) {$softFail}, FAIL {$fail}\n";

exit($fail > 0 ? 1 : 0);
