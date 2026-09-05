<?php



/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================ */
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class PersonalEmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Alamat email tujuan — diisi oleh controller, dibaca oleh User::routeNotificationForMail().
     */
    public string $email;

    /**
     * Nama route signed URL verifikasi.
     *
     * Default = route Admin Panel (personal-email.verify). Saat email pribadi
     * didaftarkan dari SIDONGAN, pakai route milik SIDONGAN
     * (sidongan.personal-email.verify) supaya link verifikasi menunjuk ke host
     * SIDONGAN dan tidak menabrak guard/domain aplikasi lain.
     */
    public string $verifyRoute;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $email, string $verifyRoute = 'personal-email.verify')
    {
        $this->email = $email;
        $this->verifyRoute = $verifyRoute;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Signed URL valid 24 jam — sertakan email agar diverifikasi dulu baru disimpan
        $verificationUrl = URL::signedRoute($this->verifyRoute, [
            'id' => $notifiable->id,
            'email' => $this->email,
        ], now()->addHours(24));

        $mail = (new MailMessage)
            ->subject('Verifikasi Email Pribadi - PKK Kabupaten Toba')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Terima kasih telah mendaftarkan email pribadi Anda untuk akun PKK Kabupaten Toba.')
            ->line('Silakan klik tombol di bawah untuk memverifikasi alamat email ini:')
            ->action('Verifikasi Email', $verificationUrl)
            ->line('Dengan memverifikasi email ini, Anda akan bisa menggunakan fitur **Lupa Password** ')
            ->line('yang akan mengirim link reset ke email pribadi Anda.')
            ->line('Link verifikasi ini akan kadaluarsa dalam **24 jam**.')
            ->line('Jika Anda tidak mendaftarkan email ini, abaikan pesan ini.')
            ->salutation('Salam, ' . PHP_EOL . 'Tim PKK Kabupaten Toba')
            ->level('primary');

        // Email dikirim ke alamat dari $this->email (diatur oleh controller),
        // dibaca oleh User::routeNotificationForMail() untuk menentukan tujuan.
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'personal_email_verification',
        ];
    }
}
/* Dikembangkan oleh Institut Teknologi Del */
