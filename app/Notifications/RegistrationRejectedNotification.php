<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $userName,
        private readonly string $reason
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Registration Request Update')
            ->greeting("Hello {$this->userName},")
            ->line('We have reviewed your registration request.')
            ->line('Unfortunately, we are unable to approve your registration at this time.')
            ->line("**Reason:** {$this->reason}")
            ->line('If you believe this is an error or would like to address the issues mentioned, please feel free to submit a new registration request.')
            ->line('Thank you for your understanding.');
    }
}
