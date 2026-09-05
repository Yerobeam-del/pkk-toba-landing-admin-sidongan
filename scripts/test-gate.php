<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();
$app->instance('request', Illuminate\Http\Request::create('/', 'GET'));

DB::connection()->beginTransaction();

$fail = 0;
$check = function (string $label, bool $got, bool $want) use (&$fail) {
    $ok = $got === $want;
    if (!$ok) $fail++;
    printf("%-4s %s — got %s, want %s\n", $ok ? 'PASS' : 'FAIL', $label, var_export($got, true), var_export($want, true));
};

$super = App\Models\User::where('sidongan_role', 'super_admin')->first();
$check('super admin can(manage-berita) via Gate::before', $super->can('manage-berita'), true);
$check('super admin isSidonganKetua (centralized)', $super->isSidonganKetua(), true);
$check('super admin isSidonganSekretaris (centralized)', $super->isSidonganSekretaris(), true);

// Anggota tanpa izin apa pun
$anggota = App\Models\User::factory()->create(['name' => 'gate-test-a', 'email' => 'gate-test-a@pkk-toba.id']);
$anggota->forceFill(['role_id' => App\Models\Role::where('name', 'anggota')->first()->id])->save();
Auth::login($anggota);
$check('anggota tanpa izin can(manage-berita) → default DENY', $anggota->can('manage-berita'), false);
$check('anggota tanpa izin isSidonganKetua', $anggota->isSidonganKetua(), false);

// Anggota + izin pribadi manage-berita
$perm = App\Models\Permission::where('name', 'manage-berita')->first();
$anggota->permissions()->attach($perm->id);
$anggota->refresh();
$check('anggota + izin pribadi can(manage-berita) → GRANT', $anggota->can('manage-berita'), true);

DB::connection()->rollBack();
echo $fail === 0 ? "=== ALL GATE SEMANTICS OK ===\n" : "=== {$fail} FAILURES ===\n";
exit($fail > 0 ? 1 : 0);
