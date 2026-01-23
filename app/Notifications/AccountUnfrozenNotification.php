<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountUnfrozenNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $fromAddress = config('mail.from.address') ?: env('MAIL_FROM_ADDRESS', 'no-reply@localhost');
        $fromName = config('mail.from.name') ?: env('MAIL_FROM_NAME', config('app.name'));
        return (new MailMessage)
            ->from($fromAddress, $fromName)
            ->subject('Your account has been reactivated')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your account has been unfrozen and reactivated.')
            ->line('You can now sign in and continue using the system.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'account_unfrozen'
        ];
    }
}
