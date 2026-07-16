<?php
error_reporting(0);
ini_set('display_errors', 0);

// Ambil path setelah /storage/
$uri = $_SERVER['REQUEST_URI'];
$file_path = str_replace('/storage/', '', $uri);

// Path ke file fisik
$base_path = '/home/vol3_5/infinityfree.com/if0_42341803/htdocs/storage/app/public/';
$real_file = $base_path . $file_path;

// Cek file
if (file_exists($real_file) && is_file($real_file)) {
    $mime = mime_content_type($real_file);
    header("Content-Type: " . $mime);
    header("Content-Length: " . filesize($real_file));
    readfile($real_file);
    exit;
}

// Debug output (hapus setelah berhasil)
header("HTTP/1.0 404 Not Found");
echo "File not found: " . $file_path . "\n";
echo "Looking in: " . $real_file . "\n";
echo "File exists: " . (file_exists($real_file) ? 'YES' : 'NO');