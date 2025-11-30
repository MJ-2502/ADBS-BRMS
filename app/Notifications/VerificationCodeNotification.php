<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $code, private readonly string $channelLabel)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your verification code')
            ->greeting('Hello!')
            ->line("Use the verification code below to confirm your {$this->channelLabel}.")
            ->line("Verification code: **{$this->code}**")
            ->line('This code will expire in 10 minutes. If you did not request this, you can ignore this message.');
    }
}
