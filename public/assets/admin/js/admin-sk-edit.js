/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * JS halaman: resources/views/admin/sk/edit.blade.php
 * (diekstrak dari blok <script> di dalam view)
 *
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */


// Preview new file name & size
const fileInput = document.getElementById('fileInput');
const currentFileInfo = document.getElementById('currentFileInfo');
const newFilePreview = document.getElementById('newFilePreview');
const fileName = document.getElementById('fileName');
const fileSize = document.getElementById('fileSize');

fileInput?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            Toast.warning('Ukuran file terlalu besar. Maksimal 10MB.');
            e.target.value = '';
            return;
        }
        
        fileName.textContent = file.name;
        
        // Format file size
        const units = ['B', 'KB', 'MB', 'GB'];
        let size = file.size;
        let unitIndex = 0;
        while (size >= 1024 && unitIndex < units.length - 1) {
            size /= 1024;
            unitIndex++;
        }
        fileSize.textContent = size.toFixed(2) + ' ' + units[unitIndex];
        
        // Hide current file, show new preview
        currentFileInfo.style.display = 'none';
        newFilePreview.style.display = 'flex';
    }
});

// Clear file selection and restore current file preview
function clearFile() {
    fileInput.value = '';
    newFilePreview.style.display = 'none';
    currentFileInfo.style.display = 'flex';
    fileName.textContent = '';
    fileSize.textContent = '';
}



// ============================================================
// Dikembangkan oleh Institut Teknologi Del
// DELEGATION (menggantikan onclick inline)
// ============================================================
document.addEventListener('click', function (event) {
    if (event.target.closest('[data-action="clear-file"]')) {
        clearFile();
    }
});
