/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/sidongan/disposisi/form.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


function toggleRoleStyle(checkbox) {
    const label = checkbox.closest('.role-option');
    if (checkbox.checked) label.classList.add('active');
    else label.classList.remove('active');
}

function toggleCustomAction() {
    const actionSelect = document.getElementById('action');
    const customContainer = document.getElementById('customActionContainer');
    const customInput = document.getElementById('customActionInput');
    
    if (actionSelect.value === 'Lainnya') {
        customContainer.style.display = 'block';
        customInput.required = true;
        setTimeout(() => customInput.focus(), 100);
    } else {
        customContainer.style.display = 'none';
        customInput.required = false;
        customInput.value = '';
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    if (document.querySelectorAll('input[name="target_roles[]"]:checked').length === 0) {
        e.preventDefault();
        Toast.warning('Pilih minimal satu tujuan disposisi!');
        return false;
    }
    
    const actionSelect = document.getElementById('action');
    const customInput = document.getElementById('customActionInput');
    
    if (actionSelect.value === 'Lainnya' && !customInput.value.trim()) {
        e.preventDefault();
        Toast.warning('Silakan masukkan tindakan/instruksi lainnya!');
        customInput.focus();
        return false;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    toggleCustomAction();
});



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// CHANGE DELEGATION (menggantikan onchange inline)
// ============================================================
document.addEventListener('change', function (event) {
    const target = event.target;
    if (!target) return;
    if (target.matches && target.matches('input[name="target_roles[]"]')) {
        toggleRoleStyle(target);
    }
    if (target.id === 'action') {
        toggleCustomAction();
    }
});
