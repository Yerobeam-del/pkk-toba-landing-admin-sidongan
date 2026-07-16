@extends('admin.layouts.app')
@section('title', 'Detail Akun')
@section('page-title', 'Detail Akun')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Detail Akun</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Informasi akun {{ $user->name }}</p>
    </div>
    <div style="display:flex;gap:0.75rem">
        
        {{-- Hanya tampilkan tombol Edit jika user yang sedang login adalah Super Admin --}}
        @if(auth()->user()->sidongan_role === 'super_admin')
            <a href="{{ route('admin.user-management.edit', $user) }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Edit Akun
            </a>
        @endif
        
        <a href="{{ route('admin.user-management.index') }}" class="btn" style="background:#f1f5f9;color:#475569">Kembali</a>
    </div>
</div>

<div class="card" style="padding:1.5rem;border-radius:12px">
    <div style="display:grid;gap:1rem;max-width:600px">
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--text-muted)">Nama</span>
            <span style="font-weight:600">{{ $user->name }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--text-muted)">Email</span>
            <span style="font-weight:600">{{ $user->email }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--text-muted)">Telepon</span>
            <span style="font-weight:600">{{ $user->phone_number ?? '-' }}</span>
        </div>
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border)">
            <span style="color:var(--text-muted)">Status Email</span>
            <span>{{ $user->email_verified_at ? '✓ Terverifikasi' : '⚠ Belum Verifikasi' }}</span>
        </div>
        
        {{-- SIDONGAN Role --}}
        @if($user->sidongan_role)
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border);background:rgba(20,184,166,0.05);padding:0.75rem 1rem;border-radius:8px">
            <span style="color:var(--text-muted)">Peran di SIDONGAN</span>
            <span style="font-weight:600;color:#0d9488">
                <i class="fas fa-user-tag" style="margin-right:0.5rem"></i>
                {{ $user->sidongan_role_name }}
            </span>
        </div>
        @endif
        
        <div style="display:flex;justify-content:space-between;padding:0.75rem 0">
            <span style="color:var(--text-muted)">Dibuat</span>
            <span style="font-weight:600">{{ $user->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>
    
    <div style="margin-top:1.5rem">
        <h3 style="font-weight:600;margin-bottom:0.75rem">Aplikasi yang Diakses</h3>
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
        <p style="color:var(--text-muted)">Belum ada akses aplikasi</p>
        @endif
    </div>

    {{-- BAGIAN BARU: Info Admin Panel --}}
    <div style="margin-top:1.5rem">
        <h3 style="font-weight:600;margin-bottom:0.75rem">Info Admin Panel</h3>
        
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
            <span style="color:var(--text-muted)">Belum ada role yang ditetapkan</span>
        @endif
    </div>

    {{-- Info Akses SIEDA --}}
    @if($user->sieda_role)
    <div style="margin-top:1.5rem">
        <h3 style="font-weight:600;margin-bottom:0.75rem">Info Akses SIEDA</h3>
        
        <div style="background:#f0fdf4;padding:1rem;border-radius:8px;border-left:4px solid #22c55e">
            <div style="display:grid;gap:0.75rem">
                {{-- Role SIEDA --}}
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:0.85rem;color:var(--text-muted)">Role di SIEDA</span>
                    <span style="background:#22c55e;color:#fff;padding:4px 12px;border-radius:20px;font-size:0.85rem;font-weight:600;text-transform:capitalize">
                        {{ $user->sieda_role }}
                    </span>
                </div>

                {{-- Kecamatan --}}
                @if($user->sieda_kecamatan)
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:0.5rem;border-top:1px dashed #bbf7d0">
                    <span style="font-size:0.85rem;color:var(--text-muted)">Kecamatan</span>
                    <span style="font-weight:600;color:#166534">
                        <i class="fas fa-map-marker-alt" style="margin-right:0.5rem;color:#22c55e"></i>
                        {{ $kecamatan ? $kecamatan->name : $user->sieda_kecamatan }}
                    </span>
                </div>
                @endif

                {{-- Kelurahan/Desa --}}
                @if($user->sieda_kelurahan)
                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:0.5rem">
                    <span style="font-size:0.85rem;color:var(--text-muted)">Kelurahan/Desa</span>
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

@endsection