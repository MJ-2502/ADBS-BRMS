<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $userName,
        private readonly ?string $notes = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Registration Approved - Welcome to BRMS')
            ->greeting("Hello {$this->userName}!")
            ->line('Great news! Your registration request has been approved.')
            ->line('You can now sign in to your account and access all resident services.');

        if ($this->notes) {
            $message->line("**Note from admin:** {$this->notes}");
        }

        $message->action('Sign In Now', route('login'))
            ->line('Thank you for registering with us!');

        return $message;
    }
}
