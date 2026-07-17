@props([
    'data' => [], 
    'columns' => [], 
    'emptyMessage' => 'Belum ada data', 
    'editRoute' => null, 
    'deleteRoute' => null,
    'showRoute' => null,
    'actions' => ['edit', 'delete', 'show'],
    'rowActions' => null
])

<div class="desktop-table-view">
    @if(count($data) > 0)
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($columns as $column)
                        <th style="{{ $column['align'] ?? 'left' === 'right' ? 'text-align: right' : 'text-align: left' }}">
                            @if(isset($column['icon']))
                                <span class="header-icon">
                                    {!! $column['icon'] !!}
                                </span>
                            @endif
                            <span>{{ $column['label'] }}</span>
                        </th>
                    @endforeach
                    
                    @if(count($actions) > 0)
                        <th class="text-right">
                            <span>Aksi</span>
                        </th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($data as $item)
                <tr>
                    @foreach($columns as $column)
                        @php
                            $value = data_get($item, $column['key']);
                            $columnType = $column['type'] ?? 'text';
                        @endphp
                        
                        <td style="padding: 1rem; {{ ($column['align'] ?? 'left') === 'right' ? 'text-align: right' : 'text-align: left' }}">
                            @if($columnType === 'image')
                                @if($value)
                                    <img src="{{ asset('storage/' . $value) }}" alt="{{ data_get($item, $column['alt_key'] ?? 'name') }}" class="member-photo" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                @else
                                    <div class="member-photo-placeholder" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), #0d9488); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700;">
                                        {{ strtoupper(substr(data_get($item, $column['initial_key'] ?? 'name'), 0, 1)) }}
                                    </div>
                                @endif
                            
                            @elseif($columnType === 'badge')
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; {{ $column['badge_style'] ?? 'background: rgba(34,197,94,0.1); color: #166534;' }}">
                                    @if(isset($column['badge_icon']))
                                        {!! $column['badge_icon'] !!}
                                    @endif
                                    {{ $value }}
                                </span>
                            
                            @elseif($columnType === 'callback')
                                {!! $column['callback']($item, $value) !!}
                            
                            @elseif($columnType === 'html')
                                {!! $value !!}
                            
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    @endforeach
                    
                    {{-- Actions column --}}
                    @if(count($actions) > 0)
                        <td class="actions-cell" style="padding: 1rem; text-align: right;">
                            <div class="actions-container" style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                                @if(in_array('show', $actions) && $showRoute)
                                    <a href="{{ route($showRoute, $item) }}" class="action-btn" title="Lihat" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#eff6ff'; this.style.color='#2563eb'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                @endif
                                
                                @if(in_array('edit', $actions) && $editRoute)
                                    <a href="{{ route($editRoute, $item) }}" class="action-btn btn-edit" title="Edit" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; transition: all 0.2s; cursor: pointer;" onmouseover="this.style.background='#eff6ff'; this.style.color='#2563eb'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </a>
                                @endif
                                
                                @if(in_array('delete', $actions) && $deleteRoute)
                                    <button type="button" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes(data_get($item, 'name')) }}')" class="action-btn btn-delete" title="Hapus" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: transparent; color: #94a3b8; border-radius: 6px; border: none; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#fef2f2'; this.style.color='#ef4444'" onmouseout="this.style.background='transparent'; this.style.color='#94a3b8'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                                
                                @if(isset($rowActions))
                                    {!! $rowActions($item) !!}
                                @endif
                            </div>
                        </td>
                    @endif
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
                    @foreach($columns as $column)
                        @if($column['type'] ?? 'text' === 'image')
                            @php
                                $value = data_get($item, $column['key']);
                            @endphp
                            @if($value)
                                <img src="{{ asset('storage/' . $value) }}" alt="{{ data_get($item, $column['alt_key'] ?? 'name') }}" class="member-card-photo">
                            @else
                                <div class="member-card-photo-placeholder">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="member-card-info">
                                @foreach($columns as $infoColumn)
                                    @if($infoColumn['key'] !== $column['key'])
                                        <div class="member-card-name">{{ data_get($item, $infoColumn['key']) }}</div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="member-card-actions">
                    @if(in_array('edit', $actions) && $editRoute)
                        <a href="{{ route($editRoute, $item) }}" class="action-btn btn-edit" title="Edit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span>Edit</span>
                        </a>
                    @endif
                    @if(in_array('delete', $actions) && $deleteRoute)
                        <button type="button" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes(data_get($item, 'name')) }}')" class="action-btn btn-delete" title="Hapus">
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