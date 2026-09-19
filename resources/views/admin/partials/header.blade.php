{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
<div class="topbar">
    <h2 id="pageTitle">@yield('page-title', 'Dashboard')</h2>
    <div>
        <button class="btn btn-outline" style="margin-right:8px;display:inline-flex;align-items:center;gap:0.4rem">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ auth()->user()->name ?? 'Admin' }}
        </button>
        {{-- Tombol "+ Tambah" sudah dipindah ke dalam masing-masing halaman --}}
    </div>
</div>
{{-- Dikembangkan oleh Institut Teknologi Del --}}
