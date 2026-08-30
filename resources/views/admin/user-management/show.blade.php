{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Detail Akun')
@section('page-title', 'Detail Akun')

@section('content')

<div class="u-header-row-plain">
    <div>
        <h1 class="u-page-title">Detail Akun</h1>
        <p class="u-muted">Informasi akun {{ $user->name }}</p>
    </div>
    <div class="u-a64">
        
        {{-- Tombol Salin Kredensial --}}
        <button type="button" onclick="copyAccountCredentials()" id="copyCredentialsBtn" class="btn" style="display:inline-flex;align-items:center;gap:0.5rem;background:#f0fdf4;color:#166534;padding:0.5rem 1rem;border-radius:8px;border:1px solid #bbf7d0;cursor:pointer;font-weight:600;font-family:inherit;transition:all 0.2s">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            <span>Salin Kredensial</span>
        </button>

        {{-- Hanya tampilkan tombol Edit & Reset Password jika user yang sedang login adalah Super Admin --}}
        @if(auth()->user()->sidongan_role === 'super_admin')
            <a href="{{ route('admin.user-management.edit', $user) }}" class="btn btn-primary u-inline-flex-center-gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Akun
            </a>
            <button type="button" data-reset-password-id="{{ $user->id }}" data-reset-password-name="{{ addslashes($user->name) }}" class="btn btn-secondary" style="display:inline-flex;align-items:center;gap:0.5rem;background:#f1f5f9;color:#475569;padding:0.5rem 1rem;border-radius:8px;border:1px solid #e2e8f0;cursor:pointer;font-weight:600;font-family:inherit;transition:all 0.2s">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Reset Password
            </button>
        @endif
        
        <x-admin.back-button :href="route('admin.user-management.index')" />
    </div>
</div>

<div class="card u-a71">
    <div style="display:grid;gap:1rem;max-width:600px">
        <div class="u-row-divider-border">
            <span class="u-muted-plain">Nama</span>
            <span class="u-semibold">{{ $user->name }}</span>
        </div>
        <div class="u-row-divider-border">
            <span class="u-muted-plain">Email</span>
            <span class="u-semibold">{{ $user->email }}</span>
        </div>
        <div class="u-row-divider-border">
            <span class="u-muted-plain">Telepon</span>
            <span class="u-semibold">{{ $user->phone_number ?? '-' }}</span>
        </div>
        <div class="u-row-divider-border">
            <span class="u-muted-plain">Status Email</span>
            <span>{{ $user->email_verified_at ? '✓ Terverifikasi' : '⚠ Belum Verifikasi' }}</span>
        </div>
        
        {{-- SIDONGAN Role --}}
        @if($user->sidongan_role)
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);background:rgba(20,184,166,0.05);padding:0.75rem 1rem;border-radius:8px">
            <span class="u-muted-plain">Peran di SIDONGAN</span>
            <span style="font-weight:600;color:#0d9488">
                <i class="fas fa-user-tag" style="margin-right:0.5rem"></i>
                {{ $user->sidongan_role_name }}
            </span>
        </div>
        @endif
        
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0">
            <span class="u-muted-plain">Dibuat</span>
            <span class="u-semibold">{{ $user->created_at->translatedFormat('d F Y, H:i') }}</span>
        </div>
    </div>
    
    <div class="u-a73">
        <h3 class="u-a74">Aplikasi yang Diakses</h3>
        @if($user->applications->count() > 0)
        <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
            @foreach($user->applications as $app)
            <span style="background:#e0f2fe;color:#0369a1;padding:0.4rem 0.8rem;border-radius:20px;font-size:0.85rem">
                {{ $app->name }}
                @if($app->short_name === 'sidongan' && $user->sidongan_role)
                    <span style="margin-left:0.5rem;font-size:0.75rem;opacity:0.8">({{ $user->sidongan_role_name }})</span>
                @endif
            </span>
            @endforeach
        </div>
        @else
        <p class="u-muted-plain">Belum ada akses aplikasi</p>
        @endif
    </div>

    {{-- BAGIAN BARU: Info Admin Panel --}}
    <div class="u-a73">
        <h3 class="u-a74">Info Admin Panel</h3>
        
        @if($user->role)
            <div style="display:grid;gap:1rem">
                {{-- Role Badge --}}
                <div style="background:#f8fafc;padding:1rem;border-radius:8px">
                    <span style="font-size:0.85rem;color:var(--text-muted);display:block;margin-bottom:0.5rem">Role Admin Panel</span>
                    <span style="background:var(--primary);color:#fff;padding:4px 10px;border-radius:20px;font-size:0.85rem;font-weight:600">
                        {{ $user->role->display_name }}
                    </span>
                </div>

                {{-- Permissions List (Hanya muncul jika role adalah Anggota) --}}
                @if($user->role->name === 'anggota')
                <div>
                    <span style="font-size:0.85rem;color:var(--text-muted);display:block;margin-bottom:0.5rem">Permission Akses</span>
                    <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
                        @forelse($user->role->permissions as $permission)
                            <span style="background:#e0f2fe;color:#0369a1;padding:4px 8px;border-radius:12px;font-size:0.75rem">
                                {{ $permission->display_name }}
                            </span>
                        @empty
                            <span style="color:var(--text-muted);font-size:0.9rem">Belum ada permission</span>
                        @endforelse
                    </div>
                </div>
                @endif
            </div>
        @else
            <span class="u-muted-plain">Belum ada role yang ditetapkan</span>
        @endif
    </div>

    {{-- Info Akses SIEDA --}}
    @if($user->sieda_role)
    <div class="u-a73">
        <h3 class="u-a74">Info Akses SIEDA</h3>
        
        <div style="background:#f0fdf4;padding:1rem;border-radius:8px;border-left:4px solid #22c55e">
            <div class="u-grid-gap-3">
                {{-- Role SIEDA --}}
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span class="u-text-muted-sm">Role di SIEDA</span>
                    <span style="background:#22c55e;color:#fff;padding:4px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;text-transform:capitalize">
                        {{ $user->sieda_role }}
                    </span>
                </div>

                {{-- Kecamatan --}}
                @if($user->sieda_kecamatan)
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:0.5rem;border-top:1px dashed #bbf7d0">
                    <span class="u-text-muted-sm">Kecamatan</span>
                    <span style="font-weight:600;color:#166534">
                        <i class="fas fa-map-marker-alt" style="margin-right:0.5rem;color:#22c55e"></i>
                        {{ $kecamatan ? $kecamatan->name : $user->sieda_kecamatan }}
                    </span>
                </div>
                @endif

                {{-- Kelurahan/Desa --}}
                @if($user->sieda_kelurahan)
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:0.5rem">
                    <span class="u-text-muted-sm">Kelurahan/Desa</span>
                    <span style="font-weight:600;color:#166534">
                        <i class="fas fa-location-dot" style="margin-right:0.5rem;color:#22c55e"></i>
                        {{ $kelurahan ? $kelurahan->name : $user->sieda_kelurahan }}
                    </span>
                </div>
                @endif

                {{-- Status Sinkronisasi --}}
                <div style="margin-top:0.5rem;padding-top:0.75rem;border-top:1px solid #bbf7d0">
                    <span style="font-size:0.75rem;color:#166534;display:flex;align-items:center;gap:0.5rem">
                        <i class="fas fa-check-circle"></i>
                        Tersinkronisasi dengan database SIEDA
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function copyAccountCredentials() {
        var name = {{ json_encode($user->name) }};
        var email = {{ json_encode($user->email) }};
        var loginUrl = window.location.origin;

        var text = 'Halo ' + name + ',\n\n' +
            'Akun Anda di Admin Panel PKK Kabupaten Toba sudah dibuat.\n' +
            'Berikut kredensial login Anda:\n\n' +
            'Email    : ' + email + '\n' +
            'Password : (sesuai yang dibuat saat pembuatan akun)\n\n' +
            'Silakan login di: ' + loginUrl + '\n' +
            'Ganti password setelah login pertama kali untuk keamanan.\n\n' +
            'Terima kasih,';

        robustCopy(text);
    }

    function robustCopy(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                showCopySuccess();
            }).catch(function() {
                textareaCopy(text);
            });
        } else {
            textareaCopy(text);
        }
    }

    function textareaCopy(text) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        ta.style.top = '0';
        ta.style.width = '1px';
        ta.style.height = '1px';
        ta.style.opacity = '0.01';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            var ok = document.execCommand('copy');
            document.body.removeChild(ta);
            if (ok) {
                showCopySuccess();
            } else {
                showCopyManualModal(text);
            }
        } catch(e) {
            document.body.removeChild(ta);
            showCopyManualModal(text);
        }
    }

    function showCopySuccess() {
        var btn = document.getElementById('copyCredentialsBtn');
        if (btn) {
            var span = btn.querySelector('span');
            if (span) span.textContent = '✓ Tersalin!';
            btn.style.background = '#dcfce7';
            btn.style.borderColor = '#86efac';
            btn.style.color = '#166534';
            setTimeout(function() {
                if (span) span.textContent = 'Salin Kredensial';
                btn.style.background = '#f0fdf4';
                btn.style.borderColor = '#bbf7d0';
                btn.style.color = '#166534';
            }, 2000);
        }
        if (typeof Toast !== 'undefined' && Toast.success) {
            Toast.success('Kredensial berhasil disalin ke clipboard!');
        } else {
            var t = document.createElement('div');
            t.textContent = 'Kredensial berhasil disalin ke clipboard!';
            t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:999999;background:#166534;color:#fff;padding:1rem 1.5rem;border-radius:10px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,0.2);';
            document.body.appendChild(t);
            setTimeout(function(){ t.remove(); }, 2500);
        }
    }

    function showCopyManualModal(text) {
        var overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:99999;display:flex;align-items:center;justify-content:center;';
        var escapedText = text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        overlay.innerHTML = '<div style="background:#fff;border-radius:12px;padding:1.5rem;max-width:500px;width:90%;max-height:80vh;overflow:auto;">' +
            '<h3 style="margin:0 0 1rem;font-size:1.1rem;">Salin Kredensial</h3>' +
            '<p style="color:#64748b;font-size:0.9rem;margin-bottom:1rem;">Copy teks di bawah ini secara manual:</p>' +
            '<textarea readonly style="width:100%;height:200px;padding:0.75rem;border:1px solid #e2e8f0;border-radius:8px;font-family:monospace;font-size:0.85rem;resize:none;">' + escapedText + '</textarea>' +
            '<div style="display:flex;gap:0.5rem;margin-top:1rem;">' +
            '<button onclick="this.closest(\'div[style]\').parentElement.remove()" style="flex:1;padding:0.75rem;background:#f1f5f9;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Tutup</button>' +
            '<button id="modalCopyBtn" style="flex:1;padding:0.75rem;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:700;">Copy</button>' +
            '</div></div>';
        document.body.appendChild(overlay);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) overlay.remove(); });
        overlay.querySelector('#modalCopyBtn').addEventListener('click', function() {
            var ta = overlay.querySelector('textarea');
            ta.select();
            try {
                document.execCommand('copy');
                overlay.remove();
                var t = document.createElement('div');
                t.textContent = 'Berhasil disalin!';
                t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:999999;background:#166534;color:#fff;padding:1rem 1.5rem;border-radius:10px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,0.2);';
                document.body.appendChild(t);
                setTimeout(function(){ t.remove(); }, 2500);
            } catch(e) { alert('Gagal copy, silakan select & copy manual (Ctrl+C)'); }
        });
    }
</script>
@endpush

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
