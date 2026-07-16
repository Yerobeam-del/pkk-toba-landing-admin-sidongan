@echo off
title PKK Toba - Start Script
color 0A

echo ============================================
echo   PKK Kabupaten Toba - Start Script
echo ============================================
echo.

REM Cek apakah ngrok ada
where ngrok >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    color 0C
    echo [ERROR] ngrok tidak ditemukan!
    echo.
    echo Download dari: https://ngrok.com/download
    echo Atau install via: scoop install ngrok
    echo.
    pause
    exit /b 1
)

echo [1/3] Membersihkan cache Laravel...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
php artisan view:clear >nul 2>&1
echo       [OK] Cache dibersihkan
echo.

echo [2/3] Starting Laravel Server...
start "Laravel Server" cmd /k "php artisan serve"
timeout /t 3 /nobreak >nul
echo       [OK] Laravel running at http://127.0.0.1:8000
echo.

echo [3/3] Starting Ngrok Tunnel...
start "Ngrok Tunnel" cmd /k "ngrok http 8000"
timeout /t 3 /nobreak >nul
echo       [OK] Ngrok started
echo.

echo ============================================
echo   SETUP SELESAI!
echo ============================================
echo.
echo   Langkah selanjutnya:
echo.
echo   1. Buka http://127.0.0.1:4040 di browser
echo      untuk melihat URL ngrok Anda
echo.
echo   2. Copy URL ngrok (contoh: https://xxx.ngrok-free.app)
echo.
echo   3. Update APP_URL di file .env dengan URL ngrok
echo.
echo   4. Jalankan: php artisan config:clear
echo.
echo   5. Buka URL ngrok di browser untuk test preview
echo.
echo ============================================
echo.
pause