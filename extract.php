<?php
ini_set('memory_limit', '512M');
set_time_limit(300);

$zipPath = __DIR__ . '/vendor.zip';

if (!file_exists($zipPath)) {
    die('File vendor.zip tidak ditemukan di folder ini.');
}

$zip = new ZipArchive;
if ($zip->open($zipPath) === TRUE) {
    $zip->extractTo(__DIR__);
    $zip->close();
    echo 'Extraction complete. Please delete this file and vendor.zip immediately.';
} else {
    echo 'Failed to open zip file. Error: ' . $zip->getStatusString();
}
?>