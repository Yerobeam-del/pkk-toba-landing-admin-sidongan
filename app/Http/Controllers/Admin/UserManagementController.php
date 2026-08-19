<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Kecamatan;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\SiedaSyncService;

class UserManagementController extends Controller
{
    /**
     * Display listing of users.
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();
        $perPage = $request->get('per_page', 10);
        $tab = $request->get('tab', 'all');
        $search = $request->get('search'); // Ambil parameter pencarian

        $query = User::with('applications')->latest();

        if ($currentUser->sidongan_role !== 'super_admin') {
            $query->where('sidongan_role', '!=', 'super_admin');
        }

        // Filter berdasarkan tab yang dipilih SEBELUM pagination
        if ($tab === 'active') {
            $query->whereNotNull('email_verified_at');
        } elseif ($tab === 'inactive') {
            $query->whereNull('email_verified_at');
        } elseif ($tab === 'with-access') {
            $query->whereHas('applications');
        }

        // Tambahkan filter pencarian berdasarkan Nama atau Email
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate($perPage);

        return view('admin.user-management.index', compact('users', 'tab', 'perPage'));
    }

    /**
     * Show form to create new user.
     */
    /**
     * Grup permission yang disembunyikan dari form karena modulnya
     * sedang dinonaktifkan (menu sidebar-nya juga dikomentari).
     * Kosongkan array ini untuk memunculkannya kembali.
     */
    private const HIDDEN_PERMISSION_GROUPS = ['desa'];

    /**
     * Permission yang boleh dipilih di form tambah/edit akun,
     * dikelompokkan per modul.
     */
    private function assignablePermissions()
    {
        return Permission::whereNotIn('group', self::HIDDEN_PERMISSION_GROUPS)
            ->get()
            ->groupBy('group');
    }

    public function create()
    {
        $applications = Application::where('is_active', true)->orderBy('name')->get();
        $sidonganRoles = User::getSidonganRoles();
        $roles = Role::all();
        $permissions = $this->assignablePermissions();

        // Ambil data kecamatan Kabupaten Toba (kode 12.12)
        $kecamatans = Kecamatan::where('kabupaten_kode', '12.12')->orderBy('name')->get();

        return view('admin.user-management.create', compact(
            'applications', 'sidonganRoles', 'roles', 'permissions', 'kecamatans'
        ));
    }

    /**
     * Store new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@pkk-toba\.id$/'
            ],
            'phone_number' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255|unique:users,personal_email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'applications' => 'array',
            'applications.*' => 'exists:applications,id',
            'sidongan_role' => 'nullable|in:ketua,sekretaris,bendahara,staf_ahli_1,staf_ahli_2,pengurus_1,pengurus_2,pengurus_3,pengurus_4,super_admin',
            'sieda_role' => 'nullable|in:operator,kader',
            'sieda_kecamatan' => 'nullable|string|max:255',
            'sieda_kelurahan' => 'nullable|string|max:255',
        ]);

        // VALIDASI KEAMANAN: Hanya Super Admin yang bisa membuat akun Super Admin
        $selectedRole = Role::find($validated['role_id']);
        if ($selectedRole && $selectedRole->name === 'super_admin' && !auth()->user()->hasRole('super_admin')) {
            return redirect()->back()->withInput()->withErrors(['role_id' => 'Anda tidak memiliki izin untuk membuat akun Super Admin.']);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
            'personal_email' => $validated['personal_email'] ?? null,
            'role_id' => $validated['role_id'],
            'sidongan_role' => $validated['sidongan_role'] ?? null,
            'sieda_role' => $validated['sieda_role'] ?? null,
            'sieda_kecamatan' => $validated['sieda_kecamatan'] ?? null,
            'sieda_kelurahan' => $validated['sieda_kelurahan'] ?? null,
            'email_verified_at' => now(),
        ]);

        // Izin disimpan per akun di tabel permission_user, BUKAN ke role bersama.
        // Administrator sudah punya akses penuh lewat role, jadi tidak perlu izin pribadi.
        $role = Role::find($validated['role_id']);
        if ($role && in_array($role->name, ['administrator', 'super_admin'])) {
            $user->permissions()->detach();
        } else {
            $user->permissions()->sync($validated['permissions'] ?? []);
        }

        if (isset($validated['applications'])) {
            $user->applications()->sync($validated['applications']);
        }

        // SINKRONISASI KE SIEDA BACKEND via service (auto header X-Sieda-Key + HMAC)
        if ($user->sieda_role) {
            $syncService = app(SiedaSyncService::class);
            $syncService->syncUser([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $request->password,
                'sieda_role' => $user->sieda_role,
                'kecamatan_code' => $user->sieda_kecamatan,
                'kelurahan_code' => $user->sieda_kelurahan,
            ]);
        }

        return redirect()->route('admin.user-management.index')
            ->with('success', 'Akun berhasil dibuat dan disinkronisasi!');
    }

    /**
     * Show user details.
     */
    public function show(User $user)
    {
        $user->load(['applications', 'role.permissions']);

        // Ambil data kecamatan berdasarkan kode
        $kecamatan = null;
        if ($user->sieda_kecamatan) {
            $kecamatan = \App\Models\Kecamatan::where('kode_wilayah', $user->sieda_kecamatan)->first();
        }

        // Ambil data kelurahan/desa berdasarkan kode
        $kelurahan = null;
        if ($user->sieda_kelurahan) {
            $kelurahan = \App\Models\Desa::where('kode_wilayah', $user->sieda_kelurahan)->first();
        }

        return view('admin.user-management.show', compact('user', 'kecamatan', 'kelurahan'));
    }

    /**
     * Show form to edit user.
     */
    public function edit(User $user)
    {
        // Hanya Super Admin yang bisa melihat form edit akun Super Admin
        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            abort(403, 'Akses ditolak!');
        }

        $applications = Application::where('is_active', true)->orderBy('name')->get();
        $userApplications = $user->applications->pluck('id')->toArray();
        $sidonganRoles = User::getSidonganRoles();
        $roles = Role::all();
        $permissions = $this->assignablePermissions();

        // Yang dicentang adalah izin PRIBADI akun ini. Izin bawaan role tidak
        // ikut dicentang karena bukan milik akun dan tidak bisa dicabut dari sini.
        $userPermissions = $user->permissions->pluck('id')->toArray();

        // Ditampilkan sebagai keterangan agar admin tahu akses apa yang sudah
        // otomatis didapat dari role-nya.
        $rolePermissionNames = $user->role
            ? $user->role->permissions->pluck('display_name')->toArray()
            : [];

        // Ambil data kecamatan untuk edit form
        $kecamatans = Kecamatan::where('kabupaten_kode', '12.12')->orderBy('name')->get();

        return view('admin.user-management.edit', compact(
            'user', 'applications', 'userApplications', 'sidonganRoles', 'roles',
            'permissions', 'userPermissions', 'rolePermissionNames', 'kecamatans'
        ));
    }

    /**
     * Update user.
     */
    public function update(Request $request, User $user)
    {
        // VALIDASI KEAMANAN: Hanya Super Admin yang bisa mengedit akun Super Admin
        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            return back()->with('error', 'Akses ditolak! Hanya Super Admin yang dapat mengedit akun Super Admin.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email,' . $user->id,
                'regex:/^[a-zA-Z0-9._%+-]+@pkk-toba\.id$/'
            ],
            'phone_number' => 'nullable|string|max:20',
            'personal_email' => 'nullable|email|max:255|unique:users,personal_email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'applications' => 'array',
            'applications.*' => 'exists:applications,id',
            'sidongan_role' => 'nullable|in:ketua,sekretaris,bendahara,staf_ahli_1,staf_ahli_2,pengurus_1,pengurus_2,pengurus_3,pengurus_4,super_admin',
            'sieda_role' => 'nullable|in:operator,kader',
            'sieda_kecamatan' => 'nullable|string|max:255',
            'sieda_kelurahan' => 'nullable|string|max:255',
        ]);

        // VALIDASI KEAMANAN: Hanya Super Admin yang bisa mengubah role menjadi Super Admin
        $selectedRole = Role::find($validated['role_id']);
        if ($selectedRole && $selectedRole->name === 'super_admin' && !auth()->user()->hasRole('super_admin')) {
            return redirect()->back()->withInput()->withErrors(['role_id' => 'Anda tidak memiliki izin untuk mengubah role menjadi Super Admin.']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone_number = $validated['phone_number'] ?? null;
        $user->personal_email = $validated['personal_email'] ?? null;
        $user->role_id = $validated['role_id'];
        $user->sidongan_role = $validated['sidongan_role'] ?? null;
        $user->sieda_role = $validated['sieda_role'] ?? null;
        $user->sieda_kecamatan = $validated['sieda_kecamatan'] ?? null;
        $user->sieda_kelurahan = $validated['sieda_kelurahan'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Izin disimpan per akun di tabel permission_user, BUKAN ke role bersama.
        $role = Role::find($validated['role_id']);
        if ($role && in_array($role->name, ['administrator', 'super_admin'])) {
            $user->permissions()->detach();
        } else {
            $user->permissions()->sync($validated['permissions'] ?? []);
        }

        if (isset($validated['applications'])) {
            $user->applications()->sync($validated['applications']);
        }

        // SINKRONISASI UPDATE KE SIEDA BACKEND via service (auto header X-Sieda-Key + HMAC)
        if ($user->sieda_role) {
            $syncService = app(SiedaSyncService::class);
            $syncService->syncUser([
                'name' => $user->name,
                'email' => $user->email,
                'password' => !empty($validated['password']) ? $request->password : null,
                'sieda_role' => $user->sieda_role,
                'kecamatan_code' => $user->sieda_kecamatan,
                'kelurahan_code' => $user->sieda_kelurahan,
            ], $user->getOriginal('email')); // Kirim email lama untuk path /sync-user/{email}
        }

        return redirect()->route('admin.user-management.edit', $user)
            ->with('success', 'Akun berhasil diperbarui dan disinkronisasi!');
    }

    /**
     * Toggle user active/inactive status.
     */
    public function toggleStatus(User $user)
    {
        if (auth()->user()->sidongan_role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
        }

        $willDeactivate = $user->email_verified_at !== null;

        if ($user->id === auth()->id() && $willDeactivate) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa menonaktifkan akun sendiri! Minta Super Admin lain untuk melakukannya.'
            ], 403);
        }

        if ($willDeactivate) {
            $user->email_verified_at = null;
            $action = 'dinonaktifkan';
        } else {
            $user->email_verified_at = now();
            $action = 'diaktifkan';
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Akun ' . $user->name . ' telah ' . $action
        ]);
    }

    /**
     * Delete user.
     */
    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        if ($currentUser->sidongan_role !== 'super_admin') {
            return back()->with('error', 'Akses ditolak! Hanya Super Admin yang dapat menghapus akun.');
        }

        if ($user->sidongan_role === 'super_admin') {
            return back()->with('error', 'Anda tidak bisa menghapus akun Super Admin!');
        }

        try {
            // Revoke akses SIEDA sebelum menghapus user lokal
            if ($user->sieda_role || $user->email) {
                $syncService = app(SiedaSyncService::class);
                $syncService->revokeAccess($user->email);
            }

            DB::table('application_user')->where('user_id', $user->id)->delete();
            $user->delete();

            return redirect()->route('admin.user-management.index')
                ->with('success', 'Akun berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    /**
     * Resend verification email.
     */
    public function resendVerification(User $user)
    {
        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah terverifikasi']);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['success' => true, 'message' => 'Email verifikasi berhasil dikirim']);
    }

    /**
     * Reset password user dari Admin Panel (tanpa perlu email).
     */
    public function resetPassword(Request $request, User $user)
    {
        // VALIDASI KEAMANAN: Hanya Super Admin yang bisa reset password akun lain
        if (!auth()->user()->hasRole('super_admin')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak! Hanya Super Admin yang dapat mereset password.'], 403);
        }

        if ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak!'], 403);
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password akun ' . $user->name . ' berhasil direset!'
        ]);
    }

    /**
     * Get desas for a given kecamatan code (AJAX)
     */
    public function getDesas($kecamatanKode)
    {
        $kecamatan = Kecamatan::where('kode_wilayah', $kecamatanKode)->first();

        if (!$kecamatan) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $desas = Desa::where('kecamatan_id', $kecamatan->id)
                     ->where('is_active', true)
                     ->orderBy('name')
                     ->get(['id', 'name', 'kode_wilayah']);

        return response()->json([
            'success' => true,
            'data' => $desas
        ]);
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
