@extends('admin.layouts.app')
@section('title', 'Manajemen Hero Slider')
@section('page-title', 'Kelola Slider Beranda')

@section('content')
<style>
@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>

<div style="margin-bottom:2rem">

    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
    <div class="error-alert">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <div style="flex:1">
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
                        <input type="checkbox" name="is_active" id="isActive" value="1" checked style="display:none">
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
            <div class="slider-list-title" style="display:flex;align-items:center;gap:0.75rem">
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
                <form method="GET" action="{{ route('admin.hero-sliders.index') }}" style="display:flex;align-items:center;gap:0.5rem">
                    <label style="font-size:0.85rem;color:var(--text-muted);white-space:nowrap;font-weight:500">Tampilkan:</label>
                    <div style="position:relative">
                        <select name="per_page" onchange="this.form.submit()" style="padding:0.5rem 2.5rem 0.5rem 0.75rem;border:1px solid var(--border);border-radius:8px;font-size:0.9rem;min-width:80px;cursor:pointer;background:white;appearance:none;-webkit-appearance:none;-moz-appearance:none;transition:all 0.2s" onfocus="this.style.borderColor='var(--primary)';this.style.boxShadow='0 0 0 3px rgba(13, 148, 136, 0.1)'" onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                            @for($i = 5; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ $perPage == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </div>
                </form>
            </div>
        </div>

        <div id="slidersList">
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
                    <button onclick="editSlider({{ $slider->id }})" title="Edit" class="btn-edit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button onclick="confirmDeleteWithToast({{ $slider->id }}, 'Slide #{{ $slider->id }}')" title="Hapus" class="btn-del">
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
<div id="editModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center;padding:1rem">
    <div style="background:#fff;border-radius:16px;max-width:500px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 25px 60px rgba(0,0,0,0.2);animation:modalSlideUp 0.3s ease">
        <div style="padding:1.5rem 1.5rem 1rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f1f5f9">
            <h3 style="margin:0;font-size:1.25rem;font-weight:700;color:#1e293b">Edit Slide</h3>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:0.5rem;border-radius:8px;transition:all 0.2s;display:flex;align-items:center;justify-content:center" onmouseover="this.style.background='#f1f5f9';this.style.color='#ef4444'" onmouseout="this.style.background='none';this.style.color='#94a3b8'">
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
                    <input type="checkbox" name="is_active" id="editActive" value="1" style="display:none">
                    <div class="checkbox-box" id="editActiveBox">
                        <svg id="editActiveCheck" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <span class="checkbox-label">Aktif</span>
                </label>
            </div>

            <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:0.5rem;padding-top:1rem;border-top:1px solid #f1f5f9">
                <button type="button" onclick="closeEditModal()" class="btn" style="background:#f1f5f9;color:#475569">Batal</button>
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

<script>
// ==========================================
// DELETE CONFIRMATION
// ==========================================
async function confirmDeleteWithToast(id, name) {
    try {
        if (typeof Toast !== 'undefined' && typeof Toast.confirm === 'function') {
            const confirmed = await Toast.confirm(
                `Slide <strong>"${name}"</strong> akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                {
                    title: 'Hapus Slide?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal',
                    type: 'danger'
                }
            );
            if (confirmed) submitDelete(id);
        } else {
            if (confirm(`Hapus slide "${name}"?`)) submitDelete(id);
        }
    } catch (error) {
        console.error('Error:', error);
        if (confirm(`Hapus slide "${name}"?`)) submitDelete(id);
    }
}

function submitDelete(id) {
    const form = document.getElementById('delete-form-' + id);
    if (form) form.submit();
}

// ==========================================
// MOBILE REORDER FUNCTIONS
// ==========================================
function moveSlideUp(id) {
    const slidersList = document.getElementById('slidersList');
    const items = [...slidersList.querySelectorAll('.slider-item')];
    const currentIndex = items.findIndex(item => item.dataset.id == id);

    if (currentIndex > 0) {
        const currentItem = items[currentIndex];
        const prevItem = items[currentIndex - 1];

        slidersList.insertBefore(currentItem, prevItem);
        updateOrder();
        refreshMobileReorderButtons();
    }
}

function moveSlideDown(id) {
    const slidersList = document.getElementById('slidersList');
    const items = [...slidersList.querySelectorAll('.slider-item')];
    const currentIndex = items.findIndex(item => item.dataset.id == id);

    if (currentIndex < items.length - 1) {
        const currentItem = items[currentIndex];
        const nextItem = items[currentIndex + 1];

        slidersList.insertBefore(nextItem, currentItem);
        updateOrder();
        refreshMobileReorderButtons();
    }
}

function refreshMobileReorderButtons() {
    const slidersList = document.getElementById('slidersList');
    const items = slidersList.querySelectorAll('.slider-item');

    items.forEach((item, index) => {
        const mobileReorder = item.querySelector('.mobile-reorder');
        if (mobileReorder) {
            const buttons = mobileReorder.querySelectorAll('button');
            buttons[0].disabled = index === 0;
            buttons[1].disabled = index === items.length - 1;
        }
    });
}

// ==========================================
// EDIT SLIDER
// ==========================================
function editSlider(id) {
    const item = document.querySelector(`.slider-item[data-id="${id}"]`);
    if (!item) return;

    const form = document.getElementById('editForm');
    form.action = `/admin/hero-sliders/${id}`;

    document.getElementById('editId').value = id;

    const infoText = item.querySelector('.slider-meta').parentElement.textContent;
    document.getElementById('editDuration').value = infoText.match(/(\d+)s/)?.[1] || '5';

    const isActive = infoText.includes('Aktif') && !infoText.includes('Nonaktif');
    document.getElementById('editActive').checked = isActive;
    updateCheckboxStyle('editActiveBox', 'editActiveCheck', isActive);

    const preview = document.getElementById('editImagePreview');
    preview.src = item.querySelector('img').src;
    preview.style.display = 'block';

    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('editImagePreview').style.display = 'none';
}

// ==========================================
// CHECKBOX ANIMATION
// ==========================================
function updateCheckboxStyle(boxId, checkId, isChecked) {
    const box = document.getElementById(boxId);
    const check = document.getElementById(checkId);
    if (!box || !check) return;

    if (isChecked) {
        box.classList.add('checked');
    } else {
        box.classList.remove('checked');
    }
}

// ==========================================
// INITIALIZATION
// ==========================================
document.addEventListener('DOMContentLoaded', function() {
    const isActiveCheckbox = document.getElementById('isActive');
    if (isActiveCheckbox) {
        updateCheckboxStyle('isActiveBox', 'isActiveCheck', isActiveCheckbox.checked);
        isActiveCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('isActiveBox', 'isActiveCheck', this.checked);
        });
    }

    const editActiveCheckbox = document.getElementById('editActive');
    if (editActiveCheckbox) {
        editActiveCheckbox.addEventListener('change', function() {
            updateCheckboxStyle('editActiveBox', 'editActiveCheck', this.checked);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModal();
    });

    addMobileReorderButtons();
    initDragAndDrop();
});

function addMobileReorderButtons() {
    const slidersList = document.getElementById('slidersList');
    const items = slidersList.querySelectorAll('.slider-item');

    items.forEach((item, index) => {
        if (item.querySelector('.mobile-reorder')) return;

        const actionsDiv = item.querySelector('.slider-actions');
        const mobileReorder = document.createElement('div');
        mobileReorder.className = 'mobile-reorder';
        mobileReorder.innerHTML = `
            <button onclick="moveSlideUp(${item.dataset.id})" ${index === 0 ? 'disabled' : ''}>
                Geser ke Atas
            </button>
            <button onclick="moveSlideDown(${item.dataset.id})" ${index === items.length - 1 ? 'disabled' : ''}>
                Geser ke Bawah
            </button>
        `;

        actionsDiv.after(mobileReorder);
    });
}

// ==========================================
// DRAG AND DROP (Desktop Only)
// ==========================================
let draggedItem = null;

function initDragAndDrop() {
    if (window.innerWidth <= 768) return;

    const slidersList = document.getElementById('slidersList');
    if (!slidersList) return;

    const items = slidersList.querySelectorAll('.slider-item');

    items.forEach(item => {
        item.setAttribute('draggable', 'true');

        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            setTimeout(() => {
                this.classList.add('dragging');
            }, 0);
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', this.dataset.id);
        });

        item.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            draggedItem = null;

            document.querySelectorAll('.slider-item').forEach(el => {
                el.classList.remove('drag-over');
            });

            updateOrder();
        });

        item.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';

            if (this === draggedItem) return;

            this.classList.add('drag-over');
        });

        item.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });

        item.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();

            if (this === draggedItem) return;

            this.classList.remove('drag-over');

            const rect = this.getBoundingClientRect();
            const midpoint = rect.top + rect.height / 2;

            if (e.clientY < midpoint) {
                slidersList.insertBefore(draggedItem, this);
            } else {
                if (this.nextSibling) {
                    slidersList.insertBefore(draggedItem, this.nextSibling);
                } else {
                    slidersList.appendChild(draggedItem);
                }
            }
        });
    });

    slidersList.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    });

    slidersList.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();

        if (draggedItem && e.target === this) {
            this.appendChild(draggedItem);
            updateOrder();
        }
    });
}

async function updateOrder() {
    const order = [...document.querySelectorAll('.slider-item')].map(el => el.dataset.id);

    try {
        const response = await fetch('{{ route('admin.hero-sliders.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order })
        });

        if (response.ok) {
            console.log('✅ Order updated successfully');
        } else {
            console.error('❌ Failed to update order');
        }
    } catch (error) {
        console.error('❌ Error updating order:', error);
    }
}
</script>
@endsection
