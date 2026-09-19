<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email gabungan verifikasi email pribadi untuk onboarding Admin Panel:
 * kode OTP 6 digit (diverifikasi langsung di halaman onboarding) + link
 * verifikasi alternatif (fallback, berlaku 24 jam) — dalam SATU email
 * supaya user tidak menerima dua pesan terpisah.
 */
class PersonalEmailOtpNotification extends Notification
{
    use Queueable;

    public string $email;
    public string $code;
    public int $expiresMinutes;

    /**
     * Link verifikasi alternatif (signed URL, fallback bila user lebih suka
     * verifikasi lewat klik di email — misal buka di ponsel lain).
     */
    public string $verifyUrl;

    public function __construct(string $email, string $code, string $verifyUrl, int $expiresMinutes = 15)
    {
        $this->email = $email;
        $this->code = $code;
        $this->verifyUrl = $verifyUrl;
        $this->expiresMinutes = $expiresMinutes;
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Pribadi - PKK Kabupaten Toba')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftarkan email pribadi Anda untuk akun PKK Kabupaten Toba.')
            ->line('Gunakan kode berikut untuk memverifikasi email di halaman onboarding:')
            ->line('**' . implode(' ', str_split($this->code)) . '**')
            ->line('Kode berlaku **' . $this->expiresMinutes . ' menit** dan hanya bisa dipakai sekali.')
            ->line('Atau verifikasi lewat tombol di bawah — link ini berlaku **24 jam**:')
            ->action('Verifikasi lewat Link', $this->verifyUrl)
            ->line('Dengan memverifikasi email ini, Anda akan bisa menggunakan fitur **Lupa Password** ')
            ->line('yang akan mengirim link reset ke email pribadi Anda.')
            ->line('Jika Anda tidak mendaftarkan email ini, abaikan pesan ini.')
            ->salutation('Salam, ' . PHP_EOL . 'Tim PKK Kabupaten Toba')
            ->level('primary');
    }

    public function toArray(object $notifiable): array
    {
        return ['type' => 'personal_email_otp'];
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
