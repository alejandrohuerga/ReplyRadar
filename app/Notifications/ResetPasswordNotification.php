<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $locale = app()->getLocale();

        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $subject = $locale === 'es'
            ? 'Restablece tu contraseña — ReplyRadar'
            : 'Reset your password — ReplyRadar';

        $greeting = $locale === 'es'
            ? '¿Olvidaste tu contraseña?'
            : 'Forgot your password?';

        $intro = $locale === 'es'
            ? 'Recibimos una solicitud para restablecer la contraseña de tu cuenta de ReplyRadar.'
            : 'We received a request to reset the password for your ReplyRadar account.';

        $button = $locale === 'es'
            ? 'Restablecer contraseña'
            : 'Reset Password';

        $expiry = $locale === 'es'
            ? 'Este enlace expirará en :count minutos.'
            : 'This password reset link will expire in :count minutes.';

        $outro = $locale === 'es'
            ? 'Si no solicitaste este cambio, puedes ignorar este mensaje.'
            : 'If you did not request a password reset, no further action is required.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($intro)
            ->action($button, $resetUrl)
            ->line($expiry, ['count' => config('auth.passwords.users.expire')])
            ->line($outro);
    }
}
