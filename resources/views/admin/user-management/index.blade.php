{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Manajemen Akun')
@section('page-title', 'Manajemen Akun')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-user-management-index.css') }}">


<div class="u-mb-8">

    {{-- Header Section --}}
    <div class="struktur-header u-a12">
        <div class="u-flex-1-min">
            <h1 class="u-page-title-tight">Manajemen Akun</h1>
            <p class="u-muted">Kelola akun pengguna dan hak akses aplikasi sistem PKK</p>
        </div>
        <div class="u-a64">
            @if(auth()->user()->hasRole('super_admin'))
            <a href="{{ route('admin.user-management.export', ['tab' => $tab]) }}" class="btn" style="display:inline-flex;align-items:center;gap:0.5rem;background:#f0fdf4;color:#166534;padding:0.5rem 1rem;border-radius:8px;border:1px solid #bbf7d0;text-decoration:none;font-weight:600;font-size:0.85rem;transition:all 0.2s;white-space:nowrap">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
            @endif
            <a href="{{ route('admin.user-management.create') }}" class="btn btn-primary u-inline-flex-gap-2-nowrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Akun
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="u-a4">
        <div class="stat-card u-badge-blue">
            <div class="u-flex-start-gap-4">
                <div class="u-icon-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="u-flex-1">
                    <p class="u-subtitle">Total Pengguna</p>
                    <p class="u-h1-hero">{{ \App\Models\User::count() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card u-badge-green-solid">
            <div class="u-flex-start-gap-4">
                <div class="u-icon-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="u-flex-1">
                    <p class="u-subtitle">Pengguna Aktif</p>
                    <p class="u-h1-hero">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card" style="background:linear-gradient(135deg,#e53e3e,#c53030);color:#fff">
            <div class="u-flex-start-gap-4">
                <div class="u-icon-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div class="u-flex-1">
                    <p class="u-subtitle">Pengguna Nonaktif</p>
                    <p class="u-h1-hero">{{ \App\Models\User::whereNull('email_verified_at')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="stat-card" style="background:linear-gradient(135deg,#805ad5,#6b46c1);color:#fff">
            <div class="u-flex-start-gap-4">
                <div class="u-icon-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                </div>
                <div class="u-flex-1">
                    <p class="u-subtitle">Punya Akses Aplikasi</p>
                    <p class="u-h1-hero">{{ \App\Models\User::whereHas('applications')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="u-mb-4">
        <div class="tabs-container u-tabs-row">
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
                    $url = request()->fullUrlWithQuery(['tab' => $key, 'page' => 1, 'per_page' => request('per_page', 10)]);
                @endphp
                <a href="{{ $url }}" class="tab-btn {{ $isActive ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1rem;border-radius:8px;text-decoration:none;color:{{ $isActive ? 'var(--primary)' : 'var(--text-muted)' }};background:{{ $isActive ? 'rgba(13, 148, 136, 0.1)' : 'transparent' }};font-weight:600;font-size:0.9rem;transition:all 0.2s;border-bottom:2px solid {{ $isActive ? 'var(--primary)' : 'transparent' }};white-space:nowrap">
                    {{ $label }}
                    @if($key !== 'all')
                        @php
                            $badgeCount = 0;
                            if($key === 'active') $badgeCount = \App\Models\User::whereNotNull('email_verified_at')->count();
                            if($key === 'inactive') $badgeCount = \App\Models\User::whereNull('email_verified_at')->count();
                            if($key === 'with-access') $badgeCount = \App\Models\User::whereHas('applications')->count();
                        @endphp
                        @if($badgeCount > 0)
                            <span class="u-badge-soft">{{ $badgeCount }}</span>
                        @endif
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- Search & Tampilkan --}}
    <div class="u-header-row-wrap">
        {{-- Search Form --}}
        <div class="user-search-wrapper u-flex-1-min-200">
            <form method="GET" action="{{ route('admin.user-management.index') }}">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="u-relative">
                    <svg class="u-position-left" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" class="user-search-input u-input-icon-left" placeholder="Cari nama atau email...">
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

        {{-- Per Page Dropdown --}}
        <div class="user-perpage-wrapper u-flex-center-gap-2-shrink">
            <form method="GET" action="{{ route('admin.user-management.index') }}" class="user-form-wrapper u-flex-center-gap-2">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <label class="u-a3">Tampilkan:</label>
                <div class="u-relative">
                    <select class="u-select-mini" name="per_page">
                        <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <svg class="u-select-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </div>
            </form>
        </div>
    </div>

    {{-- Bulk Action Bar (hidden by default, shown when checkboxes selected) --}}
    @if(auth()->user()->hasRole('super_admin'))
    <div id="bulkActionBar" style="display:none;padding:0.75rem 1rem;background:linear-gradient(135deg,rgba(20,184,166,0.08),rgba(20,184,166,0.04));border:1px solid rgba(20,184,166,0.2);border-radius:10px;margin-bottom:1rem;display:none;align-items:center;gap:1rem;flex-wrap:wrap">
        <span style="font-size:0.85rem;font-weight:600;color:var(--primary)"><span id="bulkCount">0</span> dipilih</span>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap">
            <button type="button" onclick="bulkAction('activate')" class="btn" style="padding:0.4rem 0.75rem;background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;border-radius:6px;font-size:0.8rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:0.375rem">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Aktifkan
            </button>
            <button type="button" onclick="bulkAction('deactivate')" class="btn" style="padding:0.4rem 0.75rem;background:#fffbeb;color:#d97706;border:1px solid #fde68a;border-radius:6px;font-size:0.8rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:0.375rem">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Nonaktifkan
            </button>
            <button type="button" onclick="bulkAction('delete')" class="btn" style="padding:0.4rem 0.75rem;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:6px;font-size:0.8rem;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:0.375rem">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Hapus
            </button>
        </div>
        <button type="button" onclick="clearBulkSelection()" style="margin-left:auto;background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:0.8rem;font-weight:500;display:inline-flex;align-items:center;gap:0.25rem">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Batal
        </button>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="struktur-card">
        <div class="table-container u-p-0">

            @php
                $userColumns = [
                    @if(auth()->user()->hasRole('super_admin'))
                    [
                        'key' => 'id',
                        'label' => '<input type="checkbox" id="selectAll" style="cursor:pointer">',
                        'type' => 'callback',
                        'callback' => function($item) {
                            return '<input type="checkbox" class="bulk-checkbox" value="' . $item->id . '" style="cursor:pointer;width:16px;height:16px;accent-color:var(--primary)">';
                        }
                    ],
                    @endif
                    [
                        'key' => 'name',
                        'label' => 'Pengguna',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
                        'type' => 'callback',
                        'callback' => function($item, $value) {
                            $avatarHtml = $item->avatar
                                ? '<img src="' . asset('storage/' . $item->avatar) . '" style="width:100%;height:100%;object-fit:cover">'
                                : strtoupper(substr($item->name, 0, 1));

                            return '
                                <div class="u-flex-center-gap-3">
                                    <div style="width:40px;height:40px;border-radius:50%;overflow:hidden;background:linear-gradient(135deg,var(--primary),#0d9488);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">
                                        ' . $avatarHtml . '
                                    </div>
                                    <div>
                                        <div class="u-a30">' . $item->name . '</div>
                                        <div class="u-text-muted-sm">' . Str::limit($item->email, 20) . '</div>
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
                        'callback' => function($item, $value) {
                            $count = $item->applications->count();
                            if ($count > 0) {
                                return '<span style="background:rgba(128,90,213,0.1);color:#6b46c1;padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">' . $count . ' aplikasi</span>';
                            }
                            return '<span class="u-a31">-</span>';
                        }
                    ],
                    [
                        'key' => 'email_verified_at',
                        'label' => 'Status',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                        'type' => 'callback',
                        'callback' => function($item, $value) {
                            if ($value) {
                                return '<span class="u-badge-green"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Aktif</span>';
                            }
                            return '<span class="u-a67"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Nonaktif</span>';
                        }
                    ],
                    [
                        'key' => 'created_at',
                        'label' => 'Dibuat',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                        'type' => 'callback',
                        'callback' => function($item, $value) {
                            return $item->created_at->locale('id')->translatedFormat('d F Y');
                        }
                    ],
                ];
            @endphp

            {{-- Partial table ini sekarang OTOMATIS merender pagination jika $users adalah instance Paginator --}}
            @include('admin.partials.table', [
                'data' => $users,
                'columns' => $userColumns,
                'emptyMessage' => 'Belum ada pengguna',
                'editRoute' => 'admin.user-management.edit',
                'deleteRoute' => 'admin.user-management.destroy',
                'showRoute' => 'admin.user-management.show',
                'actions' => ['show', 'edit', 'delete'],
                'rowActions' => function($item) {
                    $html = '';
                    if (auth()->user()->sidongan_role === 'super_admin') {
                        $statusAction = $item->email_verified_at
                            ? '<button type="button" data-toggle-status="1" data-toggle-status-id="'.$item->id.'" data-toggle-status-name="'.addslashes($item->name).'" title="Nonaktifkan Akun" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg></button>'
                            : '<button type="button" data-toggle-status="0" data-toggle-status-id="'.$item->id.'" data-toggle-status-name="'.addslashes($item->name).'" title="Aktifkan Akun" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg></button>';

                        $html .= $statusAction;

                        // Tombol Reset Password
                        $html .= '<button type="button" data-reset-password-id="'.$item->id.'" data-reset-password-name="'.addslashes($item->name).'" title="Reset Password" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;border:none;cursor:pointer;transition:all 0.2s"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>';
                    }
                    return $html;
                }
            ])
        </div>
    </div>
</div>

{{-- ==========================================
     MODAL RESET PASSWORD
     ========================================== --}}
<div id="resetPasswordModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px)">
    <div style="background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:420px;margin:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.2);animation:slideIn 0.3s ease">
        <div style="text-align:center;margin-bottom:1.5rem">
            <div style="width:56px;height:56px;background:rgba(20,184,166,0.1);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h3 style="margin:0 0 0.25rem 0;font-size:1.15rem;font-weight:700;color:#1e293b">Reset Password</h3>
            <p style="margin:0;color:#64748b;font-size:0.9rem">Akun: <strong id="resetPasswordUserName"></strong></p>
        </div>

        <form id="resetPasswordForm">
            <input type="hidden" id="resetPasswordUserId" value="">

            <div class="form-group u-mb-4">
                <label for="resetPasswordInput" style="display:block;font-weight:600;color:#1e293b;margin-bottom:0.5rem;font-size:0.9rem">Password Baru</label>
                <input type="password" id="resetPasswordInput" name="password" required placeholder="Minimal 8 karakter"
                    style="width:100%;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:10px;font-size:0.95rem;font-family:inherit;transition:all 0.2s">
            </div>

            <div class="form-group u-a62">
                <label for="resetPasswordConfirmInput" style="display:block;font-weight:600;color:#1e293b;margin-bottom:0.5rem;font-size:0.9rem">Konfirmasi Password Baru</label>
                <input type="password" id="resetPasswordConfirmInput" name="password_confirmation" required placeholder="Ulangi password baru"
                    style="width:100%;padding:0.75rem 1rem;border:2px solid #e2e8f0;border-radius:10px;font-size:0.95rem;font-family:inherit;transition:all 0.2s">
            </div>

            <div class="u-a64">
                <button type="button" data-action="close-reset-password" style="flex:1;padding:0.75rem;background:#f1f5f9;color:#64748b;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-family:inherit;font-size:0.9rem;transition:all 0.2s">
                    Batal
                </button>
                <button type="submit" id="resetPasswordSubmitBtn" style="flex:1;padding:0.75rem;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer;font-family:inherit;font-size:0.9rem;transition:all 0.2s;box-shadow:0 4px 12px rgba(20,184,166,0.3)">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/admin/js/user-management-index.js') }}"></script>

    {{-- ==========================================
         MODAL: KREDENSIAL AKUN BARU
         ========================================== --}}
    // ========== BULK ACTIONS ==========
    @if(auth()->user()->hasRole('super_admin'))
    function initBulkSelection() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.bulk-checkbox');
        const bar = document.getElementById('bulkActionBar');
        const countEl = document.getElementById('bulkCount');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => { cb.checked = this.checked; });
                updateBulkBar();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        function updateBulkBar() {
            const checked = document.querySelectorAll('.bulk-checkbox:checked');
            if (checked.length > 0) {
                bar.style.display = 'flex';
                countEl.textContent = checked.length;
            } else {
                bar.style.display = 'none';
            }
        }
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.bulk-checkbox:checked')).map(cb => cb.value);
    }

    function clearBulkSelection() {
        document.querySelectorAll('.bulk-checkbox:checked').forEach(cb => { cb.checked = false; });
        const selectAll = document.getElementById('selectAll');
        if (selectAll) selectAll.checked = false;
        document.getElementById('bulkActionBar').style.display = 'none';
    }

    async function bulkAction(action) {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        const labels = { activate: 'mengaktifkan', deactivate: 'menonaktifkan', delete: 'menghapus' };
        if (action === 'delete' && !confirm('Yakin ingin menghapus ' + ids.length + ' pengguna?')) return;

        try {
            const response = await fetch('{{ route("admin.user-management.bulk-action") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action, user_ids: ids })
            });
            const result = await response.json();
            if (result.success) {
                if (typeof Toast !== 'undefined') Toast.success(result.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                if (typeof Toast !== 'undefined') Toast.error(result.message || 'Gagal melakukan aksi');
            }
        } catch (e) {
            if (typeof Toast !== 'undefined') Toast.error('Terjadi kesalahan jaringan');
        }
    }
    document.addEventListener('DOMContentLoaded', initBulkSelection);
    @endif

    @if(session('new_account'))
    <div id="newAccountModal" style="display:flex;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px)">
        <div style="background:#fff;border-radius:16px;padding:2rem;width:100%;max-width:440px;margin:1rem;box-shadow:0 20px 60px rgba(0,0,0,0.2);animation:slideIn 0.3s ease">
            {{-- Header --}}
            <div style="text-align:center;margin-bottom:1.5rem">
                <div style="width:56px;height:56px;background:rgba(34,197,94,0.1);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h3 style="margin:0 0 0.25rem 0;font-size:1.15rem;font-weight:700;color:#1e293b">Akun Berhasil Dibuat!</h3>
                <p style="margin:0;color:#64748b;font-size:0.9rem">Salin kredensial di bawah untuk dikirim ke pengguna</p>
            </div>

            {{-- Credentials Card --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:1.25rem;margin-bottom:1.25rem">
                <div style="margin-bottom:0.75rem">
                    <span style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">Nama</span>
                    <div style="font-size:0.95rem;font-weight:600;color:#1e293b;margin-top:0.25rem">{{ session('new_account.name') }}</div>
                </div>
                <div style="margin-bottom:0.75rem">
                    <span style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">Email</span>
                    <div style="font-size:0.95rem;font-weight:600;color:var(--primary);margin-top:0.25rem;font-family:monospace">{{ session('new_account.email') }}</div>
                </div>
                <div>
                    <span style="font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:0.5px">Password</span>
                    <div style="font-size:0.95rem;color:#64748b;margin-top:0.25rem;font-style:italic">Dikirim saat pembuatan akun</div>
                </div>
            </div>

            {{-- Copy Buttons --}}
            <div style="display:grid;gap:0.5rem;margin-bottom:1rem">
                <button type="button" onclick="copyCredentials('full')" id="copyFullBtn" style="width:100%;padding:0.75rem;background:linear-gradient(135deg,var(--primary),#0d9488);color:#fff;border:none;border-radius:10px;font-weight:700;cursor:pointer;font-family:inherit;font-size:0.9rem;transition:all 0.2s;box-shadow:0 4px 12px rgba(20,184,166,0.3);display:flex;align-items:center;justify-content:center;gap:0.5rem">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span>Salin Pesan Lengkap</span>
                </button>
                <button type="button" onclick="copyCredentials('email')" id="copyEmailBtn" style="width:100%;padding:0.625rem;background:#f1f5f9;color:#475569;border:none;border-radius:10px;font-weight:600;cursor:pointer;font-family:inherit;font-size:0.85rem;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.5rem">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span>Salin Email Saja</span>
                </button>
            </div>

            {{-- Close --}}
            <button type="button" onclick="document.getElementById('newAccountModal').style.display='none'" style="width:100%;padding:0.625rem;background:transparent;color:#64748b;border:1px solid #e2e8f0;border-radius:10px;font-weight:600;cursor:pointer;font-family:inherit;font-size:0.85rem;transition:all 0.2s">
                Tutup
            </button>
        </div>
    </div>
    <script>
        function copyCredentials(type) {
            const name = {{ json_encode(session('new_account.name')) }};
            const email = {{ json_encode(session('new_account.email')) }};

            let text = '';
            if (type === 'full') {
                text = 'Halo ' + name + ',\n\n' +
                    'Akun Anda di Admin Panel PKK Kabupaten Toba sudah dibuat.\n' +
                    'Berikut kredensial login Anda:\n\n' +
                    'Email    : ' + email + '\n' +
                    'Password : (password yang dibuat saat pembuatan akun)\n\n' +
                    'Silakan login di: ' + window.location.origin + '\n' +
                    'Ganti password setelah login pertama kali untuk keamanan.\n\n' +
                    'Terima kasih,';
            } else {
                text = email;
            }

            navigator.clipboard.writeText(text).then(() => {
                const btn = type === 'full' ? document.getElementById('copyFullBtn') : document.getElementById('copyEmailBtn');
                const span = btn.querySelector('span');
                const originalText = span.textContent;
                span.textContent = '✓ Tersalin!';
                btn.style.background = type === 'full' ? 'linear-gradient(135deg,#22c55e,#16a34a)' : '#dcfce7';
                btn.style.color = type === 'full' ? '#fff' : '#166534';
                setTimeout(() => {
                    span.textContent = originalText;
                    btn.style.background = type === 'full' ? 'linear-gradient(135deg,var(--primary),#0d9488)' : '#f1f5f9';
                    btn.style.color = type === 'full' ? '#fff' : '#475569';
                }, 2000);

                if (typeof Toast !== 'undefined') {
                    Toast.success('Berhasil disalin ke clipboard!');
                }
            }).catch(() => {
                if (typeof Toast !== 'undefined') {
                    Toast.error('Gagal menyalin. Silakan copy manual.');
                }
            });
        }
    </script>
    @endif
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
