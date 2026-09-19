<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Http\Controllers;

use App\Http\Middleware\SidonganEnsureProfileComplete;
use App\Support\ProfileFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Available systems that can trigger onboarding.
     * Each system has its own guard, branding, and redirect URLs.
     */
    private array $systems = [
        'sidongan' => [
            'guard' => 'sidongan',
            'name' => 'SIDONGAN',
            'full_name' => 'Sistem Informasi Dokumen Organisasi Agenda dan Naskah',
            'org' => 'PKK Kabupaten Toba',
            'logo' => 'assets/sidongan/images/Logo-SIDONGAN-white.svg',
            'color_start' => '#0d9486',
            'color_mid' => '#14b8a6',
            'color_end' => '#0ea5e9',
            'dashboard_route' => 'sidongan.dashboard',
            'login_route' => 'sidongan.login',
            'profile_edit_route' => 'sidongan.profile.edit',
        ],
        'admin' => [
            'guard' => 'web',
            'name' => 'Admin Panel',
            'full_name' => 'Panel Administrasi PKK Kabupaten Toba',
            'org' => 'PKK Kabupaten Toba',
            'logo' => 'assets/shared/images/Logo-Kabupaten-Toba-White.svg',
            'color_start' => '#6d28d9',
            'color_mid' => '#7c3aed',
            'color_end' => '#a855f7',
            'dashboard_route' => 'admin.dashboard',
            'login_route' => 'login',
            'profile_edit_route' => null,
        ],
    ];

    /**
     * Detect which system the user logged in from.
     * Returns system key or null if not authenticated.
     */
    private function detectSystem(): ?string
    {
        // Check SIDONGAN guard first
        if (Auth::guard('sidongan')->check()) {
            return 'sidongan';
        }

        // Check web (admin) guard
        if (Auth::guard('web')->check()) {
            return 'admin';
        }

        return null;
    }

    /**
     * Get the current authenticated user and their system.
     */
    private function getUserAndSystem(): array
    {
        $systemKey = $this->detectSystem();

        if (!$systemKey || !isset($this->systems[$systemKey])) {
            return [null, null];
        }

        $system = $this->systems[$systemKey];
        $user = Auth::guard($system['guard'])->user();

        return [$user, $systemKey];
    }

    /**
     * Tujuan akhir setelah onboarding selesai: ruang kerja tersimpan user
     * (paritas dengan alur login) — bukan selalu dashboard sistem asal.
     * User Admin Panel yang belum memilih → launcher "Pilih Ruang Kerja".
     */
    private function tujuanSetelahOnboarding(array $system, $user): string
    {
        if ($system['guard'] === 'web' && $user) {
            if (empty($user->workspace)) {
                return route('workspace.pilih');
            }

            return \App\Http\Controllers\WorkspaceController::url($user->workspace, $user);
        }

        return route($system['dashboard_route']);
    }

    /**
     * Route login yang sesuai dengan host saat ini.
     *
     * Halaman onboarding dipakai lintas aplikasi (SIDONGAN / Admin Panel).
     * Pengunjung yang belum login harus diarahkan ke halaman login aplikasi
     * yang sedang dibuka — kalau di host SIDONGAN → login SIDONGAN, selain itu
     * → login Admin Panel (route login lama selalu tersedia di host mana pun).
     */
    private function loginRoute(Request $request): string
    {
        $sidonganDomain = (string) config('app.sidongan_domain');

        if ($sidonganDomain !== '' && str_ends_with($request->getHost(), $sidonganDomain)) {
            return 'sidongan.login';
        }

        return 'login';
    }

    /**
     * Display the onboarding page.
     * Smart: detects which system the user came from and adapts branding.
     */
    public function show(Request $request): View|RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        // Not authenticated → redirect ke login aplikasi yang sesuai host
        if (!$user) {
            return redirect()->route($this->loginRoute($request));
        }

        $system = $this->systems[$systemKey];

        // Check if profile is already complete
        $missingFields = $this->getMissingFields($user, $systemKey);

        // SEMUA SISTEM (Admin Panel & SIDONGAN): email pribadi yang menunggu
        // verifikasi (alur mana pun: setup lama, onboarding, atau akun sinkron
        // SIEDA) → tampilkan panel verifikasi "Cek Email" LANGSUNG di sini.
        // Semua alur dan semua sistem bertemu di satu tempat — tidak ada lagi
        // halaman notice terpisah yang menduplikasi fungsi ini (paritas penuh).
        $pendingEmail = session('pending_personal_email');
        if ($user->hasVerifiedPersonalEmail()) {
            // Email sudah terverifikasi — bisa jadi lewat link di email dari
            // perangkat/tab lain. Bersihkan status pending & OTP basi agar
            // panel "Cek Email" tidak tampil menyesatkan saat reload.
            if ($pendingEmail || session('onboarding_otp_email')) {
                session()->forget(['pending_personal_email', 'onboarding_otp_email']);
            }
            $pendingEmail = null;
        } elseif (!$pendingEmail && ProfileFields::isFilled($user->personal_email)) {
            // Normalisasi: fallback DB → session, konsisten dengan alur setup.
            $pendingEmail = $user->personal_email;
            session(['pending_personal_email' => $pendingEmail]);
        }

        // Jangan kabur dari panel verifikasi: user dengan email pending harus
        // tetap melihat "Cek Email" di onboarding sampai emailnya terverifikasi
        // (atau memilih lewati).
        if (empty($missingFields) && !$pendingEmail) {
            return redirect()->to($this->tujuanSetelahOnboarding($system, $user));
        }

        $completionPercentage = $this->getCompletionPercentage($user, $systemKey);

        // Panel verifikasi: OTP aktif (session) ATAU pending email dari alur
        // lain (mis. diset via halaman setup lama lalu login ulang).
        $otpEmail = session('onboarding_otp_email') ?: $pendingEmail;

        // Sisa cooldown kirim ulang kode (detik) — dihitung dari DB supaya
        // countdown timer di tombol selalu sinkron dengan validasi server.
        // 0 bila belum pernah minta kode atau cooldown sudah lewat.
        $otpResendIn = 0;
        if ($otpEmail && $user->personal_email_otp_requested_at) {
            $elapsed = (int) floor($user->personal_email_otp_requested_at->diffInSeconds(now()));
            $otpResendIn = max(0, self::OTP_RESEND_COOLDOWN - $elapsed);
        }

        return view('onboarding.index', [
            'user' => $user,
            'system' => $system,
            'systemKey' => $systemKey,
            'missingFields' => $missingFields,
            'completionPercentage' => $completionPercentage,
            'otpEmail' => $otpEmail,
            // True bila kode OTP memang sedang aktif untuk email tersebut
            // (baru dikirim dari onboarding). False bila panel muncul karena
            // email pending dari alur lain — kode belum dikirim, tampilkan
            // tombol "Kirim kode" dulu, bukan form input kode kosong.
            'otpActive' => $otpEmail && session('onboarding_otp_email') === $otpEmail
                && $user->personal_email_otp_hash && $user->personal_email_otp_expires_at
                && $user->personal_email_otp_expires_at->isFuture(),
            'otpExpiresAt' => $otpEmail ? $user->personal_email_otp_expires_at : null,
            'otpResendIn' => $otpResendIn,
        ]);
    }

    /**
     * Handle onboarding form submission.
     */
    public function store(Request $request): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route($this->loginRoute($request));
        }

        $system = $this->systems[$systemKey];
        $missingFields = $this->getMissingFields($user, $systemKey);

        // Build dynamic validation rules
        $rules = [];

        // Avatar is always optional
        $rules['avatar'] = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:2048'];

        if (in_array('phone_number', $missingFields)) {
            $rules['phone_number'] = [
                'required', 'string', 'min:10', 'max:15',
                'regex:/^[0-9+\-\s()]+$/',
            ];
        }

        if (in_array('personal_email', $missingFields)) {
            $rules['personal_email'] = [
                'required', 'email', 'max:255',
                'unique:users,personal_email,' . $user->id,
            ];
        }

        if (empty($rules)) {
            return redirect()->to($this->tujuanSetelahOnboarding($system, $user));
        }

        $validated = $request->validate($rules);

        $updateData = [];

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $file = $request->file('avatar');
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            // Ekstensi dari isi file (magic bytes), bukan nama kiriman klien;
            // SVG ditolak (XSS via /storage). Lihat ImageUploadSanitizer.
            $path = \App\Support\ImageUploadSanitizer::store($file, 'avatars', 'avatar_' . $user->id . '_');
            if ($path === false) {
                return back()->withErrors(['avatar' => 'File avatar bukan gambar yang didukung (JPG/PNG/WEBP/GIF).'])->withInput();
            }
            $updateData['avatar'] = $path;
        }

        if (isset($validated['phone_number'])) {
            $updateData['phone_number'] = $validated['phone_number'];
        }

        if (isset($validated['personal_email'])) {
            $updateData['personal_email'] = $validated['personal_email'];

            // Paritas dengan SIEDA: email pribadi yang berubah membatalkan
            // verifikasi lama — link verifikasi baru dikirim di bawah dan
            // fitur Lupa Password terkunci sampai email baru diverifikasi.
            if ($user->personal_email !== $validated['personal_email']) {
                $updateData['personal_email_verified_at'] = null;
            }
        }

        if (!empty($updateData)) {
            $user->update($updateData);

            Log::channel('audit')->info('Profil dilengkapi via onboarding', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'system' => $systemKey,
                'fields_updated' => array_keys($updateData),
                'timestamp' => now()->toIso8601String(),
            ]);
        }

        // Personal email baru didaftarkan lewat onboarding → kirim verifikasi.
        // SEMUA SISTEM (Admin Panel & SIDONGAN — paritas penuh): SATU email
        // gabungan (kode OTP 6 digit + link alternatif) — diverifikasi langsung
        // di halaman onboarding atau via klik link.
        // Fitur Lupa Password hanya aktif setelah email pribadi diverifikasi.
        $verificationSent = false;
        if (isset($updateData['personal_email']) && ProfileFields::isFilled($updateData['personal_email'])) {
            // Route signed URL mengikuti sistem asal: SIDONGAN memakai route
            // sendiri (host sidongan), Admin Panel memakai route default.
            $verifyRoute = $systemKey === 'sidongan'
                ? 'sidongan.personal-email.verify'
                : 'personal-email.verify';

            try {
                // Satu email gabungan: OTP + link fallback (semua sistem)
                $this->issueEmailOtp($user, $updateData['personal_email'], $verifyRoute);
                session(['onboarding_otp_email' => $updateData['personal_email']]);
                $verificationSent = true;

                // Paritas dengan alur setup (Auth\PersonalEmailController): email
                // pending JUGA disimpan di SESSION supaya halaman notice dan tombol
                // "Kirim ulang" bekerja identik dari titik masuk mana pun. Email
                // tetap tersimpan di DB karena logika field pemblokir onboarding
                // membacanya dari sana.
                session(['pending_personal_email' => $updateData['personal_email']]);

                Log::channel('audit')->info('Verifikasi email pribadi dikirim via onboarding', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'system' => $systemKey,
                    'personal_email' => $updateData['personal_email'],
                    'timestamp' => now()->toIso8601String(),
                ]);
            } catch (\Throwable $e) {
                Log::channel('audit')->warning('Gagal kirim link verifikasi email pribadi via onboarding', [
                    'user_id' => $user->id,
                    'system' => $systemKey,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // "Simpan & Lanjutkan" = simpan lalu LANGSUNG lanjut ke aplikasi
        // (dashboard), meskipun masih ada langkah yang belum dilengkapi — selama
        // tidak ada field PEMBLOKIR yang kosong. Field pemblokir didefinisikan
        // sekali di App\Support\ProfileFields (phone_number & personal_email) —
        // identik dengan SIEDA; avatar opsional tidak pernah mengunci user.
        // "Lewati — nanti saja" tetap tersedia untuk user yang ingin melewati
        // onboarding tanpa menyimpan apa pun.
        $freshUser = $user->fresh();
        $blockingRemaining = ProfileFields::missingBlocking($freshUser);

        if (empty($blockingRemaining)) {
            // Tidak ada field pemblokir tersisa — hapus status skip (session & DB)
            // supaya kalau suatu saat field dikosongkan lagi, onboarding muncul lagi.
            session()->forget('onboarding_skipped');
            if ($freshUser->onboarding_skipped_at) {
                $freshUser->forceFill(['onboarding_skipped_at' => null])->save();
            }

            $successMessage = 'Profil berhasil disimpan! Anda bisa melengkapi foto profil nanti melalui menu Edit Profil.';
            if ($verificationSent) {
                $successMessage .= ' Link verifikasi email pribadi telah dikirim ke <strong>' . e($updateData['personal_email']) . '</strong> — silakan cek inbox Anda.';
            }

            // Sistem mana pun dengan OTP pending: kembali ke onboarding agar
            // kode bisa diverifikasi LANGSUNG di halaman yang sama (paritas).
            if ($verificationSent && session('onboarding_otp_email')) {
                return redirect()->route('onboarding')
                    ->with('status', 'Kode verifikasi 6 digit telah dikirim ke <strong>' . e($updateData['personal_email']) . '</strong>. Masukkan kode di bawah untuk memverifikasi email Anda.');
            }

            // Flash 'success' (bukan 'status') supaya muncul sebagai toast di dashboard SIDONGAN.
            return redirect()->to($this->tujuanSetelahOnboarding($system, $user))
                ->with('success', $successMessage)
                ->with('status', $successMessage);
        }

        $statusMessage = 'Profil berhasil disimpan. Silakan lengkapi data yang tersisa.';
        if ($verificationSent) {
            $statusMessage .= ' Link verifikasi email pribadi telah dikirim ke <strong>' . e($updateData['personal_email']) . '</strong> — silakan cek inbox Anda.';
        }

        return redirect()->route('onboarding')
            ->with('status', $statusMessage);
    }

    /**
     * Skip onboarding.
     */
    public function skip(Request $request): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route($this->loginRoute($request));
        }

        $system = $this->systems[$systemKey];

        Log::channel('audit')->info('Onboarding di-skip', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'system' => $systemKey,
            'missing_fields' => $this->getMissingFields($user, $systemKey),
            'timestamp' => now()->toIso8601String(),
        ]);

        // Simpan keputusan skip di DB (bertahan lintas login) + session flag.
        // Tanpa ini, user yang melewati onboarding akan dilempar ke onboarding
        // LAGI setiap kali login — tidak bisa "melewati" halaman onboarding.
        //
        // Admin Panel: skip SEMENTARA (session saja, hilang saat logout) —
        // user tetap ditanya lagi di login berikutnya sampai profil lengkap.
        // SIDONGAN: skip PERMANEN (DB) mengikuti perilaku semula.
        if ($systemKey === 'admin') {
            session(['onboarding_skipped' => true]);
        } elseif (!$user->onboarding_skipped_at) {
            $user->forceFill(['onboarding_skipped_at' => now()])->save();
            session(['onboarding_skipped' => true]);
        }

        return redirect()->to($this->tujuanSetelahOnboarding($system, $user))
            ->with('status', 'Anda bisa melengkapi profil nanti melalui menu Edit Profil.');
    }

    // =================================================================
    // Verifikasi email pribadi LANGSUNG di halaman onboarding (OTP)
    // =================================================================

    /** Batas percobaan salah sebelum kode hangus (harus minta baru). */
    private const OTP_MAX_ATTEMPTS = 5;
    /** Jeda minimal antar pengiriman kode (detik) — anti spam. */
    private const OTP_RESEND_COOLDOWN = 60;
    /** Masa berlaku kode (menit). */
    private const OTP_TTL_MINUTES = 15;

    /**
     * Generate kode OTP 6 digit & kirim SATU email gabungan berisi kode
     * (verifikasi instan di halaman onboarding) + link verifikasi
     * alternatif (fallback, berlaku 24 jam). Kode disimpan sebagai HASH
     * (bukan teks polos) di DB.
     */
    private function issueEmailOtp($user, string $email, string $verifyRoute = 'personal-email.verify'): void
    {
        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'personal_email_otp_hash' => hash('sha256', $code . $email),
            'personal_email_otp_email' => $email,
            'personal_email_otp_expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            'personal_email_otp_attempts' => 0,
            'personal_email_otp_requested_at' => now(),
        ])->save();

        // Signed URL fallback — sama seperti email link biasa (24 jam)
        $verifyUrl = \Illuminate\Support\Facades\URL::signedRoute($verifyRoute, [
            'id' => $user->id,
            'email' => $email,
        ], now()->addHours(24));

        $user->notify(new \App\Notifications\PersonalEmailOtpNotification(
            $email,
            $code,
            $verifyUrl,
            self::OTP_TTL_MINUTES
        ));
    }

    /**
     * Verifikasi kode OTP yang diketik user di halaman onboarding.
     */
    public function verifyEmailOtp(Request $request): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route($this->loginRoute($request));
        }

        $request->validate([
            'otp_code' => ['required', 'digits:6'],
        ], [
            'otp_code.digits' => 'Kode verifikasi harus 6 digit angka.',
        ]);

        $code = $request->input('otp_code');
        $email = $user->personal_email_otp_email;

        // Tidak ada kode aktif — minta kirim ulang
        if (!$email || !$user->personal_email_otp_hash || !$user->personal_email_otp_expires_at) {
            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Kode verifikasi tidak ditemukan. Silakan kirim kode baru.']);
        }

        if ($user->personal_email_otp_expires_at->isPast()) {
            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Kode verifikasi sudah kedaluwarsa. Silakan kirim kode baru.']);
        }

        if ($user->personal_email_otp_attempts >= self::OTP_MAX_ATTEMPTS) {
            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Percobaan terlalu banyak. Silakan kirim kode baru.']);
        }

        if (!hash_equals($user->personal_email_otp_hash, hash('sha256', $code . $email))) {
            $user->forceFill([
                'personal_email_otp_attempts' => $user->personal_email_otp_attempts + 1,
            ])->save();

            $sisa = self::OTP_MAX_ATTEMPTS - ($user->personal_email_otp_attempts + 1);
            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Kode salah. Sisa percobaan: ' . max(0, $sisa) . '.']);
        }

        // Kode benar → simpan email & tandai terverifikasi, bersihkan OTP
        if ($user->personal_email !== $email) {
            // Email OTP berbeda dari yang di DB (kasus jarang): ikuti email OTP
            $user->personal_email = $email;
        }
        $user->personal_email_verified_at = now();
        $user->personal_email_otp_hash = null;
        $user->personal_email_otp_email = null;
        $user->personal_email_otp_expires_at = null;
        $user->personal_email_otp_attempts = 0;
        $user->save();

        // Email terverifikasi → bersihkan status pending di session
        session()->forget(['onboarding_otp_email', 'pending_personal_email']);
        session()->forget('onboarding_skipped');

        Log::channel('audit')->info('Email pribadi terverifikasi via OTP onboarding', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'system' => $systemKey,
            'personal_email' => $email,
            'timestamp' => now()->toIso8601String(),
        ]);

        return redirect()->route('onboarding')
            ->with('success', 'Email pribadi <strong>' . e($email) . '</strong> berhasil diverifikasi!');
    }

    /**
     * Cek status verifikasi email pribadi (polling dari halaman onboarding).
     *
     * Dipanggil berkala oleh panel "Cek Email" supaya panel otomatis hilang
     * saat email diverifikasi lewat link di email — yang bisa diklik dari
     * perangkat/tab lain. Alternatifnya user harus refresh manual; ini yang
     * membuat alur link fallback terasa real-time.
     */
    public function emailStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return response()->json(['verified' => false, 'authenticated' => false], 401);
        }

        // Konsisten dengan verifyEmailOtp: kalau ternyata sudah terverifikasi
        // di DB (via link email), bersihkan status pending dari session dan
        // siapkan flash sukses — akan tampil saat halaman di-reload oleh client.
        if ($user->hasVerifiedPersonalEmail()) {
            session()->forget(['pending_personal_email', 'onboarding_otp_email']);
            session()->flash('status', 'Email pribadi <strong>' . e($user->personal_email) . '</strong> berhasil diverifikasi!');
        }

        return response()->json([
            'authenticated' => true,
            'verified' => $user->hasVerifiedPersonalEmail(),
            'redirect' => $user->hasVerifiedPersonalEmail()
                ? route('onboarding')
                : null,
        ]);
    }

    /**
     * Kirim ulang kode OTP (dengan cooldown anti spam).
     */
    public function resendEmailOtp(Request $request): RedirectResponse
    {
        [$user, $systemKey] = $this->getUserAndSystem();

        if (!$user) {
            return redirect()->route($this->loginRoute($request));
        }

        // Urutan sumber email: OTP aktif → pending di session (alur setup lama
        // menyimpan email DI SESSION saja sebelum terverifikasi) → DB.
        $email = $user->personal_email_otp_email
            ?: session('pending_personal_email')
            ?: $user->personal_email;
        if (!$email || !ProfileFields::isFilled($email)) {
            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Tidak ada email yang menunggu verifikasi.']);
        }

        // Cooldown: kode terakhir baru saja dikirim
        if ($user->personal_email_otp_requested_at
            && $user->personal_email_otp_requested_at->diffInSeconds(now()) < self::OTP_RESEND_COOLDOWN) {
            $sisa = self::OTP_RESEND_COOLDOWN - $user->personal_email_otp_requested_at->diffInSeconds(now());
            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Tunggu ' . max(1, (int) ceil($sisa)) . ' detik sebelum meminta kode baru.']);
        }

        try {
            // Signed URL fallback mengikuti sistem asal — konsisten dengan store().
            $verifyRoute = $systemKey === 'sidongan'
                ? 'sidongan.personal-email.verify'
                : 'personal-email.verify';

            $this->issueEmailOtp($user, $email, $verifyRoute);
            session(['onboarding_otp_email' => $email]);
        } catch (\Throwable $e) {
            Log::channel('audit')->warning('Gagal kirim ulang OTP email pribadi', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('onboarding')
                ->withErrors(['otp_code' => 'Gagal mengirim kode. Coba lagi sebentar.']);
        }

        return redirect()->route('onboarding')
            ->with('status', 'Kode verifikasi baru telah dikirim ke <strong>' . e($email) . '</strong>.');
    }

    /**
     * Get missing fields based on system requirements.
     */
    private function getMissingFields($user, string $systemKey): array
    {
        $missing = [];

        // Field pemblokir & placeholder memakai aturan terpusat di
        // App\Support\ProfileFields (case-insensitive) — identik dengan SIEDA.
        if (!ProfileFields::isFilled($user->phone_number ?? null)) {
            $missing[] = 'phone_number';
        }

        if (!ProfileFields::isFilled($user->personal_email ?? null)) {
            $missing[] = 'personal_email';
        }

        if (empty($user->avatar)) {
            $missing[] = 'avatar';
        }

        return $missing;
    }

    /**
     * Get completion percentage.
     */
    private function getCompletionPercentage($user, string $systemKey): int
    {
        $total = 3;
        $completed = 0;

        if (!empty($user->phone_number)) $completed++;
        if (!empty($user->personal_email)) $completed++;
        if (!empty($user->avatar)) $completed++;

        return (int) round(($completed / $total) * 100);
    }

    /**
     * Static helper: check if profile is blocking (for middleware).
     */
    public static function isProfileBlocking($user): bool
    {
        return ProfileFields::missingBlocking($user) !== [];
    }

    /**
     * Static helper: get missing fields (for middleware).
     */
    public static function getMissingFieldsStatic($user): array
    {
        $missing = [];
        if (!ProfileFields::isFilled($user->phone_number ?? null)) $missing[] = 'phone_number';
        if (!ProfileFields::isFilled($user->personal_email ?? null)) $missing[] = 'personal_email';
        if (empty($user->avatar)) $missing[] = 'avatar';
        return $missing;
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
