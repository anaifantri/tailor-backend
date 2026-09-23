<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

// Hapus 'implements ShouldQueue' jika ingin email terkirim langsung tanpa worker
class CustomVerifyEmailNotification extends Notification 
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // 1. Buat signed URL Backend menggunakan property 'ulid' langsung
        $backendUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'ulid' => $notifiable->ulid, // <-- Diubah dari $notifiable->getKey() ke $notifiable->ulid
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // 2. Ambil query string (expires & signature) dari backendUrl
        $queryString = parse_url($backendUrl, PHP_URL_QUERY);

        // 3. Ambil URL Frontend dari file .env (dengan fallback localhost)
        $frontendBaseUrl = config('app.frontend_url', 'http://localhost:5173');

        // 4. Susun Frontend URL yang rapi
        $frontendUrl = sprintf(
            '%s/verify-email/%s/%s?%s',
            rtrim($frontendBaseUrl, '/'),
            $notifiable->ulid, // <-- Diubah ke $notifiable->ulid
            sha1($notifiable->getEmailForVerification()),
            $queryString
        );
        // $frontendUrl = str_replace(url('/api'), 'http://localhost:5173/verify-email/', 'http://localhost:5173/verify-email/'.$notifiable->ulid . '/' . sha1($notifiable->getEmailForVerification()) . '?' . $queryString);

        return (new MailMessage)
            ->subject('Verifikasi Alamat Email Anda')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun.')
            ->action('Verifikasi Email', $frontendUrl)
            ->line('Jika Anda tidak membuat akun di aplikasi kami, abaikan email ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}