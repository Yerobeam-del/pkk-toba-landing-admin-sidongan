<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class PersonalEmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        // Signed URL valid 24 jam — hanya bisa dipakai oleh user yang terautentikasi
        $verificationUrl = URL::signedRoute('personal-email.verify', [
            'id' => $notifiable->id,
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

        // Email dikirim ke alamat yang ditentukan oleh User::routeNotificationForMail()
        // yang akan mengarahkan ke personal_email (bukan login email @pkk-toba.id)
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
