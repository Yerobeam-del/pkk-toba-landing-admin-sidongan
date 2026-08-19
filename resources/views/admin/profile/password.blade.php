{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@section('content')

<div class="u-header-row-plain">
    <div>
        <h1 class="u-page-title">Ubah Password</h1>
        <p class="u-muted">Perbarui password akun Anda</p>
    </div>
    <div class="u-flex-center-gap-2">
        @if (session('sso_from_sieda'))
        {{-- Diakses dari SIEDA: tombol kembali diganti menjadi Kembali ke SIEDA --}}
        <a href="{{ app(\App\Services\SsoTokenService::class)->buildCallbackUrl(auth()->user()->email) }}" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.5rem 1rem;background:var(--primary);color:#fff;border-radius:8px;text-decoration:none;font-size:0.85rem;font-weight:600;transition:all 0.2s">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Kembali ke SIEDA
        </a>
        @else
        <x-admin.back-button :href="route('admin.profile.edit')" label="Kembali ke Profil" />
        @endif
    </div>
</div>

@if(session('success'))
<div style="background:#f0fdf4;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#166534;display:flex;align-items:center;gap:0.75rem">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="u-a34">
    <ul style="margin:0;padding-left:1.25rem">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card" style="max-width:600px;padding:1.5rem;border-radius:12px">
    <form action="{{ route('admin.profile.password.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="u-a62">
            <label class="u-label-dark">Password Saat Ini <span class="u-text-danger">*</span></label>
            <input class="u-a63" type="password" 
                   name="current_password" 
                   required 
                  
                   placeholder="Masukkan password saat ini">
            @error('current_password')
            <p style="color:#ef4444;font-size:0.85rem;margin-top:0.25rem">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="u-a62">
            <label class="u-label-dark">Password Baru <span class="u-text-danger">*</span></label>
            <input class="u-a63" type="password" 
                   name="password" 
                   required 
                  
                   placeholder="Masukkan password baru">
            @error('password')
            <p style="color:#ef4444;font-size:0.85rem;margin-top:0.25rem">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="u-mb-6">
            <label class="u-label-dark">Konfirmasi Password Baru <span class="u-text-danger">*</span></label>
            <input class="u-a63" type="password" 
                   name="password_confirmation" 
                   required 
                  
                   placeholder="Konfirmasi password baru">
        </div>
        
        <div class="u-a64">
            <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Update Password
            </button>
        </div>
    </form>
</div>

@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
