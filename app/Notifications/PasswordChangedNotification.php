<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
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
    public function toMail($notifiable): MailMessage
    {
		$loginUrl = config('app.frontend_url') . '/login';
		
        return (new MailMessage)
            ->subject('Password Anda Telah Diubah')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami ingin menginformasikan bahwa password akun Anda baru saja berhasil diubah.')
            ->line('Jika Anda merasa tidak melakukan perubahan ini, segera hubungi tim dukungan kami untuk mengamankan akun Anda.')
            ->action('Masuk ke Akun', $loginUrl)
            ->line('Terima kasih telah menggunakan aplikasi kami!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
