<div class="topbar">
    <h2 id="pageTitle">@yield('page-title', 'Dashboard')</h2>
    <div>
        <button class="btn btn-outline" style="margin-right:8px">
            👤 {{ auth()->user()->name ?? 'Admin' }}
        </button>
        {{-- Tombol "+ Tambah" sudah dipindah ke dalam masing-masing halaman --}}
    </div>
</div>