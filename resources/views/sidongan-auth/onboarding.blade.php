{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lengkapi Profil - SIDONGAN PKK Kabupaten Toba</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('assets/sidongan-auth/css/auth-onboarding.css') }}">
</head>
<body>
    <div class="ob-split">
        {{-- ===== LEFT PANEL: Welcome & Info ===== --}}
        <div class="ob-left">
            <div class="ob-left-content">
                {{-- Logo --}}
                <div class="ob-logo">
                    <img src="{{ asset('assets/sidongan/images/Logo-SIDONGAN-white.svg') }}" alt="Logo SIDONGAN" width="56" height="56">
                </div>

                {{-- Welcome --}}
                <h1 class="ob-welcome">Selamat Datang,<br>{{ $user->name }}! 👋</h1>
                <p class="ob-subtitle">Selamat datang di SIDONGAN PKK Kabupaten Toba. Sebelum mulai, yuk lengkapi profil Anda agar pengalaman lebih optimal.</p>

                {{-- Steps --}}
                <div class="ob-steps">
                    <div class="ob-step @if(!empty($user->avatar)) ob-step--done @endif">
                        <div class="ob-step-num">
                            @if(!empty($user->avatar))
                                <i class="fas fa-check"></i>
                            @else
                                1
                            @endif
                        </div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Upload Foto Profil</span>
                            <span class="ob-step-desc">Agar dikenali oleh tim Anda</span>
                        </div>
                    </div>

                    <div class="ob-step @if(!empty($user->phone_number)) ob-step--done @endif">
                        <div class="ob-step-num">
                            @if(!empty($user->phone_number))
                                <i class="fas fa-check"></i>
                            @else
                                2
                            @endif
                        </div>
                        <div class="ob-step-info">
                            <span class="ob-step-title">Nomor Telepon</span>
                            <span class="ob-step-desc">Untuk notifikasi WhatsApp</span>
                        </div>
                    </div>

                    <div class="ob-step @if(!empty($user->personal_email)) ob-step--done @endif">
                        <div class="ob-step-num">
                            @if(!empty($user->personal_email))
                                <i class="fas fa-check"></i>
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
                        <i class="fas fa-lightbulb"></i> Tips
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
                @if(session('status'))
                    <div class="ob-alert ob-alert--success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="ob-alert ob-alert--error">
                        <i class="fas fa-exclamation-circle"></i>
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
                <form method="POST" action="{{ route('sidongan.onboarding.store') }}" enctype="multipart/form-data" class="ob-form" id="onboardingForm">
                    @csrf

                    {{-- Avatar Upload --}}
                    <div class="ob-avatar-section">
                        <div class="ob-avatar-preview" id="avatarPreview">
                            @if(!empty($user->avatar))
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" id="avatarImg">
                            @else
                                <div class="ob-avatar-placeholder" id="avatarPlaceholder">
                                    <i class="fas fa-camera"></i>
                                </div>
                            @endif
                            <label for="avatarInput" class="ob-avatar-btn" title="Upload Foto">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                        <div class="ob-avatar-info">
                            <span class="ob-avatar-label">Foto Profil</span>
                            <span class="ob-avatar-hint">JPG/PNG, maks 2MB</span>
                            <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png" class="ob-file-input">
                        </div>
                    </div>

                    {{-- Phone Number --}}
                    <div class="ob-field">
                        <label class="ob-field-label" for="phone_number">
                            <i class="fas fa-phone"></i>
                            Nomor Telepon
                            @if(!in_array('phone_number', $missingFields))
                                <span class="ob-badge ob-badge--done"><i class="fas fa-check"></i> Terisi</span>
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
                            <span class="ob-field-hint">Nomor WhatsApp aktif untuk notifikasi surat masuk/keluar</span>
                        @else
                            <div class="ob-field-filled">
                                <i class="fas fa-check-circle"></i>
                                <span>{{ $user->phone_number }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Personal Email --}}
                    <div class="ob-field">
                        <label class="ob-field-label" for="personal_email">
                            <i class="fas fa-envelope"></i>
                            Email Pribadi
                            @if(!in_array('personal_email', $missingFields))
                                <span class="ob-badge ob-badge--done"><i class="fas fa-check"></i> Terisi</span>
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
                                <i class="fas fa-check-circle"></i>
                                <span>{{ $user->personal_email }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="ob-actions">
                        <button type="submit" class="ob-btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan & Lanjutkan
                        </button>
                        <a href="{{ route('sidongan.onboarding.skip') }}" class="ob-btn-skip">
                            Lewati — nanti saja
                        </a>
                    </div>
                </form>
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
                    preview.querySelector('.ob-avatar-btn').insertAdjacentHTML('beforebegin', '');
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
</body>
</html>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
