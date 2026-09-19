<?php

/* ============================================================
 * Dikembangkan oleh Institut Teknologi Del
 * ============================================================
 * Email pemberitahuan kepada Ketua PKK bahwa ada Surat Keluar
 * yang menunggu persetujuan. In-app notification sudah dibuat
 * di controller; email ini menambah saluran agar Ketua tidak
 * harus login untuk mengetahui adanya surat menunggu.
 *
 * Tujuan email mengikuti routeNotificationForMail di User:
 * personal_email yang sudah terverifikasi, fallback ke email login.
 */

namespace App\Notifications;

use App\Models\OutgoingLetter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutgoingLetterSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public OutgoingLetter $letter)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('sidongan.outgoing.show', $this->letter);

        return (new MailMessage)
            ->subject('Surat Keluar Menunggu Persetujuan - PKK Kabupaten Toba')
            ->greeting('Halo ' . $notifiable->name . '!')
            ->line('Sekretaris telah mengajukan Surat Keluar yang menunggu persetujuan Anda.')
            ->line('**Perihal:** ' . $this->letter->subject)
            ->line('**Penerima:** ' . $this->letter->recipient)
            ->line('**Tanggal Surat:** ' . optional($this->letter->letter_date)->locale('id')->translatedFormat('d F Y'))
            ->line('Nomor surat akan diterbitkan otomatis setelah Anda menyetujui.')
            ->action('Buka Surat Keluar', $url)
            ->line('Anda menerima email ini karena berperan sebagai Ketua PKK di SIDONGAN.');
    }
}
