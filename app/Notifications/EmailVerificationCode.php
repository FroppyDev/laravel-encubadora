<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationCode extends Notification
{
    public function __construct(public string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu código de verificación · Incubadora FIC')
            ->greeting('Hola, '.$notifiable->name)
            ->line('Ingresa este código en la página de verificación de Incubadora FIC:')
            ->line($this->code)
            ->line('El código vence en 10 minutos. No lo compartas con nadie.')
            ->line('Si no solicitaste este código, puedes ignorar este correo.');
    }
}
