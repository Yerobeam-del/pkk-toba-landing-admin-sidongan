/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/tentang/index.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


function addProgram() {
    const container = document.getElementById('programsContainer');
    const div = document.createElement('div');
    div.className = 'program-item';
    div.style.cssText = 'display:flex;gap:0.75rem;align-items:center';
    div.innerHTML = `
        <input type="text" name="programs[]" class="form-control u-flex-1" placeholder="Nama program" required>
        <button type="button" onclick="removeProgram(this)" 
                title="Hapus program"
                style="width:36px;height:36px;display:inline-flex;align-items:center;justify-content:center;background:transparent;color:#94a3b8;border:none;border-radius:6px;cursor:pointer;transition:all 0.2s"
                onmouseover="this.style.background='#fef2f2';this.style.color='#ef4444';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.background='transparent';this.style.color='#94a3b8';this.style.transform='translateY(0)'">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
        </button>
    `;
    container.appendChild(div);
    
    // Focus ke input baru
    const newInput = div.querySelector('input');
    if (newInput) newInput.focus();
}

function removeProgram(btn) {
    const container = document.getElementById('programsContainer');
    const items = container.querySelectorAll('.program-item');
    
    if (items.length <= 1) {
        if (typeof Toast !== 'undefined') {
            Toast.warning('Minimal harus ada 1 program');
        } else {
            Toast.warning('Minimal harus ada 1 program');
        }
        return;
    }
    
    btn.parentElement.remove();
}



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
document.addEventListener('click', function (event) {
    const rmBtn = event.target.closest('[data-action="remove-program"]');
    if (rmBtn) {
        removeProgram(rmBtn);
        return;
    }
    if (event.target.closest('[data-action="add-program"]')) {
        addProgram();
    }
});
