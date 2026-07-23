<div style="padding:1.25rem;margin-bottom:1rem;background:#fff;border-radius:12px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04)">
    {{-- Nama Aplikasi --}}
    <div style="font-weight:700;color:var(--text-dark);font-size:1.05rem;margin-bottom:0.25rem;text-align:center">{{ $app->name }}</div>
    <div style="font-size:0.85rem;color:var(--text-muted);text-align:center;margin-bottom:1rem">{{ $app->short_name }}</div>

    {{-- Icon/Logo di Tengah --}}
    <div style="display:flex;justify-content:center;margin-bottom:1rem">
        @if($app->icon)
            <img src="{{ asset('storage/'.$app->icon) }}" alt="{{ $app->name }}" style="width:80px;height:80px;border-radius:12px;object-fit:cover;background:#f8fafc;border:1px solid rgba(0,0,0,0.06)">
        @else
            <div style="width:80px;height:80px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.5rem">
                {{ strtoupper(substr($app->short_name, 0, 2)) }}
            </div>
        @endif
    </div>

    {{-- Info Grid --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem">
        <div style="padding:0.75rem;background:#f8fafc;border-radius:8px">
            <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem">Kategori</div>
            <div style="font-weight:600;color:var(--text-dark);font-size:0.9rem">{{ ucfirst($app->category) }}</div>
        </div>
        <div style="padding:0.75rem;background:#f8fafc;border-radius:8px">
            <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem">Status</div>
            @include('admin.aplikasi.partials.status-badge', ['app' => $app])
        </div>
        <div style="padding:0.75rem;background:#f8fafc;border-radius:8px;grid-column:span 2">
            <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem">URL</div>
            @if($app->url && $app->url !== '#')
                <a href="{{ $app->url }}" target="_blank" style="color:var(--primary);text-decoration:none;font-size:0.85rem;word-break:break-all;border-bottom:1px dotted var(--primary)">{{ $app->url }}</a>
            @else
                <span style="color:var(--text-muted);font-size:0.85rem">-</span>
            @endif
        </div>
        <div style="padding:0.75rem;background:#f8fafc;border-radius:8px">
            <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem">Urutan</div>
            <div style="font-weight:600;color:var(--text-dark);font-size:0.9rem">{{ $app->sort_order }}</div>
        </div>
        <div style="padding:0.75rem;background:#f8fafc;border-radius:8px">
            <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:0.25rem">Status Tampil</div>
            <div style="font-weight:600;color:{{ $app->is_active ? '#166534' : '#92400e' }};font-size:0.85rem">
                {{ $app->is_active ? '✓ Aktif' : '○ Nonaktif' }}
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="display:flex;gap:0.5rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06)">
        <a href="{{ route('admin.aplikasi.edit', $app) }}" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;transition:all 0.2s;cursor:pointer;text-decoration:none" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <span style="margin-left:0.5rem;font-weight:600">Edit</span>
        </a>
        <button type="button" onclick="confirmDeleteApp({{ $app->id }}, '{{ addslashes($app->name) }}')" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;border:none;cursor:pointer;transition:all 0.2s" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
            <span style="margin-left:0.5rem;font-weight:600">Hapus</span>
        </button>
        <form id="delete-app-{{ $app->id }}" action="{{ route('admin.aplikasi.destroy', $app) }}" method="POST" style="display:none">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
