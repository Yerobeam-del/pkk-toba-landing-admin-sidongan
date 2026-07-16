@extends('sidongan.layouts.app')
@section('title', 'Disposisi Surat - SIDONGAN')

@section('content')
<style>
.disposisi-item {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 3px solid transparent;
    position: relative;
    overflow: hidden;
}
.disposisi-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: linear-gradient(180deg, #f97316, #ea580c);
    opacity: 0;
    transition: opacity 0.3s;
}
.disposisi-item:hover {
    background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
    border-left-color: #f97316;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}
.disposisi-item:hover::before {
    opacity: 1;
}
.btn-action {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
.stats-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.stats-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(249, 115, 22, 0.2);
}
@keyframes slideIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-slide-in {
    animation: slideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    opacity: 0;
}
</style>

<div style="padding: 0 1.5rem;">
    {{-- Header --}}
    <div style="margin-bottom: 2rem;" class="animate-slide-in">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">
            Disposisi Surat
        </h1>
        <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">
            Kelola disposisi surat untuk diteruskan ke pelaksana
        </p>
    </div>

    {{-- Stats Cards --}}
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        {{-- Menunggu Disposisi - WARNA ORANGE --}}
        <div class="stats-card animate-slide-in" 
             style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-hourglass-half" style="font-size: 2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                        Menunggu Disposisi
                    </p>
                    <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">
                        {{ $documents->total() ?? 0 }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Sudah Didisposisi - WARNA HIJAU --}}
        <div class="stats-card animate-slide-in" 
             style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-check-circle" style="font-size: 2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                        Sudah Didisposisi
                    </p>
                    <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">
                        {{ \App\Models\Document::where('status', 'berjalan')->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Total Surat Bulan Ini - WARNA BIRU --}}
        <div class="stats-card animate-slide-in" 
             style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 1rem; padding: 1.5rem; color: white; position: relative; overflow: hidden; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);">
            <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%; backdrop-filter: blur(10px);"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
            
            <div style="display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1;">
                <div style="width: 4.5rem; height: 4.5rem; background: rgba(255,255,255,0.25); border-radius: 1rem; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-calendar-check" style="font-size: 2rem;"></i>
                </div>
                <div style="flex: 1;">
                    <p style="font-size: 0.9rem; opacity: 0.95; margin: 0 0 0.5rem 0; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">
                        Surat Bulan Ini
                    </p>
                    <p style="font-size: 3rem; font-weight: 800; margin: 0; line-height: 1; letter-spacing: -0.05em;">
                        {{ \App\Models\Document::whereMonth('created_at', now()->month)->count() }}
                    </p>
                    <p style="font-size: 0.8rem; opacity: 0.9; margin: 0.25rem 0 0 0; font-weight: 500;">
                        {{ now()->locale('id')->translatedFormat('F Y') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div style="background: white; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.1);" class="animate-slide-in">
        <form id="filterForm" method="GET" action="{{ route('sidongan.disposisi') }}">
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; align-items: end;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Cari Surat
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.875rem;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                            placeholder="Cari berdasarkan judul, nomor, atau pengirim..." 
                            style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.75rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; transition: all 0.2s;"
                            onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249, 115, 22, 0.1)'"
                            onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                    </div>
                </div>
                
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Urutkan
                    </label>
                    <div style="position: relative;">
                        <select name="sort" style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; background: white; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem; transition: all 0.2s;"
                                onchange="document.getElementById('filterForm').submit()"
                                onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249, 115, 22, 0.1)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="agenda" {{ request('sort') == 'agenda' ? 'selected' : '' }}>No. Agenda</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">
                        Tampilkan
                    </label>
                    <div style="position: relative;">
                        <select name="per_page" id="perPageSelect" style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.9rem; background: white; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%239ca3af\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6 9 12 15 18 9\'%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1rem; transition: all 0.2s;"
                                onchange="document.getElementById('filterForm').submit()"
                                onfocus="this.style.borderColor='#f97316'; this.style.boxShadow='0 0 0 3px rgba(249, 115, 22, 0.1)'"
                                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                            @foreach([5, 10, 15, 25, 50] as $value)
                                <option value="{{ $value }}" {{ (request('per_page', 10) == $value) ? 'selected' : '' }}>
                                    {{ $value }} surat
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Daftar Surat Disposisi --}}
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; overflow: hidden;" class="animate-slide-in">        
        @if(isset($documents) && $documents->count() > 0)
            <div style="padding: 0;">
                @foreach($documents as $index => $doc)
                    {{-- Item Disposisi --}}
                    <div class="disposisi-item animate-slide-in" 
                        style="padding: 1.5rem 1.75rem; border-bottom: {{ $loop->last ? 'none' : '1px solid #f3f4f6' }}; 
                                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                                border-left: 3px solid transparent;
                                position: relative;
                                overflow: hidden;"
                        onmouseover="this.style.background='#fff7ed'; this.style.borderLeftColor='#f97316'; this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" 
                        onmouseout="this.style.background='white'; this.style.borderLeftColor='transparent'; this.style.transform='translateX(0)'; this.style.boxShadow='none'">
                        
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.8rem; font-family: monospace; background: white; color: #ea580c; padding: 0.375rem 0.75rem; border-radius: 0.5rem; font-weight: 700; border: 1px solid #fed7aa; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        {{ $doc->agenda_number }}
                                    </span>
                                    <span style="font-size: 0.75rem; padding: 0.375rem 0.875rem; border-radius: 9999px; font-weight: 600; background: #fef3c7; color: #92400e; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                        Menunggu Disposisi
                                    </span>
                                </div>
                                
                                <h4 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0 0 0.75rem 0; line-height: 1.4;">
                                    {{ $doc->subject ?? $doc->title }}
                                </h4>
                                
                                <div style="display: flex; gap: 1.5rem; font-size: 0.875rem; color: #64748b; flex-wrap: wrap; margin-bottom: 0.75rem;">
                                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 1.5rem; height: 1.5rem; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="color: #f97316; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $doc->sender }}
                                    </span>
                                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 1.5rem; height: 1.5rem; background: #fff7ed; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-calendar" style="color: #f97316; font-size: 0.7rem;"></i>
                                        </div>
                                        {{ $doc->document_date->locale('id')->translatedFormat('d M Y') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem; flex-shrink: 0; flex-direction: column;">
                                <a href="{{ route('sidongan.disposisi.form', $doc->id) }}?from={{ urlencode(route('sidongan.disposisi')) }}" 
                                    class="btn-action"
                                    style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #f97316, #ea580c); color: white; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(249, 115, 22, 0.2);">
                                        <i class="fas fa-share"></i>
                                        <span>Disposisi</span>
                                </a>
                                <a href="{{ route('sidongan.documents.show', $doc) }}?from={{ urlencode(route('sidongan.disposisi')) }}" 
                                    style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; background: white; color: #f97316; border: 2px solid #fed7aa; text-decoration: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.05); transition: all 0.2s;"
                                    onmouseover="this.style.background='#fff7ed'; this.style.borderColor='#f97316'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(249, 115, 22, 0.15)'" 
                                    onmouseout="this.style.background='white'; this.style.borderColor='#fed7aa'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.05)'">
                                        <i class="fas fa-eye"></i>
                                        <span>Detail</span>
                                </a>
                            </div>
                        </div>
                        
                        @if($doc->suggestion)
                        <div style="background: #fff7ed; border-left: 3px solid #f97316; border-radius: 0.5rem; padding: 0.875rem 1rem;">
                            <div style="display: flex; align-items: start; gap: 0.75rem;">
                                <div style="width: 2rem; height: 2rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <i class="fas fa-comment-alt" style="color: #f97316; font-size: 0.875rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <p style="font-size: 0.8rem; color: #92400e; margin: 0 0 0.375rem 0; font-weight: 700;">Saran Sekretaris</p>
                                    <p style="font-size: 0.875rem; color: #64748b; margin: 0; line-height: 1.5; font-style: italic;">
                                        "{{ Str::limit($doc->suggestion, 100) }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Pagination Modern --}}
            @if($documents->hasPages())
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.875rem; color: #64748b;">
                    Menampilkan <strong>{{ $documents->firstItem() }}</strong> - <strong>{{ $documents->lastItem() }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
                </div>
                
                <div style="display: flex; gap: 0.35rem; align-items: center;">
                    @if($documents->onFirstPage())
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f1f5f9; color: #94a3b8; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    @else
                        <a href="{{ $documents->previousPageUrl() }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f97316; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    @endif
                    
                    @php
                        $currentPage = $documents->currentPage();
                        $lastPage = $documents->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                        
                        if ($endPage - $startPage < 4) {
                            if ($startPage == 1) $endPage = min(5, $lastPage);
                            if ($endPage == $lastPage) $startPage = max(1, $lastPage - 4);
                        }
                    @endphp
                    
                    @if($startPage > 1)
                        <a href="{{ $documents->url(1) }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                            1
                        </a>
                        @if($startPage > 2)
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; color: #94a3b8; font-size: 0.875rem;">...</span>
                        @endif
                    @endif
                    
                    @for($i = $startPage; $i <= $endPage; $i++)
                        @if($i == $currentPage)
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f97316; color: white; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 600;">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $documents->url($i) }}" 
                            style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor
                    
                    @if($endPage < $lastPage)
                        @if($endPage < $lastPage - 1)
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; color: #94a3b8; font-size: 0.875rem;">...</span>
                        @endif
                        <a href="{{ $documents->url($lastPage) }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: white; color: #475569; border: 1px solid #e2e8f0; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">
                            {{ $lastPage }}
                        </a>
                    @endif
                    
                    @if($documents->hasMorePages())
                        <a href="{{ $documents->nextPageUrl() }}" 
                        style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f97316; color: white; border-radius: 0.375rem; font-size: 0.875rem; text-decoration: none; transition: all 0.2s;"
                        onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    @else
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; background: #f1f5f9; color: #94a3b8; border-radius: 0.375rem; font-size: 0.875rem; cursor: not-allowed;">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    @endif
                </div>
            </div>
            @else
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="font-size: 0.875rem; color: #64748b;">
                    Menampilkan <strong>{{ $documents->firstItem() ?? 0 }}</strong> - <strong>{{ $documents->lastItem() ?? 0 }}</strong> dari <strong>{{ $documents->total() }}</strong> surat
                </div>
                <div style="font-size: 0.875rem; color: #94a3b8;">
                    <i class="fas fa-info-circle"></i> Semua surat ditampilkan dalam satu halaman
                </div>
            </div>
            @endif
        @else
            {{-- Empty State --}}
            <div style="text-align: center; padding: 3rem 1.5rem;" class="animate-slide-in">
                <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 50%; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); animation: float 3s ease-in-out infinite;">
                    <i class="fas fa-check-double" style="color: #10b981; font-size: 3rem;"></i>
                </div>
                <h4 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">Semua Surat Sudah Didisposisi</h4>
                <p style="font-size: 0.95rem; color: #64748b; margin: 0; line-height: 1.6;">
                    Tidak ada surat yang menunggu disposisi saat ini.
                </p>
                <a href="{{ route('sidongan.documents.index') }}" 
                   class="btn-action"
                   style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; text-decoration: none; border-radius: 0.625rem; font-weight: 600; margin-top: 1.5rem; box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar Surat</span>
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    // Auto-submit search after delay
    searchInput?.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });
</script>
@endsection