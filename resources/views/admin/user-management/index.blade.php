@extends('admin.layouts.app')
@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')

@section('content')

{{-- Header Section --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--text-dark);margin:0 0 0.25rem 0;letter-spacing:-0.5px">Manajemen Akun</h1>
        <p style="color:var(--text-muted);margin:0;font-size:0.9rem">Kelola akun pengguna dan hak akses aplikasi sistem PKK</p>
    </div>
    <a href="{{ route('admin.user-management.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Akun
    </a>
</div>

{{-- Stats Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
    {{-- Total Pengguna --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#3182ce,#2b6cb0);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Total Pengguna</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $users->total() }}</p>
            </div>
        </div>
    </div>

    {{-- Pengguna Aktif --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#38a169,#2f855a);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Pengguna Aktif</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $users->filter(fn($u) => $u->email_verified_at)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Pengguna Nonaktif --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Pengguna Nonaktif</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $users->filter(fn($u) => !$u->email_verified_at)->count() }}</p>
            </div>
        </div>
    </div>

    {{-- Dengan Akses Aplikasi --}}
    <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff">
        <div style="display:flex;align-items:flex-start;gap:1rem">
            <div style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:0.85rem;opacity:0.9;margin:0 0 0.25rem 0">Punya Akses Aplikasi</p>
                <p style="font-size:1.85rem;font-weight:800;margin:0;line-height:1.1">{{ $users->filter(fn($u) => $u->applications->count() > 0)->count() }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Modern Tabs --}}
<div style="display:flex;gap:0.25rem;margin-bottom:1.5rem;border-bottom:1px solid rgba(0,0,0,0.06);padding-bottom:0.5rem;overflow-x:auto">
    <button class="tab-btn active" onclick="switchTab('all', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid var(--primary)">
        Semua Pengguna
    </button>
    <button class="tab-btn" onclick="switchTab('active', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent">
        Aktif <span style="background:rgba(56,161,105,0.15);color:#2f855a;padding:2px 8px;border-radius:12px;font-size:0.75rem;margin-left:4px">{{ $users->filter(fn($u) => $u->email_verified_at)->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('inactive', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent">
        Nonaktif <span style="background:rgba(229,62,62,0.15);color:#c53030;padding:2px 8px;border-radius:12px;font-size:0.75rem;margin-left:4px">{{ $users->filter(fn($u) => !$u->email_verified_at)->count() }}</span>
    </button>
    <button class="tab-btn" onclick="switchTab('with-access', this)" style="padding:0.6rem 1rem;border-radius:8px 8px 0 0;background:transparent;border:none;font-weight:600;color:var(--text-muted);cursor:pointer;transition:all 0.2s;border-bottom:2px solid transparent">
        Punya Akses <span style="background:rgba(128,90,213,0.15);color:#6b46c1;padding:2px 8px;border-radius:12px;font-size:0.75rem;margin-left:4px">{{ $users->filter(fn($u) => $u->applications->count() > 0)->count() }}</span>
    </button>
</div>

{{-- Main Card --}}
<div class="card" style="padding:0;overflow:hidden">
    
    {{-- All Users --}}
    <div id="tab-all" class="tab-content active">
        <div class="table-container" style="padding:1rem">
            @if($users->count() > 0)
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid rgba(0,0,0,0.06)">
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Pengguna</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Email</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Aplikasi</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Status</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Dibuat</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04)">
                        <td style="padding:1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" style="width:100%;height:100%;object-fit:cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-dark)">{{ $user->name }}</div>
                                    <div style="font-size:0.85rem;color:var(--text-muted)">{{ Str::limit($user->email, 20) }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:1rem;color:var(--text-muted);font-size:0.9rem">{{ $user->email }}</td>
                        <td style="padding:1rem">
                            @if($user->applications->count() > 0)
                                <span style="background:rgba(128,90,213,0.1);color:#6b46c1;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">
                                    {{ $user->applications->count() }} aplikasi
                                </span>
                            @else
                                <span style="color:var(--text-muted);font-size:0.85rem">-</span>
                            @endif
                        </td>
                        <td style="padding:1rem">
                            @if($user->email_verified_at)
                                <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    Aktif
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(239,68,68,0.1);color:#dc2626">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td style="padding:1rem;color:var(--text-muted);font-size:0.9rem">{{ $user->created_at->format('d M Y') }}</td>
                        <td style="padding:1rem;text-align:right">
                            <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex;align-items:center">
                                {{-- Tombol View --}}
                                <a href="{{ route('admin.user-management.show', $user) }}" title="Lihat" 
                                   style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                   onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                   onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                
                                {{-- Tombol untuk Super Admin --}}
                                @if(auth()->user()->sidongan_role === 'super_admin')
                                    
                                    {{-- Tombol Toggle Status --}}
                                    <button type="button" onclick="toggleStatus({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->email_verified_at ? 'true' : 'false' }})" 
                                            title="{{ $user->email_verified_at ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}" 
                                            style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                            onmouseover="this.style.background='{{ $user->email_verified_at ? '#fef3c7' : '#f0fdf4' }}';this.style.color='{{ $user->email_verified_at ? '#d97706' : '#16a34a' }}'"
                                            onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                        @if($user->email_verified_at)
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                        @else
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                        @endif
                                    </button>
                                    
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('admin.user-management.edit', $user) }}" title="Edit" 
                                       style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                       onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                       onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    
                                    {{-- Tombol Delete --}}
                                    @if($user->id !== auth()->id() && $user->sidongan_role !== 'super_admin')
                                    <button type="button" onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Hapus" 
                                            style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                            onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'"
                                            onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                    <form id="delete-user-{{ $user->id }}" action="{{ route('admin.user-management.destroy', $user) }}" method="POST" style="display:none">
                                        @csrf @method('DELETE')
                                    </form>
                                    @endif
                                    
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
                <div style="width:64px;height:64px;background:#f8fafc;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <h3 style="font-size:1rem;font-weight:700;color:var(--text-dark);margin:0 0 0.5rem">Belum Ada Pengguna</h3>
                <p style="font-size:0.9rem;margin:0">Silakan tambah akun pengguna pertama Anda.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Active Users --}}
    <div id="tab-active" class="tab-content" style="display:none">
        <div class="table-container" style="padding:1rem">
            @php $activeUsers = $users->filter(fn($u) => $u->email_verified_at); @endphp
            @if($activeUsers->count() > 0)
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid rgba(0,0,0,0.06)">
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Pengguna</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Email</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Aplikasi</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeUsers as $user)
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04)">
                        <td style="padding:1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,#38a169,#2f855a);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" style="width:100%;height:100%;object-fit:cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-dark)">{{ $user->name }}</div>
                                    <div style="font-size:0.85rem;color:var(--text-muted)">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:1rem;color:var(--text-muted)">{{ $user->email }}</td>
                        <td style="padding:1rem">
                            @if($user->applications->count() > 0)
                                <span style="background:rgba(128,90,213,0.1);color:#6b46c1;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">{{ $user->applications->count() }} aplikasi</span>
                            @else
                                <span style="color:var(--text-muted)">-</span>
                            @endif
                        </td>
                        <td style="padding:1rem;text-align:right">
                            <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex;align-items:center">
                                @if(auth()->user()->sidongan_role === 'super_admin')
                                <button type="button" onclick="toggleStatus({{ $user->id }}, '{{ addslashes($user->name) }}', true)" title="Nonaktifkan Akun" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                        onmouseover="this.style.background='#fef3c7';this.style.color='#d97706'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                </button>
                                <a href="{{ route('admin.user-management.edit', $user) }}" title="Edit" 
                                   style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                   onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                   onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @if($user->id !== auth()->id() && $user->sidongan_role !== 'super_admin')
                                <button type="button" onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Hapus" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                        onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                                <form id="delete-user-{{ $user->id }}" action="{{ route('admin.user-management.destroy', $user) }}" method="POST" style="display:none">
                                    @csrf @method('DELETE')
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
                <p style="margin:0;font-size:0.95rem">Tidak ada pengguna aktif</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Inactive Users --}}
    <div id="tab-inactive" class="tab-content" style="display:none">
        <div class="table-container" style="padding:1rem">
            @php $inactiveUsers = $users->filter(fn($u) => !$u->email_verified_at); @endphp
            @if($inactiveUsers->count() > 0)
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid rgba(0,0,0,0.06)">
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Pengguna</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Email</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Status</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inactiveUsers as $user)
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04)">
                        <td style="padding:1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,#e53e3e,#c53030);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" style="width:100%;height:100%;object-fit:cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-dark)">{{ $user->name }}</div>
                                    <div style="font-size:0.85rem;color:var(--text-muted)">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:1rem;color:var(--text-muted)">{{ $user->email }}</td>
                        <td style="padding:1rem">
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(229,62,62,0.1);color:#c53030">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Nonaktif
                            </span>
                        </td>
                        <td style="padding:1rem;text-align:right">
                            <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex;align-items:center">
                                @if(auth()->user()->sidongan_role === 'super_admin')
                                <button type="button" onclick="toggleStatus({{ $user->id }}, '{{ addslashes($user->name) }}', false)" title="Aktifkan Akun" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                        onmouseover="this.style.background='#f0fdf4';this.style.color='#16a34a'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                </button>
                                <a href="{{ route('admin.user-management.edit', $user) }}" title="Edit" 
                                   style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                   onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                   onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @if($user->id !== auth()->id() && $user->sidongan_role !== 'super_admin')
                                <button type="button" onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Hapus" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                        onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                                <form id="delete-user-{{ $user->id }}" action="{{ route('admin.user-management.destroy', $user) }}" method="POST" style="display:none">
                                    @csrf @method('DELETE')
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
                <p style="margin:0;font-size:0.95rem">Tidak ada pengguna nonaktif</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Users with Access --}}
    <div id="tab-with-access" class="tab-content" style="display:none">
        <div class="table-container" style="padding:1rem">
            @php $usersWithAccess = $users->filter(fn($u) => $u->applications->count() > 0); @endphp
            @if($usersWithAccess->count() > 0)
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid rgba(0,0,0,0.06)">
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Pengguna</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Email</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px">Aplikasi Diakses</th>
                        <th style="padding:1rem;color:var(--text-muted);font-weight:600;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.5px;text-align:right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usersWithAccess as $user)
                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04)">
                        <td style="padding:1rem">
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,#805ad5,#6b46c1);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" style="width:100%;height:100%;object-fit:cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-dark)">{{ $user->name }}</div>
                                    <div style="font-size:0.85rem;color:var(--text-muted)">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:1rem;color:var(--text-muted)">{{ $user->email }}</td>
                        <td style="padding:1rem">
                            <div style="display:flex;flex-wrap:wrap;gap:0.25rem">
                                @foreach($user->applications->take(3) as $app)
                                <span style="background:rgba(59,130,246,0.1);color:#2563eb;padding:3px 8px;border-radius:20px;font-size:0.7rem;font-weight:600">
                                    {{ $app->short_name ?? Str::limit($app->name, 10) }}
                                </span>
                                @endforeach
                                @if($user->applications->count() > 3)
                                <span style="background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:20px;font-size:0.7rem;font-weight:600">+{{ $user->applications->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding:1rem;text-align:right">
                            <div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex;align-items:center">
                                @if(auth()->user()->sidongan_role === 'super_admin')
                                <button type="button" onclick="toggleStatus({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->email_verified_at ? 'true' : 'false' }})" 
                                        title="{{ $user->email_verified_at ? 'Nonaktifkan' : 'Aktifkan' }}" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                        onmouseover="this.style.background='{{ $user->email_verified_at ? '#fef3c7' : '#f0fdf4' }}';this.style.color='{{ $user->email_verified_at ? '#d97706' : '#16a34a' }}'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    @if($user->email_verified_at)
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                                    @else
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                    @endif
                                </button>
                                <a href="{{ route('admin.user-management.edit', $user) }}" title="Edit" 
                                   style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;cursor:pointer"
                                   onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'"
                                   onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @if($user->id !== auth()->id() && $user->sidongan_role !== 'super_admin')
                                <button type="button" onclick="confirmDeleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Hapus" 
                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"
                                        onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'"
                                        onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                </button>
                                <form id="delete-user-{{ $user->id }}" action="{{ route('admin.user-management.destroy', $user) }}" method="POST" style="display:none">
                                    @csrf @method('DELETE')
                                </form>
                                @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align:center;padding:3rem 1rem;color:var(--text-muted)">
                <p style="margin:0;font-size:0.95rem">Belum ada pengguna dengan akses aplikasi</p>
            </div>
            @endif
        </div>
    </div>

</div>

<script>
// ==========================================
// TOGGLE STATUS FUNCTION
// ==========================================
async function toggleStatus(userId, userName, currentStatus) {
    const action = currentStatus ? 'menonaktifkan' : 'mengaktifkan';
    
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Apakah Anda yakin ingin <strong>${action}</strong> akun <strong>"${userName}"</strong>?`,
                {
                    title: 'Konfirmasi Perubahan Status',
                    confirmText: 'Ya, Ubah',
                    cancelText: 'Batal',
                    type: 'warning'
                }
            );
            
            if (!confirmed) return;
        } else {
            if (!confirm(`Apakah Anda yakin ingin ${action} akun "${userName}"?`)) return;
        }
        
        const response = await fetch(`/admin/user-management/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            if (typeof Toast !== 'undefined') {
                Toast.success(data.message);
            }
            setTimeout(() => location.reload(), 1000);
        } else {
            if (typeof Toast !== 'undefined') {
                Toast.error(data.message);
            } else {
                alert(data.message);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof Toast !== 'undefined') {
            Toast.error('Terjadi kesalahan saat mengubah status akun');
        } else {
            alert('Terjadi kesalahan saat mengubah status akun');
        }
    }
}

// ==========================================
// DELETE CONFIRMATION
// ==========================================
async function confirmDeleteUser(id, name) {
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Akun <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                {
                    title: 'Hapus Akun?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
            
            if (confirmed) {
                document.getElementById('delete-user-' + id).submit();
            }
        } else {
            if (confirm(`Hapus akun "${name}"?`)) {
                document.getElementById('delete-user-' + id).submit();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        if (confirm(`Hapus akun "${name}"?`)) {
            document.getElementById('delete-user-' + id).submit();
        }
    }
}

// ==========================================
// TAB SWITCHING
// ==========================================
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.style.color = 'var(--text-muted)';
        b.style.borderBottom = '2px solid transparent';
    });
    document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
    
    btn.style.color = 'var(--primary)';
    btn.style.borderBottom = '2px solid var(--primary)';
    document.getElementById('tab-' + tabId).style.display = 'block';
}

document.addEventListener('DOMContentLoaded', () => {
    const firstBtn = document.querySelector('.tab-btn');
    if(firstBtn) switchTab('all', firstBtn);
});
</script>

@endsection