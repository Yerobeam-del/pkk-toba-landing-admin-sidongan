@echo off
title PKK Toba - Start Script
color 0A

echo ============================================
echo   PKK Kabupaten Toba - Start Script
echo ============================================
echo.

echo [1/3] Cek MySQL...
powershell -NoProfile -Command "if (Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -InformationLevel Quiet) { exit 0 } else { exit 1 }" >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    color 0E
    echo       [!] MySQL belum jalan di port 3306.
    echo           Buka Laragon lalu klik "Start All", kemudian jalankan ulang script ini.
    echo.
    pause
    exit /b 1
)
echo       [OK] MySQL siap
echo.

echo [2/3] Membersihkan cache Laravel...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1
php artisan route:clear >nul 2>&1
echo       [OK] Cache dibersihkan
echo.

echo [3/3] Starting Laravel Server...
start "Laravel Server" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 3 /nobreak >nul
echo       [OK] Server jalan di port 8000
echo.

echo ============================================
echo   SIAP DIGUNAKAN!
echo ============================================
echo.
echo   Landing page : http://pkktoba.localhost:8000
echo   Admin panel  : http://pkktoba.localhost:8000/admin
echo   SIDONGAN     : http://sidongan.pkktoba.localhost:8000
echo.
echo   Catatan: domain lokal diatur lewat LANDING_DOMAIN dan
echo   SIDONGAN_DOMAIN di file .env. Browser otomatis mengarahkan
echo   *.localhost ke 127.0.0.1, jadi file hosts tidak perlu diubah.
echo.
echo ============================================
echo.

start http://pkktoba.localhost:8000

pause
