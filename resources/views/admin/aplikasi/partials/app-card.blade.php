{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="app-card" style="padding:1.25rem;margin-bottom:1rem;background:#fff;border-radius:12px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04)">
    {{-- Header: Nama Aplikasi (tanpa logo di header) --}}
    <div style="text-align:center;margin-bottom:1rem">
        <div style="font-weight:700;color:var(--text-dark);margin-bottom:0.25rem;font-size:1.05rem">{{ $app->name }}</div>
        <div class="u-text-muted-sm">{{ $app->short_name }}</div>
    </div>

    {{-- Logo/Icon di tengah, di bawah nama aplikasi --}}
    <div style="display:flex;justify-content:center;margin-bottom:1rem">
        @if($app->icon)
            <img src="{{ asset('storage/'.$app->icon) }}" alt="{{ $app->name }}" style="width:80px;height:80px;border-radius:12px;object-fit:cover;background:#f8fafc;box-shadow:0 4px 12px rgba(0,0,0,0.08)">
        @else
            <div style="width:80px;height:80px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.5rem;box-shadow:0 4px 12px rgba(0,0,0,0.08)">
                {{ strtoupper(substr($app->short_name, 0, 2)) }}
            </div>
        @endif
    </div>

    {{-- Info Grid --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem">
        {{-- Kategori --}}
        <div class="u-box-soft">
            <div class="u-eyebrow-xs">Kategori</div>
            <div class="u-a32">{{ ucfirst($app->category) }}</div>
        </div>

        {{-- Status --}}
        <div class="u-box-soft">
            <div class="u-eyebrow-xs">Status</div>
            @include('admin.aplikasi.partials.status-badge', ['app' => $app])
        </div>

        {{-- URL --}}
        <div style="padding:0.75rem;background:#f8fafc;border-radius:8px;grid-column:span 2">
            <div class="u-eyebrow-xs">URL</div>
            @if($app->url && $app->url !== '#')
                <a href="{{ $app->url }}" target="_blank" style="color:var(--primary);text-decoration:none;font-size:0.85rem;word-break:break-all;border-bottom:1px dotted var(--primary)">{{ $app->url }}</a>
            @else
                <span class="u-a31">-</span>
            @endif
        </div>

        {{-- Urutan --}}
        <div class="u-box-soft">
            <div class="u-eyebrow-xs">Urutan</div>
            <div class="u-a32">{{ $app->sort_order }}</div>
        </div>

        {{-- Aktif/Nonaktif --}}
        <div class="u-box-soft">
            <div class="u-eyebrow-xs">Status Tampil</div>
            <div style="font-weight:600;color:{{ $app->is_active ? '#166534' : '#92400e' }};font-size:0.85rem">
                {{ $app->is_active ? '✓ Aktif' : '○ Nonaktif' }}
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div style="display:flex;gap:0.5rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06)">
        <a href="{{ route('admin.aplikasi.edit', $app) }}" class="btn-edit u-a54" title="Edit">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <span class="u-a19">Edit</span>
        </a>
        <button type="button" data-delete-app-id="{{ $app->id }}" data-delete-app-name="{{ addslashes($app->name) }}" class="btn-del" title="Hapus" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;border:none;cursor:pointer;transition:all 0.2s">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
            </svg>
            <span class="u-a19">Hapus</span>
        </button>
        <form id="delete-app-{{ $app->id }}" action="{{ route('admin.aplikasi.destroy', $app) }}" method="POST" style="display:none">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
