<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Guard yang digunakan (web atau sidongan).
     */
    protected string $guard;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token, string $guard = 'web')
    {
        $this->token = $token;
        $this->guard = $guard;
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
        // Tentukan URL reset berdasarkan guard
        if ($this->guard === 'sidongan') {
            $url = route('sidongan.password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
            $appName = 'SIDONGAN';
            $actionText = 'Reset Password SIDONGAN';
        } else {
            $url = route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
            $appName = 'Admin Panel';
            $actionText = 'Reset Password';
        }

        $mail = (new MailMessage)
            ->subject('Reset Password - ' . $appName . ' PKK Kabupaten Toba')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun ' . $appName . ' Anda.')
            ->line('Klik tombol di bawah untuk mereset password Anda:')
            ->action($actionText, $url)
            ->line('Link reset password ini akan kadaluarsa dalam ' . config('auth.passwords.users.expire', 60) . ' menit.')
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->salutation('Salam, ' . PHP_EOL . 'Tim PKK Kabupaten Toba')
            ->level('primary');

        // Email dikirim ke alamat yang ditentukan oleh User::routeNotificationForMail()
        // yang akan mengarahkan ke personal_email jika sudah diverifikasi
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
            'token' => $this->token,
            'guard' => $this->guard,
        ];
    }
}
