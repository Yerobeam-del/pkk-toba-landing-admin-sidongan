{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     Standalone Onboarding — detects system from login source
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil — {{ $system['name'] }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" id="favicon" href="{{ asset($system['logo']) }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Tema 3-mode (system/light/dark): mengikuti preferensi sistem asal user —
         onboarding dipakai lintas aplikasi (SIDONGAN & Admin Panel). Class diterapkan
         SEBELUM CSS render agar tidak flicker. --}}
    <script>
    (function(){
        var k='{{ $systemKey === 'sidongan' ? 'sidongan-theme' : 'admin-dark-mode' }}';
        var s=localStorage.getItem(k);
        var mq=window.matchMedia?window.matchMedia('(prefers-color-scheme:dark)'):null;
        var dark;
        if(k==='admin-dark-mode'){ dark=(s==='true')||(s!=='false'&&mq&&mq.matches); }
        else { dark=(s==='dark')||(s!=='light'&&s!=='dark'&&mq&&mq.matches); }
        if(dark){document.documentElement.classList.add('dark-mode');}
        // Mode system (tanpa pilihan eksplisit): ikuti perangkat secara live bila tema berubah saat halaman terbuka
        if(mq&&mq.addEventListener){
            var inSystemMode=function(){
                var v=localStorage.getItem(k);
                return k==='admin-dark-mode' ? (v===null) : (v!=='light'&&v!=='dark');
            };
            mq.addEventListener('change',function(e){
                if(inSystemMode()){
                    document.documentElement.classList.toggle('dark-mode',e.matches);
                }
            });
        }
        // Favicon mengikuti tema aktif — pasangan logo per sistem asal
        window.addEventListener('DOMContentLoaded',function(){
            var fav=document.getElementById('favicon');
            if(!fav)return;
            @if($systemKey === 'sidongan')
                var LIGHT='{{ asset('assets/sidongan/images/Logo-SIDONGAN-ThemeAware.svg') }}';
                var DARK='{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}';
            @else
                var LIGHT='{{ asset('assets/admin/images/Logo_Admin-Panel.svg') }}';
                var DARK='{{ asset('assets/admin/images/Logo_Admin-Panel-White.svg') }}';
            @endif
            var apply=function(){fav.href=document.documentElement.classList.contains('dark-mode')?DARK:LIGHT;};
            apply();
            document.addEventListener('sidongan-theme-changed',apply);
            if(mq&&mq.addEventListener){mq.addEventListener('change',apply);}
        });
    })();
    </script>

    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">

    <style>
        :root {
            --gradient-start: {{ $system['color_start'] }};
            --gradient-mid: {{ $system['color_mid'] }};
            --gradient-end: {{ $system['color_end'] }};
        }
    </style>

    {{-- Tanda "Opsional" — foto profil tidak wajib, bisa dilengkapi nanti lewat
         menu Edit Profil (lihat ProfileFields: avatar tidak pernah menghalangi
         akses aplikasi). --}}
    <style>
        .ob-step-tag, .ob-avatar-opt {
            display: inline-block;
            margin-left: 0.45rem;
            padding: 0.12rem 0.5rem;
            border-radius: 99px;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            vertical-align: 2px;
        }
        /* di panel kiri (latar gradasi gelap) */
        .ob-step-tag {
            background: rgba(255,255,255,0.16);
            color: #fff;
            border: 1px dashed rgba(255,255,255,0.55);
        }
        /* di panel kanan (latar putih) */
        .ob-avatar-opt {
            background: #f0fdf4;
            color: #15803d;
            border: 1px dashed #6ee7b7;
        }
    </style>
</head>
<body>
    <div class="ob-split">
        {{-- ===== LEFT PANEL: Welcome & Info ===== --}}
        <div class="ob-left">
            <div class="ob-left-content">
                {{-- Logo --}}
                <div class="ob-logo">
                    <img src="{{ asset($system['logo']) }}" alt="Logo {{ $system['name'] }}" width="56" height="56">
                </div>

                {{-- Welcome --}}
                <h1 class="ob-welcome">Selamat Datang,<br>{{ $user->name }}!</h1>
                <p class="ob-subtitle">Selamat datang di {{ $system['full_name'] }} — {{ $system['org'] }}. Sebelum mulai, yuk lengkapi profil Anda agar pengalaman lebih optimal.</p>

                {{-- Steps --}}
                <div class="ob-steps">
                    <div class="ob-step @if(!empty($user->avatar)) ob-step--done @endif">
                        <div class="ob-step-num">
                            @if(!empty($user->avatar))
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                1
                            @endif
                        </div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Upload Foto Profil <span class="ob-step-tag">Opsional</span></span>
                            <span class="ob-step-desc">Agar dikenali oleh tim Anda — bisa dilewati dulu</span>
                        </div>
                    </div>

                    <div class="ob-step @if(!empty($user->phone_number)) ob-step--done @endif">
                        <div class="ob-step-num">
                            @if(!empty($user->phone_number))
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                2
                            @endif
                        </div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Nomor Telepon</span>
                            <span class="ob-step-desc">Kontak yang bisa dihubungi</span>
                        </div>
                    </div>

                    <div class="ob-step @if(!empty($user->personal_email)) ob-step--done @endif">
                        <div class="ob-step-num">
                            @if(!empty($user->personal_email))
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            @else
                                3
                            @endif
                        </div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Email Pribadi</span>
                            <span class="ob-step-desc">Untuk reset password</span>
                        </div>
                    </div>
                </div>

                {{-- Tips --}}
                <div class="ob-tips">
                    <div class="ob-tips-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a7 7 0 0 1 7 7c0 2.38-1.19 4.47-3 5.74V17a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2.26C6.19 13.47 5 11.38 5 9a7 7 0 0 1 7-7z"/><line x1="9" y1="21" x2="15" y2="21"/></svg>
                        Tips
                    </div>
                    <p>Anda bisa melengkapi profil kapan saja melalui menu <strong>Edit Profil</strong> di dashboard.</p>
                </div>

                {{-- Footer --}}
                <div class="ob-left-footer">
                    <span>&copy; {{ date('Y') }} TP-PKK Kabupaten Toba</span>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT PANEL: Form ===== --}}
        <div class="ob-right">
            <div class="ob-right-content">
                {{-- Progress Header --}}
                <div class="ob-progress-header">
                    <div class="ob-progress-text">
                        <h2>Lengkapi Profil Anda</h2>
                        <span class="ob-progress-label">{{ $completionPercentage }}% selesai</span>
                    </div>
                    <div class="ob-progress-bar">
                        <div class="ob-progress-fill" style="width: {{ $completionPercentage }}%"></div>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session('status') || session('success'))
                    <div class="ob-alert ob-alert--success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <span>{!! session('success') ?: session('status') !!}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="ob-alert ob-alert--error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('onboarding.store') }}" enctype="multipart/form-data" class="ob-form" id="onboardingForm" data-validate>
                    @csrf

                    {{-- Avatar Upload --}}
                    <div class="ob-avatar-section">
                        <div class="ob-avatar-preview" id="avatarPreview">
                            @if(!empty($user->avatar))
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" id="avatarImg">
                            @else
                                <div class="ob-avatar-placeholder" id="avatarPlaceholder">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                </div>
                            @endif
                            <label for="avatarInput" class="ob-avatar-btn" title="Upload Foto">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            </label>
                        </div>
                        <div class="ob-avatar-info">
                            <span class="ob-avatar-label">Foto Profil <span class="ob-avatar-opt">Opsional</span></span>
                            <span class="ob-avatar-hint">Boleh kosong — Anda bisa langsung lanjut tanpa foto. Tambahkan nanti lewat menu Edit Profil (JPG/PNG, maks 2MB)</span>
                            <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png" class="ob-file-input">
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div class="ob-field">
                        <label class="ob-field-label" for="phone_number">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            Nomor Telepon
                            @if(!in_array('phone_number', $missingFields))
                                <span class="ob-badge ob-badge--done">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Terisi
                                </span>
                            @else
                                <span class="ob-badge ob-badge--needed">Perlu diisi</span>
                            @endif
                        </label>
                        @if(in_array('phone_number', $missingFields))
                            <div class="ob-input-wrap">
                                <span class="ob-input-prefix">+62</span>
                                <input
                                    type="tel"
                                    id="phone_number"
                                    name="phone_number"
                                    value="{{ old('phone_number', $user->phone_number) }}"
                                    placeholder="812 3456 7890"
                                    class="ob-input"
                                    required
                                >
                            </div>
                            <span class="ob-field-hint">Masukkan nomor TANPA awalan +62 (contoh: 812 3456 7890)</span>
                        @else
                            <div class="ob-field-filled">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span>{{ $user->phone_number }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Personal Email --}}
                    <div class="ob-field">
                        <label class="ob-field-label" for="personal_email">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            Email Pribadi
                            @if(!in_array('personal_email', $missingFields))
                                <span class="ob-badge ob-badge--done">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Terisi
                                </span>
                            @else
                                <span class="ob-badge ob-badge--needed">Perlu diisi</span>
                            @endif
                        </label>
                        @if(in_array('personal_email', $missingFields))
                            <input
                                type="email"
                                id="personal_email"
                                name="personal_email"
                                value="{{ old('personal_email', $user->personal_email) }}"
                                placeholder="namaketua@gmail.com"
                                class="ob-input"
                                required
                            >
                            <span class="ob-field-hint">Email aktif untuk reset password (Gmail, Yahoo, dll)</span>
                        @else
                            <div class="ob-field-filled">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <span>{{ $user->personal_email }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="ob-actions" @if(!empty($otpEmail)) style="display:none" @endif>
                        <button type="submit" class="ob-btn-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Simpan & Lanjutkan
                        </button>
                        {{-- Skip tersedia di semua sistem: SIDONGAN (skip
                             permanen) dan Admin Panel (skip sementara — user
                             tetap ditanya lagi di login berikutnya sampai
                             profilnya lengkap, email tetap bisa diatur via
                             menu Profil). --}}
                        <a href="{{ route('onboarding.skip') }}" class="ob-btn-skip">
                            Lewati — nanti saja
                        </a>
                    </div>
                </form>

                {{-- ============================================================
                     Panel Verifikasi Email via OTP (Admin Panel & SIDONGAN)
                     Muncul menggantikan form setelah profil disimpan dan kode
                     6 digit terkirim ke email pribadi. Verifikasi langsung di
                     halaman ini — link di email tetap berlaku sebagai fallback.
                     ============================================================ --}}
                @if(!empty($otpEmail))
                <div id="otpPanel" style="margin-top:1.5rem;padding:1.5rem;border:1px solid rgba(0,0,0,0.08);border-radius:16px;background:#f8fafc">
                    <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.4rem">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gradient-mid, #0ea5e9)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <h3 style="margin:0;font-size:1.05rem;font-weight:700">Cek Email Anda</h3>
                    </div>
                    @if(!empty($otpActive))
                    <p style="margin:0 0 1rem;font-size:0.88rem;color:#64748b;line-height:1.6">
                        Kode 6 digit telah dikirim ke <strong>{{ $otpEmail }}</strong>.
                        Masukkan kode di bawah untuk langsung memverifikasi email — atau klik link verifikasi di email Anda.
                    </p>
                    @else
                    <p style="margin:0 0 1rem;font-size:0.88rem;color:#64748b;line-height:1.6">
                        Email pribadi <strong>{{ $otpEmail }}</strong> terdaftar tetapi belum terverifikasi.
                        Klik tombol di bawah — kami akan mengirim kode verifikasi 6 digit ke email tersebut.
                    </p>
                    @endif

                    @if(session('status') || session('success'))
                        <div style="margin-bottom:1rem;padding:0.7rem 1rem;border-radius:10px;background:#ecfdf5;border:1px solid #6ee7b7;color:#047857;font-size:0.85rem">{!! session('success') ?: session('status') !!}</div>
                    @endif
                    @if($errors->has('otp_code'))
                        <div style="margin-bottom:1rem;padding:0.7rem 1rem;border-radius:10px;background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;font-size:0.85rem">{{ $errors->first('otp_code') }}</div>
                    @endif

                    @if(!empty($otpActive))
                    <form method="POST" action="{{ route('onboarding.verify-email') }}" class="js-otp-verify-form">
                        @csrf
                        <div style="display:flex;gap:0.75rem;align-items:stretch;flex-wrap:wrap">
                            <input
                                type="text"
                                name="otp_code"
                                class="js-otp-verify-input"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                maxlength="6"
                                pattern="[0-9]{6}"
                                placeholder="______"
                                required
                                style="flex:1;min-width:180px;letter-spacing:0.5em;text-align:center;font-size:1.3rem;font-weight:700;padding:0.7rem 0.5rem;border:1.5px solid #cbd5e1;border-radius:12px;outline:none"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                            >
                            <button type="submit" class="ob-btn-primary js-otp-verify-btn" style="flex-shrink:0">
                                Verifikasi
                            </button>
                        </div>
                    </form>

                    {{-- Auto-submit: begitu 6 digit terisi (ketik atau paste),
                         form verifikasi langsung dikirim tanpa perlu klik tombol.
                         Guard `terkirim` mencegah kirim ganda saat transisi halaman;
                         tombol tetap tersedia sebagai jalur manual (autofill yang
                         tidak memicu event input, aksesibilitas). --}}
                    <script>
                    (function(){
                        var form = document.querySelector('.js-otp-verify-form');
                        var input = form && form.querySelector('.js-otp-verify-input');
                        var btn = form && form.querySelector('.js-otp-verify-btn');
                        if (!form || !input || !btn) return;

                        var terkirim = false;

                        input.addEventListener('input', function () {
                            // Sanitasi digit (dobel dengan oninput inline, untuk kasus paste)
                            var digits = input.value.replace(/[^0-9]/g, '');
                            if (input.value !== digits) input.value = digits;

                            if (!terkirim && digits.length === 6) {
                                terkirim = true;
                                btn.disabled = true;
                                btn.textContent = 'Memverifikasi...';
                                form.submit();
                            }
                        });

                        // Fokus memilih seluruh isi — memudahkan ketik ulang kode baru
                        input.addEventListener('focus', function () {
                            try { input.select(); } catch (e) {}
                        });
                    })();
                    </script>

                    <form method="POST" action="{{ route('onboarding.resend-email-otp') }}" style="margin-top:0.9rem">
                        @csrf
                        <button type="submit" class="js-otp-resend-btn" style="background:none;border:none;color:var(--gradient-mid, #0ea5e9);font-size:0.85rem;font-weight:600;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:0.35rem">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            <span class="js-otp-resend-label">Kirim ulang kode</span>
                        </button>
                    </form>
                    @else
                    {{-- Email pending dari alur lain (mis. setup lama): kode belum
                         pernah dikirim dari onboarding — tombol kirim kode dulu. --}}
                    <form method="POST" action="{{ route('onboarding.resend-email-otp') }}">
                        @csrf
                        <button type="submit" class="ob-btn-primary js-otp-resend-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                            <span class="js-otp-resend-label">Kirim kode verifikasi</span>
                        </button>
                    </form>
                    @endif

                    <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px dashed rgba(0,0,0,0.08)">
                        <a href="{{ route($system['dashboard_route']) }}" style="font-size:0.85rem;color:#64748b;text-decoration:none;display:inline-flex;align-items:center;gap:0.3rem">Verifikasi nanti &amp; masuk dashboard<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
                    </div>

                    {{-- Auto-refresh panel "Cek Email": polling status verifikasi
                         berkala — panel otomatis menyegarkan halaman (panel hilang,
                         flash sukses tampil) begitu email diverifikasi lewat link
                         di email, dari perangkat/tab lain. Jeda dinaikkan otomatis
                         kalau tab tidak aktif (hemat request), dan dicek ulang
                         segera saat tab di-fokuskan lagi. --}}
                    <script>
                    (function(){
                        if (!document.getElementById('otpPanel')) return;

                        var url = '{{ route('onboarding.email-status') }}';
                        var INTERVAL = 10000;       // polling normal: 10 detik
                        var BACKOFF = 30000;        // tab tidak aktif: 30 detik
                        var busy = false;
                        var timer = null;

                        function check() {
                            if (busy || document.hidden) return;
                            busy = true;
                            fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                                .then(function (r) { return r.ok ? r.json() : null; })
                                .then(function (data) {
                                    if (data && data.verified && data.redirect) {
                                        // Sesi habis saat polling → biarkan redirect
                                        // membawa ke login (perilaku standar).
                                        window.location.href = data.redirect;
                                    }
                                })
                                .catch(function () { /* jaringan gagal: coba lagi nanti */ })
                                .finally(function () { busy = false; });
                        }

                        function schedule() {
                            if (timer) clearInterval(timer);
                            timer = setInterval(check, document.hidden ? BACKOFF : INTERVAL);
                        }

                        document.addEventListener('visibilitychange', function () {
                            // Tab baru saja aktif kembali — kemungkinan user membuka
                            // email di tab lain lalu kembali; cek segera.
                            if (!document.hidden) check();
                            schedule();
                        });

                        schedule();
                    })();
                    </script>

                    {{-- Countdown cooldown kirim ulang kode: sinkron dengan
                         OTP_RESEND_COOLDOWN di OnboardingController (60 detik).
                         Sisa awal dihitung SERVER (otpResendIn, dari DB) — bukan
                         selalu 60 — supaya reload halaman tidak me-reset hitungan
                         dan timer selalu cocok dengan validasi server-side. --}}
                    <script>
                    (function(){
                        var sisa = {{ (int) ($otpResendIn ?? 0) }};
                        var btns = document.querySelectorAll('.js-otp-resend-btn');
                        var labels = document.querySelectorAll('.js-otp-resend-label');
                        if (!btns.length || sisa <= 0) return;

                        // Label default tiap tombol dipertahankan apa adanya
                        var defaultLabels = Array.prototype.map.call(labels, function (l) { return l.textContent; });
                        var timer = null;

                        function setDisabled(disabled) {
                            btns.forEach(function (b) {
                                b.disabled = disabled;
                                b.style.opacity = disabled ? '0.55' : '';
                                b.style.cursor = disabled ? 'not-allowed' : 'pointer';
                            });
                        }

                        function tick() {
                            if (sisa > 0) {
                                setDisabled(true);
                                labels.forEach(function (l, i) {
                                    l.textContent = defaultLabels[i] + ' (' + sisa + 's)';
                                });
                                sisa--;
                            } else {
                                setDisabled(false);
                                labels.forEach(function (l, i) {
                                    l.textContent = defaultLabels[i];
                                });
                                if (timer) { clearInterval(timer); timer = null; }
                            }
                        }

                        tick();
                        timer = setInterval(tick, 1000);
                    })();
                    </script>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Avatar Preview Script --}}
    <script>
    (function(){
        var input = document.getElementById('avatarInput');
        var preview = document.getElementById('avatarPreview');
        if (!input || !preview) return;

        input.addEventListener('change', function(e){
            var file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                input.value = '';
                return;
            }
            var reader = new FileReader();
            reader.onload = function(ev){
                var img = document.getElementById('avatarImg');
                var placeholder = document.getElementById('avatarPlaceholder');
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'avatarImg';
                    img.alt = 'Avatar';
                    if (placeholder) placeholder.style.display = 'none';
                    preview.insertBefore(img, preview.querySelector('.ob-avatar-btn'));
                }
                img.src = ev.target.result;
                img.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    })();
    </script>

    {{-- Validasi realtime (email, telepon) — identik dengan onboarding SIEDA --}}
    <script src="{{ asset('assets/shared/js/auth-form-validation.js') }}"></script>
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
