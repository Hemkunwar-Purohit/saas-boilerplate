<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

/**
 * Tenant-aware email verification
 * Default Laravel VerifyEmail central URL bhejta hai.
 * Yeh tenant ke subdomain ka URL bhejta hai.
 */
class TenantVerifyEmail extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        // Tenant domain se URL banao
        $tenantDomain = tenant()?->domains()->first()?->domain;

        if (!$tenantDomain) {
            return parent::verificationUrl($notifiable);
        }

        $scheme = config('app.env') === 'production' ? 'https' : 'http';
        $port   = config('app.env') === 'local' ? ':' . parse_url(config('app.url'), PHP_URL_PORT) : '';

        // Signed URL banao tenant domain ke saath
        $temporarySignedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        // Central URL ko tenant URL se replace karo
        $centralUrl = config('app.url');
        $tenantUrl  = "{$scheme}://{$tenantDomain}{$port}";

        return str_replace($centralUrl, $tenantUrl, $temporarySignedUrl);
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $tenantName      = tenant()?->name ?? config('app.name');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject("Verify your email — {$tenantName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Welcome to {$tenantName}! Please verify your email address.")
            ->action('Verify Email Address', $verificationUrl)
            ->line('This link expires in 60 minutes.')
            ->line('If you did not create an account, no further action is required.');
    }
}
