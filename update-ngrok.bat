@echo off
setlocal enabledelayedexpansion

echo ============================================
echo   Auto Update APP_URL dengan Ngrok
echo ============================================
echo.

REM Ambil URL ngrok dari API
for /f "delims=" %%a in ('curl -s http://127.0.0.1:4040/api/tunnels ^| findstr "public_url"') do (
    set "line=%%a"
)

REM Extract URL
for /f "tokens=2 delims=:, " %%a in ("%line%") do (
    set "NGROK_URL=%%a"
)

REM Hapus tanda kutip
set "NGROK_URL=%NGROK_URL:"=%"

if "%NGROK_URL%"=="" (
    echo [ERROR] Tidak bisa mendapatkan URL ngrok!
    echo Pastikan ngrok sudah berjalan.
    pause
    exit /b 1
)

echo URL Ngrok: %NGROK_URL%
echo.

REM Update .env
powershell -Command "(Get-Content .env) -replace 'APP_URL=.*', 'APP_URL=%NGROK_URL%' | Set-Content .env"

echo [OK] APP_URL sudah diupdate!
echo.

REM Clear cache
php artisan config:clear >nul 2>&1
echo [OK] Cache sudah dibersihkan!
echo.

echo ============================================
echo   Buka URL ini di browser:
echo   %NGROK_URL%/#template
echo ============================================
echo.

start %NGROK_URL%/#template

pause