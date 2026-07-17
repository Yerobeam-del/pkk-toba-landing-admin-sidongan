@extends('admin.layouts.app')
@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')

@section('content')
<div style="margin-bottom:2rem">
    
    {{-- Header Section --}}
    <div class="struktur-header">
        <div style="flex:1;min-width:0">
            <h1>Manajemen Akun</h1>
            <p>Kelola akun pengguna dan hak akses aplikasi sistem PKK</p>
        </div>
        <a href="{{ route('admin.user-management.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Akun
        </a>
    </div>

    {{-- Stats Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-bottom:2rem">
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

    {{-- TABS & SEARCH --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;gap:1rem;flex-wrap:wrap">
        <div class="tabs-container" style="flex:1;min-width:0">
            @php
                $tabs = [
                    'all' => 'Semua Pengguna',
                    'active' => 'Aktif',
                    'inactive' => 'Nonaktif',
                    'with-access' => 'Punya Akses'
                ];
            @endphp
            @foreach($tabs as $key => $label)
                @php
                    $isActive = $tab === $key;
                    $url = request()->fullUrlWithQuery(['tab' => $key, 'page' => 1, 'search' => request('search')]);
                    
                    $badgeCount = 0;
                    if($key === 'active') $badgeCount = \App\Models\User::whereNotNull('email_verified_at')->count();
                    if($key === 'inactive') $badgeCount = \App\Models\User::whereNull('email_verified_at')->count();
                    if($key === 'with-access') $badgeCount = \App\Models\User::whereHas('applications')->count();
                @endphp
                <a href="{{ $url }}" class="tab-btn {{ $isActive ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:8px 8px 0 0;text-decoration:none;color:{{ $isActive ? 'var(--primary)' : 'var(--text-muted)' }};background:transparent;border:none;font-weight:600;font-size:0.9rem;transition:all 0.2s;border-bottom:2px solid {{ $isActive ? 'var(--primary)' : 'transparent' }}">
                    {{ $label }}
                    @if($key !== 'all' && $badgeCount > 0)
                        <span style="background:rgba(0,0,0,0.05);color:var(--text-muted);padding:2px 8px;border-radius:12px;font-size:0.75rem">{{ $badgeCount }}</span>
                @endif
                </a>
            @endforeach
        </div>
        
        {{-- SEARCH BOX --}}
        <form method="GET" action="{{ route('admin.user-management.index') }}" style="flex-shrink:0">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div style="position:relative">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted)">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengguna..." style="padding:0.5rem 0.75rem 0.5rem 2.5rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;width:250px;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                @if(request('search'))
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);text-decoration:none" title="Hapus pencarian">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Main Card --}}
    <div class="struktur-card">
        
        @php
            $userColumns = [
                [
                    'key' => 'name',
                    'label' => 'Pengguna',
                    'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                    'type' => 'callback',
                    'callback' => function($user, $value) {
                        return '
                            <div style="display:flex;align-items:center;gap:0.75rem">
                                <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">
                                    ' . ($user->avatar ? '<img src="' . asset('storage/' . $user->avatar) . '" style="width:100%;height:100%;object-fit:cover">' : strtoupper(substr($user->name, 0, 1))) . '
                                </div>
                                <div>
                                    <div style="font-weight:600;color:var(--text-dark)">' . $user->name . '</div>
                                    <div style="font-size:0.85rem;color:var(--text-muted)">' . Str::limit($user->email, 20) . '</div>
                                </div>
                            </div>
                        ';
                    }
                ],
                [
                    'key' => 'email',
                    'label' => 'Email',
                    'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
                ],
                [
                    'key' => 'applications',
                    'label' => 'Aplikasi',
                    'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
                    'type' => 'callback',
                    'callback' => function($user, $value) {
                        $count = $user->applications->count();
                        return $count > 0 
                            ? '<span style="background:rgba(128,90,213,0.1);color:#6b46c1;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">' . $count . ' aplikasi</span>'
                            : '<span style="color:var(--text-muted);font-size:0.85rem">-</span>';
                    }
                ],
                [
                    'key' => 'email_verified_at',
                    'label' => 'Status',
                    'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                    'type' => 'callback',
                    'callback' => function($user, $value) {
                        return $value 
                            ? '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(34,197,94,0.1);color:#166534"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Aktif</span>'
                            : '<span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600;background:rgba(239,68,68,0.1);color:#dc2626"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Nonaktif</span>';
                    }
                ],
                [
                    'key' => 'created_at',
                    'label' => 'Dibuat',
                    'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                    'type' => 'callback',
                    'callback' => function($user, $value) {
                        return $user->created_at->locale('id')->translatedFormat('d F Y');
                    }
                ],
            ];
        @endphp

        {{-- Tab: Semua Pengguna --}}
        <div id="tab-all" class="tab-content {{ $tab === 'all' ? 'active' : '' }}">
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $users,
                    'columns' => $userColumns,
                    'emptyMessage' => 'Belum ada pengguna',
                    'editRoute' => 'admin.user-management.edit',
                    'deleteRoute' => 'admin.user-management.destroy',
                    'showRoute' => 'admin.user-management.show',
                    'actions' => ['show', 'edit', 'delete']
                ])
                
                @if($users->hasPages())
                    @include('admin.partials.pagination', ['paginator' => $users])
                @endif
            </div>
        </div>

        {{-- Tab: Pengguna Aktif --}}
        <div id="tab-active" class="tab-content {{ $tab === 'active' ? 'active' : '' }}">
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $users,
                    'columns' => $userColumns,
                    'emptyMessage' => 'Tidak ada pengguna aktif',
                    'editRoute' => 'admin.user-management.edit',
                    'deleteRoute' => 'admin.user-management.destroy',
                    'showRoute' => 'admin.user-management.show',
                    'actions' => ['show', 'edit', 'delete']
                ])
                
                @if($users->hasPages())
                    @include('admin.partials.pagination', ['paginator' => $users])
                @endif
            </div>
        </div>

        {{-- Tab: Pengguna Nonaktif --}}
        <div id="tab-inactive" class="tab-content {{ $tab === 'inactive' ? 'active' : '' }}">
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $users,
                    'columns' => $userColumns,
                    'emptyMessage' => 'Tidak ada pengguna nonaktif',
                    'editRoute' => 'admin.user-management.edit',
                    'deleteRoute' => 'admin.user-management.destroy',
                    'showRoute' => 'admin.user-management.show',
                    'actions' => ['show', 'edit', 'delete']
                ])
                
                @if($users->hasPages())
                    @include('admin.partials.pagination', ['paginator' => $users])
                @endif
            </div>
        </div>

        {{-- Tab: Punya Akses --}}
        <div id="tab-with-access" class="tab-content {{ $tab === 'with-access' ? 'active' : '' }}">
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $users,
                    'columns' => $userColumns,
                    'emptyMessage' => 'Belum ada pengguna dengan akses aplikasi',
                    'editRoute' => 'admin.user-management.edit',
                    'deleteRoute' => 'admin.user-management.destroy',
                    'showRoute' => 'admin.user-management.show',
                    'actions' => ['show', 'edit', 'delete']
                ])
                
                @if($users->hasPages())
                    @include('admin.partials.pagination', ['paginator' => $users])
                @endif
            </div>
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
                { title: 'Konfirmasi Perubahan Status', confirmText: 'Ya, Ubah', cancelText: 'Batal', type: 'warning' }
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
            if (typeof Toast !== 'undefined') Toast.success(data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            if (typeof Toast !== 'undefined') Toast.error(data.message);
            else alert(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        if (typeof Toast !== 'undefined') Toast.error('Terjadi kesalahan saat mengubah status akun');
        else alert('Terjadi kesalahan saat mengubah status akun');
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
                { title: 'Hapus Akun?', confirmText: 'Ya, Hapus', cancelText: 'Batal', type: 'danger' }
            );
            if (confirmed) document.getElementById('delete-user-' + id).submit();
        } else {
            if (confirm(`Hapus akun "${name}"?`)) document.getElementById('delete-user-' + id).submit();
        }
    } catch (error) {
        console.error('Error:', error);
        if (confirm(`Hapus akun "${name}"?`)) document.getElementById('delete-user-' + id).submit();
    }
}
</script>
@endsection