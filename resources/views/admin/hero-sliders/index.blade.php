{{-- ============================================================
     Dikembangkan oleh Institut Teknologi Del
     ============================================================ --}}
@extends('admin.layouts.app')
@section('title', 'Manajemen Hero Slider')
@section('page-title', 'Kelola Slider Beranda')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-hero-sliders-index.css') }}">


<div class="u-mb-8">

    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
    <div class="error-alert">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div class="u-flex-1">
            <strong>Batas Maksimal Tercapai</strong>
            <p>{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- INFO COUNTER & PROGRESS BAR --}}
    <div class="capacity-card">
        <div class="capacity-card-header">
            <div class="capacity-card-info">
                <div class="capacity-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                <div>
                    <div class="capacity-card-title">Kapasitas Slider Beranda</div>
                    <div class="capacity-card-subtitle">Maksimal {{ $maxSliders }} gambar dapat diupload</div>
                </div>
            </div>

            <div class="capacity-counter">
                <div class="counter-number {{ $totalSliders >= $maxSliders ? 'text-danger' : 'text-primary' }}">
                    {{ $totalSliders }}<span style="font-size:1rem;color:var(--text-muted);font-weight:500">/{{ $maxSliders }}</span>
                </div>
                <div class="counter-text">
                    @if($totalSliders >= $maxSliders)
                        <span style="color:#ef4444;font-weight:600">Penuh</span>
                    @else
                        Sisa {{ $maxSliders - $totalSliders }} slot
                    @endif
                </div>
            </div>
        </div>

        <div class="slider-progress-bar">
            @php
                $percentage = ($totalSliders / $maxSliders) * 100;
                $progressClass = $percentage >= 80 ? 'high' : ($percentage >= 50 ? 'medium' : 'low');
            @endphp
            <div class="slider-progress-fill {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
        </div>
    </div>

    {{-- FORM TAMBAH SLIDE --}}
    @if($totalSliders < $maxSliders)
    <div class="add-slide-card">
        <div class="add-slide-header">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8b5cf6" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <h3 class="add-slide-title">Tambah Slide Baru</h3>
        </div>
        <p class="add-slide-description">Upload gambar background untuk slider beranda. Teks konten tetap menggunakan desain yang sudah ada.</p>

        <form action="{{ route('admin.hero-sliders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">Gambar Background <span class="required">*</span></label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
                <div class="form-helper">
                    <span>Format: JPG, PNG, WebP</span>
                    <span>Maksimal: 5MB</span>
                    <span>Rekomendasi: 1920x1080px (16:9)</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="margin-bottom:0">
                    <label class="form-label">Durasi Tampil (detik)</label>
                    <input type="number" name="display_duration" class="form-control" value="5" min="3" max="30">
                </div>
                <div style="padding-bottom:0.25rem">
                    <label class="checkbox-wrapper">
                        <input class="u-hidden" type="checkbox" name="is_active" id="isActive" value="1" checked>
                        <div class="checkbox-box checked" id="isActiveBox">
                            <svg id="isActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <span class="checkbox-label">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tambah Slide
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="max-limit-warning">
        <div class="max-limit-warning-icon">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
        <h3>Batas Maksimal Tercapai</h3>
        <p>Anda sudah mengupload <strong>{{ $totalSliders }}</strong> dari <strong>{{ $maxSliders }}</strong> gambar yang diizinkan.<br>Hapus beberapa gambar terlebih dahulu untuk dapat mengupload yang baru.</p>
    </div>
    @endif

    {{-- Daftar Slide --}}
    <div class="slider-list-card">
        <div class="slider-list-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem">
            <div class="slider-list-title u-flex-center-gap-3">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke-width="2">
                    <line x1="8" y1="6" x2="21" y2="6"/>
                    <line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                <h3 style="margin:0">Daftar Slide</h3>
            </div>
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
                <div class="slider-list-hints">
                    <small class="desktop-only">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"/>
                            <line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                        </svg>
                        Drag & drop untuk mengurutkan
                    </small>
                    <small class="text-primary">• Slide baru otomatis di urutan terakhir</small>
                </div>
                {{-- Dropdown Tampilkan --}}
                <form class="u-flex-center-gap-2" method="GET" action="{{ route('admin.hero-sliders.index') }}">
                    <label class="u-a3">Tampilkan:</label>
                    <div class="u-relative">
                        <select name="per_page" class="form-control" style="padding:0.5rem 2.5rem 0.5rem 0.75rem;border-radius:8px;font-size:0.9rem;min-width:80px;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;transition:all 0.2s">
                            @for($i = 5; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $perPage == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <svg class="u-select-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                </form>
            </div>
        </div>

        <div id="slidersList" data-reorder-url="{{ route('admin.hero-sliders.reorder') }}">
            @forelse($sliders as $slider)
            <div class="slider-item" data-id="{{ $slider->id }}" draggable="true">
                <div class="drag-handle desktop-only">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="8" y1="6" x2="21" y2="6"/>
                        <line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                    </svg>
                </div>
                <img class="slider-image" src="{{ $slider->image_url }}" alt="Slide {{ $slider->id }}">
                <div class="slider-info">
                    <div class="slider-title">Slide #{{ $slider->id }}</div>
                    <div class="slider-path">{{ Str::limit($slider->image_path, 40) }}</div>
                    <div class="slider-meta">
                        <span>{{ $slider->display_duration }}s</span>
                        <span class="{{ $slider->is_active ? 'status-active' : 'status-inactive' }}">
                            ● {{ $slider->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>
                <div class="slider-actions">
                    <a href="{{ $slider->image_url }}" target="_blank" title="Preview" class="btn-view">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </a>
                    <button data-edit-slider="{{ $slider->id }}" title="Edit" class="btn-edit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button data-delete-slider="{{ $slider->id }}" data-delete-slider-title="Slide #{{ $slider->id }}" title="Hapus" class="btn-del">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <line x1="10" y1="11" x2="10" y2="17"/>
                            <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                    </button>
                    <form id="delete-form-{{ $slider->id }}" action="{{ route('admin.hero-sliders.destroy', $slider) }}" method="POST" style="display:none">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <line x1="3" y1="9" x2="21" y2="9"/>
                    <line x1="9" y1="21" x2="9" y2="9"/>
                </svg>
                <p>Belum ada slide. Tambahkan slide pertama di atas.</p>
            </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($sliders->hasPages())
        <div class="pagination-wrapper">
            <div class="pagination-container">
                @if($sliders->onFirstPage())
                    <button class="pagination-btn" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        <span class="desktop-only">Previous</span>
                    </button>
                @else
                    <a href="{{ $sliders->previousPageUrl() }}" class="pagination-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        <span class="desktop-only">Previous</span>
                    </a>
                @endif

                @php
                    $currentPage = $sliders->currentPage();
                    $lastPage = $sliders->lastPage();

                    if ($lastPage <= 5) {
                        $pages = range(1, $lastPage);
                    } else {
                        if ($currentPage <= 3) {
                            $pages = [1, 2, 3, 4, '...', $lastPage];
                        } elseif ($currentPage >= $lastPage - 2) {
                            $pages = [1, '...', $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
                        } else {
                            $pages = [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
                        }
                    }
                @endphp

                @foreach($pages as $page)
                    @if($page === '...')
                        <span style="padding: 0.5rem 0.25rem; color: var(--text-muted); font-size: 0.875rem;">...</span>
                    @elseif($page == $currentPage)
                        <button class="pagination-btn active">{{ $page }}</button>
                    @else
                        <a href="{{ $sliders->url($page) }}" class="pagination-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($sliders->hasMorePages())
                    <a href="{{ $sliders->nextPageUrl() }}" class="pagination-btn">
                        <span class="desktop-only">Next</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                @else
                    <button class="pagination-btn" disabled>
                        <span class="desktop-only">Next</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                @endif
            </div>

            <div class="pagination-info">
                Menampilkan <strong>{{ $sliders->firstItem() }}</strong> - <strong>{{ $sliders->lastItem() }}</strong> dari <strong>{{ $totalSliders }}</strong> slide
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="modal-overlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div class="modal" style="border-radius:16px;max-width:500px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,0.2);animation:modalSlideUp 0.3s ease">
        <div class="modal-header" style="padding:1.5rem 1.5rem 1rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border)">
            <h3 class="page-title" style="margin:0;font-size:1.25rem;font-weight:700">Edit Slide</h3>
            <button data-action="close-edit-modal" class="toggle-btn" style="cursor:pointer;padding:0.5rem;border-radius:8px;transition:all 0.2s;display:flex;align-items:center;justify-content:center">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data" style="padding:1.5rem;display:grid;gap:1.25rem">
            @csrf
            @method('PUT')
            <input type="hidden" id="editId" name="id">

            <div>
                <label class="form-label">Gambar (kosongkan jika tidak diubah)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <img id="editImagePreview" src="" style="max-width:100%;max-height:200px;margin-top:0.75rem;border-radius:10px;display:none;object-fit:cover;box-shadow:0 4px 12px rgba(0,0,0,0.1)">
            </div>

            <div>
                <label class="form-label">Durasi Tampil (detik)</label>
                <input type="number" name="display_duration" id="editDuration" class="form-control" min="3" max="30">
            </div>

            <div>
                <label class="checkbox-wrapper">
                    <input class="u-hidden" type="checkbox" name="is_active" id="editActive" value="1">
                    <div class="checkbox-box" id="editActiveBox">
                        <svg id="editActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span class="checkbox-label">Aktif</span>
                </label>
            </div>

            <div class="form-actions" style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:0.5rem;padding-top:1rem;border-top:1px solid var(--border)">
                <button type="button" data-action="close-edit-modal" class="btn btn-cancel">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/admin/js/hero-sliders-index.js') }}"></script>
@endpush
@endsection
{{-- Dikembangkan oleh Institut Teknologi Del --}}
