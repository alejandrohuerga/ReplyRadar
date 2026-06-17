<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $locale = app()->getLocale();

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $subject = $locale === 'es'
            ? 'Verifica tu correo electrónico — ReplyRadar'
            : 'Verify your email address — ReplyRadar';

        $greeting = $locale === 'es'
            ? '¡Gracias por registrarte!'
            : 'Thanks for signing up!';

        $intro = $locale === 'es'
            ? 'Solo falta un paso para empezar a detectar oportunidades en Reddit.'
            : 'One more step to start detecting opportunities on Reddit.';

        $button = $locale === 'es'
            ? 'Verificar correo electrónico'
            : 'Verify Email Address';

        $outro = $locale === 'es'
            ? 'Si no creaste esta cuenta, puedes ignorar este mensaje.'
            : 'If you did not create this account, no further action is required.';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($intro)
            ->action($button, $verifyUrl)
            ->line($outro);
    }
}
