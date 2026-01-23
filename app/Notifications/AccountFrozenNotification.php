<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountFrozenNotification extends Notification implements ShouldQueue
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
            ->subject('Your account has been frozen')
            ->greeting('Hello '.$notifiable->name)
            ->line('Your account has been frozen by the administrator.')
            ->line('You will not be able to access or perform actions until it is reactivated.')
            ->line('If you believe this is a mistake, please contact support.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'account_frozen'
        ];
    }
}
