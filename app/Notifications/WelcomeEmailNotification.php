<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeEmailNotification extends Notification
{
    use Queueable;

    protected $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('¡Bienvenido a Oh! Sansi!')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tu cuenta ha sido creada exitosamente.')
            ->line('Tus credenciales de acceso son:')
            ->line('Email: ' . $notifiable->email)
            ->line('Contraseña: ' . $this->password)
            ->action('Iniciar Sesión', url('/login'))
            ->line('Te recomendamos cambiar tu contraseña después de iniciar sesión.')
            ->line('¡Gracias por ser parte de nuestra competición!');
    }
}