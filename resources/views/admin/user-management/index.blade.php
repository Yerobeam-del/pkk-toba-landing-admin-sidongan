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
            <a href="{{ route('admin.user-management.export', ['tab' => $tab]) }}" class="btn um-export-btn">
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

        <div class="stat-card um-stat-red">
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

        <div class="stat-card um-stat-purple">
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
                <a href="{{ $url }}" class="tab-btn um-tab-btn {{ $isActive ? 'active' : '' }}">
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
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="um-search-clear" title="Hapus pencarian">
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
    <div id="bulkActionBar" class="um-bulk-bar" style="display:none">
        <span class="um-bulk-count"><span id="bulkCount">0</span> dipilih</span>
        <div class="um-bulk-btns">
            <button type="button" id="bulkBtnActivate" onclick="bulkAction('activate')" class="um-bulk-activate" style="display:none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Aktifkan
            </button>
            <button type="button" id="bulkBtnDeactivate" onclick="bulkAction('deactivate')" class="um-bulk-deactivate" style="display:none">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Nonaktifkan
            </button>
            <button type="button" onclick="bulkAction('delete')" class="um-bulk-delete">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Hapus
            </button>
        </div>
        <button type="button" onclick="clearBulkSelection()" class="um-bulk-cancel">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Batal
        </button>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="struktur-card">
        <div class="table-container u-p-0">

            @php
                $userColumns = [];

                if (auth()->user()->hasRole('super_admin')) {
                    $userColumns[] = [
                        'key' => 'id',
                        'label' => '<input type="checkbox" id="selectAll">',
                        'type' => 'callback',
                        'callback' => function($item) {
                            $status = $item->email_verified_at ? 'active' : 'inactive';
                            return '<input type="checkbox" class="bulk-checkbox" value="' . $item->id . '" data-status="' . $status . '">';
                        }
                    ];
                }

                $userColumns[] = [
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
                                    <div class="um-user-avatar">
                                        ' . $avatarHtml . '
                                    </div>
                                    <div>
                                        <div class="u-a30">' . $item->name . '</div>
                                        <div class="u-text-muted-sm">' . Str::limit($item->email, 20) . '</div>
                                    </div>
                                </div>
                            ';
                        }
                    ];

                    $userColumns[] = [
                        'key' => 'email',
                        'label' => 'Email',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
                    ];

                    $userColumns[] = [
                        'key' => 'applications',
                        'label' => 'Aplikasi',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
                        'type' => 'callback',
                        'callback' => function($item, $value) {
                            $count = $item->applications->count();
                            if ($count > 0) {
                                return '<span class="um-app-badge">' . $count . ' aplikasi</span>';
                            }
                            return '<span class="u-a31">-</span>';
                        }
                    ];

                    $userColumns[] = [
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
                    ];

                    $userColumns[] = [
                        'key' => 'created_at',
                        'label' => 'Dibuat',
                        'icon' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
                        'type' => 'callback',
                        'callback' => function($item, $value) {
                            return $item->created_at->locale('id')->translatedFormat('d F Y');
                        }
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
                            ? '<button type="button" class="action-btn" data-toggle-status="1" data-toggle-status-id="'.$item->id.'" data-toggle-status-name="'.addslashes($item->name).'" title="Nonaktifkan Akun"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg></button>'
                            : '<button type="button" class="action-btn" data-toggle-status="0" data-toggle-status-id="'.$item->id.'" data-toggle-status-name="'.addslashes($item->name).'" title="Aktifkan Akun"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg></button>';

                        $html .= $statusAction;

                        // Tombol Salin Kredensial
                        $html .= '<button type="button" class="action-btn" onclick="copyUserCredentials('.$item->id.', \'' . addslashes($item->name) . '\', \'' . addslashes($item->email) . '\')" title="Salin Kredensial"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg></button>';

                        // Tombol Reset Password
                        $html .= '<button type="button" class="action-btn" data-reset-password-id="'.$item->id.'" data-reset-password-name="'.addslashes($item->name).'" title="Reset Password"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></button>';
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
<div id="resetPasswordModal" class="um-modal-overlay">
    <div class="um-modal-card" style="max-width:420px">
        <div style="text-align:center;margin-bottom:1.5rem">
            <div class="um-modal-icon-box um-modal-icon-box--teal">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </div>
            <h3 class="um-modal-title">Reset Password</h3>
            <p class="um-modal-subtitle">Akun: <strong id="resetPasswordUserName"></strong></p>
        </div>

        <form id="resetPasswordForm">
            <input type="hidden" id="resetPasswordUserId" value="">

            <div class="form-group u-mb-4">
                <label for="resetPasswordInput" class="um-modal-label">Password Baru</label>
                <input type="password" id="resetPasswordInput" name="password" required placeholder="Minimal 8 karakter"
                    class="um-modal-input">
            </div>

            <div class="form-group u-a62">
                <label for="resetPasswordConfirmInput" class="um-modal-label">Konfirmasi Password Baru</label>
                <input type="password" id="resetPasswordConfirmInput" name="password_confirmation" required placeholder="Ulangi password baru"
                    class="um-modal-input">
            </div>

            <div class="u-a64">
                <button type="button" data-action="close-reset-password" class="um-modal-btn-cancel">
                    Batal
                </button>
                <button type="submit" id="resetPasswordSubmitBtn" class="um-modal-btn-primary">
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
    @if(auth()->user()->hasRole('super_admin'))
    <script>
    // ========== BULK ACTIONS ==========
    function initBulkSelection() {
        // Use event delegation for selectAll checkbox
        document.addEventListener('change', function(e) {
            // Select All checkbox
            if (e.target && e.target.id === 'selectAll') {
                const checkboxes = document.querySelectorAll('.bulk-checkbox');
                checkboxes.forEach(function(cb) {
                    cb.checked = e.target.checked;
                });
                updateBulkBar();
            }
            // Individual checkbox
            if (e.target && e.target.classList.contains('bulk-checkbox')) {
                updateBulkBar();
                // Sync selectAll state
                const allCbs = document.querySelectorAll('.bulk-checkbox');
                const checkedCbs = document.querySelectorAll('.bulk-checkbox:checked');
                const selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.checked = allCbs.length > 0 && allCbs.length === checkedCbs.length;
                }
            }
        });

        function updateBulkBar() {
            const bar = document.getElementById('bulkActionBar');
            const countEl = document.getElementById('bulkCount');
            const btnActivate = document.getElementById('bulkBtnActivate');
            const btnDeactivate = document.getElementById('bulkBtnDeactivate');
            if (!bar) return;
            const checked = document.querySelectorAll('.bulk-checkbox:checked');
            if (checked.length > 0) {
                bar.style.display = 'flex';
                countEl.textContent = checked.length;

                // Smart: cek status semua yang dipilih
                const statuses = new Set();
                checked.forEach(function(cb) {
                    statuses.add(cb.dataset.status);
                });

                const onlyActive = statuses.size === 1 && statuses.has('active');
                const onlyInactive = statuses.size === 1 && statuses.has('inactive');

                // Aktifkan: tampilkan HANYA jika semua nonaktif
                if (btnActivate) btnActivate.style.display = onlyInactive ? 'inline-flex' : 'none';
                // Nonaktifkan: tampilkan HANYA jika semua aktif
                if (btnDeactivate) btnDeactivate.style.display = onlyActive ? 'inline-flex' : 'none';
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
        if (action === 'delete') {
            const confirmed = await Toast.confirm(
                'Yakin ingin menghapus <strong>' + ids.length + ' pengguna</strong> secara permanen?',
                { title: 'Hapus Pengguna?', confirmText: 'Ya, Hapus', cancelText: 'Batal', type: 'danger' }
            );
            if (!confirmed) return;
        }

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
                Toast.success(result.message);
                setTimeout(() => location.reload(), 1000);
            } else {
                Toast.error(result.message || 'Gagal melakukan aksi');
            }
        } catch (e) {
            Toast.error('Terjadi kesalahan jaringan');
        }
    }
    document.addEventListener('DOMContentLoaded', initBulkSelection);

    // ========== COPY USER CREDENTIALS ==========
    function copyUserCredentials(userId, name, email) {
        const text = 'Halo ' + name + ',\n\n' +
            'Akun Anda di Admin Panel PKK Kabupaten Toba sudah dibuat.\n' +
            'Berikut kredensial login Anda:\n\n' +
            'Email    : ' + email + '\n' +
            'Password : (gunakan password yang diberikan admin)\n\n' +
            'Silakan login di: ' + window.location.origin + '\n' +
            'Ganti password setelah login pertama kali untuk keamanan.\n\n' +
            'Terima kasih,';

        robustCopy(text);
    }

    function robustCopy(text) {
        // Method 1: Try Clipboard API first (works on HTTPS + localhost)
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function() {
                Toast.success('Kredensial berhasil disalin ke clipboard!');
            }).catch(function() {
                // Method 2: Fallback to textarea
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
        // Make visible but off-screen so browser allows selection
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
                Toast.success('Kredensial berhasil disalin ke clipboard!');
            } else {
                showCopyManualModal(text);
            }
        } catch(e) {
            document.body.removeChild(ta);
            showCopyManualModal(text);
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
        // Modal copy button uses execCommand
        overlay.querySelector('#modalCopyBtn').addEventListener('click', function() {
            var ta = overlay.querySelector('textarea');
            ta.select();
            try {
                document.execCommand('copy');
                overlay.remove();
                Toast.success('Berhasil disalin!');
            } catch(e) { Toast.error('Gagal copy, silakan select & copy manual (Ctrl+C)'); }
        });
    }
    </script>
    @endif

    @if(session('new_account'))
    <div id="newAccountModal" class="um-modal-overlay" style="display:flex">
        <div class="um-modal-card">
            {{-- Header --}}
            <div style="text-align:center;margin-bottom:1.5rem">
                <div class="um-modal-icon-box um-modal-icon-box--green">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h3 class="um-modal-title">Akun Berhasil Dibuat!</h3>
                <p class="um-modal-subtitle">Salin kredensial di bawah untuk dikirim ke pengguna</p>
            </div>

            {{-- Credentials Card --}}
            <div class="um-cred-card">
                <div style="margin-bottom:0.75rem">
                    <span class="um-cred-label">Nama</span>
                    <div class="um-cred-value">{{ session('new_account.name') }}</div>
                </div>
                <div style="margin-bottom:0.75rem">
                    <span class="um-cred-label">Email</span>
                    <div class="um-cred-value um-cred-value--email">{{ session('new_account.email') }}</div>
                </div>
                @if(session('new_account.password'))
                <div>
                    <span class="um-cred-label">Password</span>
                    <div class="um-cred-value um-cred-value--password" id="credPassword">{{ session('new_account.password') }}</div>
                </div>
                @endif
            </div>

            {{-- Copy Buttons --}}
            <div style="display:grid;gap:0.5rem;margin-bottom:1rem">
                <button type="button" onclick="copyCredentials('full')" id="copyFullBtn" class="um-copy-btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span>Salin Pesan Lengkap</span>
                </button>
                <button type="button" onclick="copyCredentials('email')" id="copyEmailBtn" class="um-copy-btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    <span>Salin Email Saja</span>
                </button>
            </div>

            {{-- Close --}}
            <button type="button" onclick="document.getElementById('newAccountModal').style.display='none'" class="um-modal-btn-close">
                Tutup
            </button>
        </div>
    </div>
    <script>
        function copyCredentials(type) {
            const name = {{ json_encode(session('new_account.name')) }};
            const email = {{ json_encode(session('new_account.email')) }};
            const password = {{ json_encode(session('new_account.password', '')) }};

            let text = '';
            if (type === 'full') {
                text = 'Halo ' + name + ',\n\n' +
                    'Akun Anda di Admin Panel PKK Kabupaten Toba sudah dibuat.\n' +
                    'Berikut kredensial login Anda:\n\n' +
                    'Email    : ' + email + '\n' +
                    'Password : ' + password + '\n\n' +
                    'Silakan login di: ' + window.location.origin + '\n' +
                    'Ganti password setelah login pertama kali untuk keamanan.\n\n' +
                    'Terima kasih,';
            } else {
                text = email;
            }

            // Fallback untuk browser yang tidak support navigator.clipboard
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    showCopyBtnSuccess(type);
                }).catch(() => {
                    fallbackCopyModal(text, type);
                });
            } else {
                fallbackCopyModal(text, type);
            }
        }

        function fallbackCopyModal(text, type) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            textarea.style.top = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                document.execCommand('copy');
                showCopyBtnSuccess(type);
            } catch (err) {
                Toast.error('Gagal menyalin. Silakan copy manual.');
            }
            document.body.removeChild(textarea);
        }

        function showCopyBtnSuccess(type) {
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

            Toast.success('Berhasil disalin ke clipboard!');
        }
    </script>
    @endif
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
