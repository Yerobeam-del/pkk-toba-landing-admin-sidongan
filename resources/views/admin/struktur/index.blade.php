@extends('admin.layouts.app')
@section('title', 'Manajemen Struktur')
@section('page-title', 'Struktur Organisasi')

@section('content')
<div style="margin-bottom:2rem">
    
    {{-- Header Section --}}
    <div class="struktur-header">
        <div style="flex:1;min-width:0">
            <h1>Struktur Organisasi</h1>
            <p>Kelola data sesuai bagan organisasi asli PKK Kabupaten Toba</p>
        </div>
        <a href="{{ route('admin.struktur.create') }}" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:0.5rem;white-space:nowrap;flex-shrink:0">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Anggota
        </a>
    </div>

    {{-- TABS --}}
    <div class="tabs-container">
        <button class="tab-btn active" onclick="switchTab('pengurus', this)">
            Pengurus Inti
        </button>
        <button class="tab-btn" onclick="switchTab('pokja1', this)">
            POKJA I
        </button>
        <button class="tab-btn" onclick="switchTab('pokja2', this)">
            POKJA II
        </button>
        <button class="tab-btn" onclick="switchTab('pokja3', this)">
            POKJA III
        </button>
        <button class="tab-btn" onclick="switchTab('pokja4', this)">
            POKJA IV
        </button>
    </div>

    {{-- Main Card --}}
    <div class="struktur-card">
        
        {{-- Tab 1: Pengurus Inti --}}
        <div id="tab-pengurus" class="tab-content active">
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $pengurusInti ?? [], 
                    'emptyMessage' => 'Belum ada data pengurus inti.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy'
                ])
            </div>
        </div>

        {{-- Tab 2: Pokja I --}}
        <div id="tab-pokja1" class="tab-content">
            @php $pokja1 = $pokjaList->find(1); @endphp
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $pokja1->members ?? [], 
                    'emptyMessage' => 'Belum ada anggota di Pokja I.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy'
                ])
            </div>
        </div>

        {{-- Tab 3: Pokja II --}}
        <div id="tab-pokja2" class="tab-content">
            @php $pokja2 = $pokjaList->find(2); @endphp
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $pokja2->members ?? [], 
                    'emptyMessage' => 'Belum ada anggota di Pokja II.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy'
                ])
            </div>
        </div>

        {{-- Tab 4: Pokja III --}}
        <div id="tab-pokja3" class="tab-content">
            @php $pokja3 = $pokjaList->find(3); @endphp
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $pokja3->members ?? [], 
                    'emptyMessage' => 'Belum ada anggota di Pokja III.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy'
                ])
            </div>
        </div>

        {{-- Tab 5: Pokja IV --}}
        <div id="tab-pokja4" class="tab-content">
            @php $pokja4 = $pokjaList->find(4); @endphp
            <div class="table-container">
                @include('admin.partials.table', [
                    'data' => $pokja4->members ?? [], 
                    'emptyMessage' => 'Belum ada anggota di Pokja IV.',
                    'editRoute' => 'admin.struktur.edit',
                    'deleteRoute' => 'admin.struktur.destroy'
                ])
            </div>
        </div>

    </div>
</div>

<script>
function switchTab(tabId, btn) {
    // Reset all tabs
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
    });
    
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.remove('active');
    });
    
    // Activate selected
    btn.classList.add('active');
    document.getElementById('tab-' + tabId).classList.add('active');
}

// Init first tab on load
document.addEventListener('DOMContentLoaded', () => {
    const firstBtn = document.querySelector('.tab-btn');
    if (firstBtn) switchTab('pengurus', firstBtn);
});
</script>
@endsection