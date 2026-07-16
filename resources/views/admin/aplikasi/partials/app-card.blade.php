<div style="padding:1rem;margin-bottom:1rem;background:#fff;border-radius:12px;border:1px solid rgba(0,0,0,0.06);box-shadow:0 2px 8px rgba(0,0,0,0.04)">
    {{-- Header: Logo + Nama --}}
    <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem">
        @if($app->icon)
        <img src="{{ asset('storage/'.$app->icon) }}" style="width:50px;height:50px;border-radius:10px;object-fit:cover;background:#f8fafc">
        @else
        <div style="width:50px;height:50px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:0.9rem">
            {{ strtoupper(substr($app->short_name, 0, 2)) }}
        </div>
        @endif
        <div style="flex:1;min-width:0">
            <div style="font-weight:600;color:var(--text-dark);margin-bottom:0.25rem">{{ $app->name }}</div>
            <div style="font-size:0.85rem;color:var(--text-muted)">{{ $app->short_name }}</div>
        </div>
    </div>
    
    {{-- Info Grid --}}
    <div style="display:grid;gap:0.75rem;margin-bottom:1rem">
        {{-- Kategori + Status --}}
        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
            <span style="background:{{ $app->category == 'aplikasi' ? 'rgba(20,184,166,0.1)' : 'rgba(59,130,246,0.1)' }};color:{{ $app->category == 'aplikasi' ? 'var(--primary)' : '#2563eb' }};padding:4px 10px;border-radius:20px;font-size:0.75rem;font-weight:600">
                {{ ucfirst($app->category) }}
            </span>
            @include('admin.aplikasi.partials.status-badge', ['app' => $app])
        </div>
        
        {{-- URL --}}
        @if($app->url && $app->url !== '#')
        <div style="font-size:0.85rem;color:var(--text-muted);word-break:break-all">
            <span style="color:var(--text-muted);font-size:0.8rem">URL:</span>
            <a href="{{ $app->url }}" target="_blank" style="color:var(--primary);text-decoration:none;margin-left:0.5rem">{{ Str::limit($app->url, 40) }}</a>
        </div>
        @endif
        
        {{-- Urutan --}}
        <div style="font-size:0.85rem;color:var(--text-muted)">
            <span style="color:var(--text-muted);font-size:0.8rem">Urutan:</span> <strong>{{ $app->sort_order }}</strong>
        </div>
    </div>
    
    {{-- Action Buttons --}}
    <div style="display:flex;gap:0.5rem;margin-top:1rem;padding-top:1rem;border-top:1px solid rgba(0,0,0,0.06)">
        <a href="{{ route('admin.aplikasi.edit', $app) }}" class="btn-edit" title="Edit" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;transition:all 0.2s;cursor:pointer" onmouseover="this.style.background='#eff6ff';this.style.color='#2563eb'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </a>
        <button type="button" onclick="confirmDeleteApp({{ $app->id }}, '{{ addslashes($app->name) }}')" class="btn-del" title="Hapus" style="flex:1;height:40px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:8px;border:none;cursor:pointer;transition:all 0.2s" onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444'" onmouseout="this.style.background='transparent';this.style.color='#94a3b8'">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </button>
        <form id="delete-app-{{ $app->id }}" action="{{ route('admin.aplikasi.destroy', $app) }}" method="POST" style="display:none">
            @csrf 
            @method('DELETE')
        </form>
    </div>
</div>