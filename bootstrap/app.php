<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 🟢 TAMBAHAN: Mempercayai semua proxy luar (Localtunnel / VS Code Ports)
        // Ini wajib agar Laravel membaca header X-Forwarded-Proto untuk HTTPS
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'can.access' => \App\Http\Middleware\CanAccessApplication::class,
            'sidongan.auth' => \App\Http\Middleware\SidonganAuthenticate::class,
            'sidongan.guest' => \App\Http\Middleware\RedirectIfAuthenticatedSidongan::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle 419 Page Expired (CSRF Token Mismatch)
        $exceptions->renderable(function (TokenMismatchException $e, $request) {
            $path = $request->path();
            
            // Deteksi SIDONGAN berdasarkan path
            $isSidongan = str_starts_with($path, 'sidongan') 
                || str_starts_with($path, 'sidongan-login');
            
            // Jika path mengandung 'admin', bukan SIDONGAN
            if (str_starts_with($path, 'admin')) {
                $isSidongan = false;
            }
            
            if ($isSidongan) {
                // Clear session dan redirect ke login SIDONGAN
                if (session()->isStarted()) {
                    session()->flush();
                    session()->regenerateToken();
                }
                
                return redirect()
                    ->route('sidongan.login')
                    ->with('error', 'Sesi login Anda telah berakhir. Silakan login kembali.');
            }
            
            // Untuk Admin Panel dan lainnya
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'CSRF token expired. Silakan refresh halaman.',
                    'status' => 419
                ], 419);
            }
            
            return response()->view('errors.419', [], 419);
        });
    })->create();
