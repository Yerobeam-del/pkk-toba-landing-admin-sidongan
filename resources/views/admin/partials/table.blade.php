@props(['data' => [], 'emptyMessage' => 'Belum ada data', 'editRoute' => null, 'deleteRoute' => null])

<div class="desktop-table-view">
    @if(count($data) > 0)
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <span class="header-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                        </span>
                        <span>Foto</span>
                    </th>
                    <th>
                        <span class="header-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <span>Nama</span>
                    </th>
                    <th>
                        <span class="header-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                        </span>
                        <span>Jabatan</span>
                    </th>

                    <th class="text-right">
                        <span>Aksi</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    <td>
                        @if($item->photo_path)
                            <img src="{{ asset('storage/'.$item->photo_path) }}" alt="{{ $item->name }}" class="member-photo">
                        @else
                            <div class="member-photo-placeholder">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="member-name">{{ $item->name }}</td>
                    <td>
                        <span class="position-badge">{{ $item->position }}</span>
                    </td>
                    <td class="actions-cell">
                        <div class="actions-container">
                            @if($editRoute)
                                <a href="{{ route($editRoute, $item) }}" class="action-btn btn-edit" title="Edit">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>
                            @endif
                            @if($deleteRoute)
                                <button type="button" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')" class="action-btn btn-delete" title="Hapus">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                    </svg>
                                </button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route($deleteRoute, $item) }}" method="POST" class="d-none">
                                    @csrf 
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <h3>Belum Ada Data</h3>
            <p>{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

<div class="mobile-card-view">
    @if(count($data) > 0)
        <div class="mobile-card-wrapper">
            @foreach($data as $item)
            <div class="member-card">
                <div class="member-card-header">
                    @if($item->photo_path)
                        <img src="{{ asset('storage/'.$item->photo_path) }}" alt="{{ $item->name }}" class="member-card-photo">
                    @else
                        <div class="member-card-photo-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    @endif
                    <div class="member-card-info">
                        <div class="member-card-name">{{ $item->name }}</div>
                        <span class="member-card-position">{{ $item->position }}</span>
                    </div>
                </div>
                <div class="member-card-actions">
                    @if($editRoute)
                        <a href="{{ route($editRoute, $item) }}" class="action-btn btn-edit" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span>Edit</span>
                        </a>
                    @endif
                    @if($deleteRoute)
                        <button type="button" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')" class="action-btn btn-delete" title="Hapus">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <line x1="10" y1="11" x2="10" y2="17"/>
                                <line x1="14" y1="11" x2="14" y2="17"/>
                            </svg>
                            <span>Hapus</span>
                        </button>
                        <form id="delete-form-{{ $item->id }}" action="{{ route($deleteRoute, $item) }}" method="POST" class="d-none">
                            @csrf 
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon-wrapper">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <h3>Belum Ada Data</h3>
            <p>{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

<script>
if (typeof window.confirmDeleteItem === 'undefined') {
    window.confirmDeleteItem = function(id, name) {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            Toast.confirm(
                `Data <strong>"${name}"</strong> akan dihapus secara permanen.`,
                {
                    title: 'Hapus Data?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            ).then(function(confirmed) {
                if (confirmed) {
                    const form = document.getElementById('delete-form-' + id);
                    if (form) form.submit();
                }
            });
        } else {
            if (confirm(`Hapus data "${name}"?`)) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        }
    };
}
</script>