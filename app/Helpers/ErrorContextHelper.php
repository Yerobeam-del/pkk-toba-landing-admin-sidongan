<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ErrorContextHelper
{
    /**
     * Deteksi apakah error terjadi di konteks SIDONGAN
     */
    public static function isSidonganContext()
    {
        // 1. Cek URL path
        $path = request()->path();
        if (str_starts_with($path, 'sidongan')) {
            return true;
        }
        
        // 2. Cek URL referer
        $referer = request()->header('referer');
        if ($referer && str_contains($referer, '/sidongan')) {
            return true;
        }
        
        // 3. Cek session guard sidongan
        if (Auth::guard('sidongan')->check()) {
            return true;
        }
        
        // 4. Cek session data
        if (Session::has('sidongan_context')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get dashboard route berdasarkan konteks
     */
    public static function getDashboardRoute()
    {
        if (self::isSidonganContext()) {
            return route('sidongan.dashboard');
        }
        
        return route('admin.dashboard');
    }
    
    /**
     * Get login route berdasarkan konteks
     */
    public static function getLoginRoute()
    {
        if (self::isSidonganContext()) {
            return route('sidongan.login');
        }
        
        return route('login');
    }
    
    /**
     * Get nama sistem berdasarkan konteks
     */
    public static function getSystemName()
    {
        if (self::isSidonganContext()) {
            return 'SIDONGAN';
        }
        
        return 'Admin Panel';
    }
    
    /**
     * Get warna tema berdasarkan konteks
     */
    public static function getThemeColors()
    {
        if (self::isSidonganContext()) {
            // Tema SIDONGAN - Cyan/Teal
            return [
                'primary' => '#0891b2',
                'secondary' => '#14b8a6',
                'gradient_from' => '#0891b2',
                'gradient_to' => '#14b8a6',
                'bg_light' => '#ecfeff',
                'bg_medium' => '#cffafe',
                'text_dark' => '#164e63',
                'shadow' => 'rgba(8, 145, 178, 0.15)',
            ];
        }
        
        // Tema Admin - Oranye/Amber
        return [
            'primary' => '#f59e0b',
            'secondary' => '#d97706',
            'gradient_from' => '#f59e0b',
            'gradient_to' => '#d97706',
            'bg_light' => '#fffbeb',
            'bg_medium' => '#fef3c7',
            'text_dark' => '#b45309',
            'shadow' => 'rgba(217, 119, 6, 0.15)',
        ];
    }
}