<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $minutes = config(
            'auth.passwords.' .
                config('auth.defaults.passwords') .
                '.expire'
        );

        return (new MailMessage)
            ->subject('Restablece tu contraseña | Vantrovia')
            ->greeting('Hola, ' . $notifiable->name)
            ->line(
                'Recibimos una solicitud para establecer o restablecer la contraseña de tu cuenta en Vantrovia.'
            )
            ->action(
                'Establecer contraseña',
                $url
            )
            ->line(
                "Este enlace expirará en {$minutes} minutos."
            )
            ->line(
                'Si no solicitaste este cambio, puedes ignorar este correo.'
            )
            ->salutation('Equipo Vantrovia');
    }
}
