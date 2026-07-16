@extends('admin.layouts.app')
@section('title', 'Ubah Password')
@section('page-title', 'Ubah Password')

@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0">Ubah Password</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Perbarui password akun Anda</p>
    </div>
    <a href="{{ route('admin.profile.edit') }}" class="btn" style="background:#f1f5f9;color:#475569">
        ← Kembali ke Profil
    </a>
</div>

@if(session('success'))
<div style="background:#f0fdf4;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#166534;display:flex;align-items:center;gap:0.75rem">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div style="background:#fef2f2;padding:1rem;margin-bottom:1.5rem;border-radius:10px;color:#dc2626">
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
        
        <div style="margin-bottom:1.25rem">
            <label style="display:block;font-weight:600;margin-bottom:0.5rem;color:var(--text-dark)">Password Saat Ini <span style="color:#ef4444">*</span></label>
            <input type="password" 
                   name="current_password" 
                   required 
                   style="width:100%;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem"
                   placeholder="Masukkan password saat ini">
            @error('current_password')
            <p style="color:#ef4444;font-size:0.85rem;margin-top:0.25rem">{{ $message }}</p>
            @enderror
        </div>
        
        <div style="margin-bottom:1.25rem">
            <label style="display:block;font-weight:600;margin-bottom:0.5rem;color:var(--text-dark)">Password Baru <span style="color:#ef4444">*</span></label>
            <input type="password" 
                   name="password" 
                   required 
                   style="width:100%;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem"
                   placeholder="Masukkan password baru">
            @error('password')
            <p style="color:#ef4444;font-size:0.85rem;margin-top:0.25rem">{{ $message }}</p>
            @enderror
        </div>
        
        <div style="margin-bottom:1.5rem">
            <label style="display:block;font-weight:600;margin-bottom:0.5rem;color:var(--text-dark)">Konfirmasi Password Baru <span style="color:#ef4444">*</span></label>
            <input type="password" 
                   name="password_confirmation" 
                   required 
                   style="width:100%;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:8px;font-size:0.95rem"
                   placeholder="Konfirmasi password baru">
        </div>
        
        <div style="display:flex;gap:0.75rem">
            <button type="submit" class="btn btn-primary" style="flex:1;display:flex;align-items:center;justify-content:center;gap:0.5rem">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Update Password
            </button>
        </div>
    </form>
</div>

@endsection