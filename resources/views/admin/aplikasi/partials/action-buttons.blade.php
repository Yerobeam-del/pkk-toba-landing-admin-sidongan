{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="actions" style="justify-content:flex-end;gap:0.5rem;display:flex">
    <a href="{{ route('admin.aplikasi.edit', $app) }}" class="btn-edit u-a18" title="Edit">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
    </a>
    <button type="button" data-delete-app-id="{{ $app->id }}" data-delete-app-name="{{ addslashes($app->name) }}" class="btn-del" title="Hapus" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border-radius:6px;transition:all 0.2s;border:none;cursor:pointer">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
    </button>
    <form id="delete-app-{{ $app->id }}" action="{{ route('admin.aplikasi.destroy', $app) }}" method="POST" style="display:none">
        @csrf 
        @method('DELETE')
    </form>
</div>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
