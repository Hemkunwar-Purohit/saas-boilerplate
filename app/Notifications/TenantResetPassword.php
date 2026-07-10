<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class TenantResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $tenantName   = tenant()?->name ?? config('app.name');
        $tenantDomain = tenant()?->domains()->first()?->domain;

        $scheme = config('app.env') === 'production' ? 'https' : 'http';
        $port   = config('app.env') === 'local'
            ? ':' . parse_url(config('app.url'), PHP_URL_PORT)
            : '';

        $resetUrl = $tenantDomain
            ? "{$scheme}://{$tenantDomain}{$port}/reset-password/{$this->token}?email={$notifiable->email}"
            : url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->email], false));

        return (new MailMessage)
            ->subject("Reset your password — {$tenantName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line('You are receiving this email because we received a password reset request.')
            ->action('Reset Password', $resetUrl)
            ->line('This link expires in ' . config('auth.passwords.users.expire') . ' minutes.')
            ->line('If you did not request a password reset, no action is required.');
    }
}
