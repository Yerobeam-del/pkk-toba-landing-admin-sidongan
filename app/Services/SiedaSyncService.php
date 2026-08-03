<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk sinkronisasi user antara pkk-toba (Admin Panel) dan aplikasi SIEDA.
 *
 * Backend SIEDA kini melindungi endpoint /api/sieda/* dengan middleware
 * VerifySiedaSyncKey yang mewajibkan header:
 *   - X-Sieda-Key       : shared secret (SIEDA_SYNC_SECRET, wajib)
 *   - X-Sieda-Timestamp : unix timestamp saat request (untuk HMAC)
 *   - X-Sieda-Signature : hash_hmac('sha256', "{timestamp}.{body_json}", secret)
 *
 * Pastikan SIEDA_SYNC_SECRET di .env pkk-toba SAMA dengan di .env SIEDA.
 */
class SiedaSyncService
{
    protected string $baseUrl;
    protected ?string $secret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sieda.base_url', 'http://127.0.0.1:8004'), '/');
        $this->secret = config('services.sieda.sync_secret');
    }

    /**
     * Konfirmasi apakah konfigurasi sync sudah lengkap.
     */
    public function isConfigured(): bool
    {
        return !empty($this->secret);
    }

    /**
     * Sinkronisasi data user ke SIEDA.
     *
     * @param array $payload ['name','email','password','sieda_role','kecamatan_code','kelurahan_code']
     * @param string|null $emailForUpdate Jika update, isi email lama (untuk URL path /sync-user/{email})
     * @return bool True jika sukses
     */
    public function syncUser(array $payload, ?string $emailForUpdate = null): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('[SiedaSync] SIEDA_SYNC_SECRET belum dikonfigurasi. Sinkronisasi dilewati.');
            return false;
        }

        try {
            $url = $this->baseUrl . '/api/sieda/sync-user';
            $method = 'POST';

            if ($emailForUpdate) {
                // Update: PUT ke /api/sieda/sync-user/{email}
                $url .= '/' . urlencode($emailForUpdate);
                $method = 'PUT';
            }

            // Bangun body JSON mentah agar signature cocok dengan yang diterima SIEDA
            $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $timestamp = (string) time();
            $signature = hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

            $response = Http::timeout(10)
                ->retry(2, 200)
                ->withBody($body, 'application/json')
                ->withHeaders([
                    'X-Sieda-Key' => $this->secret,
                    'X-Sieda-Timestamp' => $timestamp,
                    'X-Sieda-Signature' => $signature,
                    'Accept' => 'application/json',
                ])
                ->send($method, $url);

            if ($response->successful()) {
                Log::info('[SiedaSync] User berhasil disinkronisasi', [
                    'email' => $payload['email'],
                    'role' => $payload['sieda_role'],
                ]);
                return true;
            }

            // Log detail error dari SIEDA (4xx/5xx)
            Log::error('[SiedaSync] Gagal — SIEDA menolak request', [
                'email' => $payload['email'],
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('[SiedaSync] Exception: ' . $e->getMessage(), [
                'email' => $payload['email'],
            ]);
            return false;
        }
    }

    /**
     * Cabut akses SIEDA dari user (saat user dihapus atau dinonaktifkan).
     *
     * @param string $email
     * @return bool
     */
    public function revokeAccess(string $email): bool
    {
        if (!$this->isConfigured()) {
            Log::warning('[SiedaSync] SIEDA_SYNC_SECRET belum dikonfigurasi. Revoke dilewati.');
            return false;
        }

        try {
            $url = $this->baseUrl . '/api/sieda/revoke-access';
            $payload = ['email' => $email];

            $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $timestamp = (string) time();
            $signature = hash_hmac('sha256', $timestamp . '.' . $body, $this->secret);

            $response = Http::timeout(10)
                ->retry(2, 200)
                ->withBody($body, 'application/json')
                ->withHeaders([
                    'X-Sieda-Key' => $this->secret,
                    'X-Sieda-Timestamp' => $timestamp,
                    'X-Sieda-Signature' => $signature,
                    'Accept' => 'application/json',
                ])
                ->post($url);

            if ($response->successful()) {
                Log::info('[SiedaSync] Akses SIEDA dicabut', ['email' => $email]);
                return true;
            }

            Log::error('[SiedaSync] Gagal revoke — SIEDA menolak', [
                'email' => $email,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('[SiedaSync] Revoke Exception: ' . $e->getMessage(), [
                'email' => $email,
            ]);
            return false;
        }
    }
}
