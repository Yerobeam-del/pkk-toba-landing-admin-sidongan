<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Token SSO dari Admin Panel menuju SIEDA (dan verifikasi token dari SIEDA).
 *
 * Format token:
 *   base64url(JSON{email, exp, return}) . '.' . HMAC-SHA256(payload, SIEDA_SYNC_SECRET)
 *
 * Secret memakai SIEDA_SYNC_SECRET yang SAMA dengan kunci sinkronisasi user
 * (SiedaSyncService) — tidak ada kunci baru yang perlu dipertukarkan.
 * Token berumur pendek (5 menit); sisi /sso/login memaksakan sekali pakai.
 */
class SsoTokenService
{
    private const TTL = 300;

    public function secret(): string
    {
        return (string) config('services.sieda.sync_secret');
    }

    public function isConfigured(): bool
    {
        return $this->secret() !== '';
    }

    /**
     * Terbitkan token SSO untuk sebuah email.
     */
    public function issue(string $email, string $returnPath = ''): ?string
    {
        if (!$this->isConfigured()) {
            Log::warning('SSO: SIEDA_SYNC_SECRET belum diatur, token tidak dibuat.');
            return null;
        }

        $payload = json_encode([
            'email'  => $email,
            'exp'    => time() + self::TTL,
            'return' => $returnPath,
        ]);

        return $this->encode($payload);
    }

    /**
     * Verifikasi token SSO. Mengembalikan payload bila sah, null bila
     * signature salah, sudah kedaluwarsa, atau secret belum diatur.
     */
    public function verify(string $token): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        $parts = explode('.', $token, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$payloadB64, $signature] = $parts;

        $payload = base64_decode(
            strtr($payloadB64, '-_', '+/') . str_repeat('=', (4 - strlen($payloadB64) % 4) % 4),
            true
        );
        if ($payload === false) {
            return null;
        }

        if (!hash_equals(hash_hmac('sha256', $payload, $this->secret()), $signature)) {
            return null;
        }

        $data = json_decode($payload, true);
        if (!is_array($data) || empty($data['email']) || empty($data['exp']) || $data['exp'] < time()) {
            return null;
        }

        return $data;
    }

    /**
     * URL callback SSO menuju SIEDA ("Kembali ke SIEDA"). Bila secret
     * belum diatur, fallback ke halaman login SIEDA.
     */
    public function buildCallbackUrl(?string $email = null): string
    {
        $base = rtrim((string) config('services.sieda.base_url', 'http://127.0.0.1:8004'), '/');
        $email = $email ?? auth()->user()?->email;

        if (!$email) {
            return $base . '/login';
        }

        $token = $this->issue($email);

        return $token
            ? $base . '/sso/callback?token=' . urlencode($token)
            : $base . '/login';
    }

    private function encode(string $payload): string
    {
        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=') . '.' . hash_hmac('sha256', $payload, $this->secret());
    }
}
