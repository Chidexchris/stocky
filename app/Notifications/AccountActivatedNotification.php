<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $planName;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($planName)
    {
        $this->planName = $planName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $fromAddress = config('mail.from.address');
        if (empty($fromAddress)) {
            $fromAddress = env('MAIL_FROM_ADDRESS', 'no-reply@dtrecord.com');
        }
        if (empty($fromAddress)) {
            $fromAddress = 'no-reply@dtrecord.com';
        }

        $fromName = config('mail.from.name');
        if (empty($fromName)) {
            $fromName = env('MAIL_FROM_NAME', 'dtrecord Support');
        }
        if (empty($fromName)) {
            $fromName = 'dtrecord Support';
        }

        return (new MailMessage)
                    ->from($fromAddress, $fromName)
                    ->subject('Account Activated - Welcome to dtrecord')
                    ->greeting('Hello ' . $notifiable->name . '!')
                    ->line('Your account has been successfully activated and your subscription is now active.')
                    ->line('Plan: ' . $this->planName)
                    ->action('Go to Dashboard', url('/'))
                    ->line('Thank you for choosing dtrecord!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Account Activated',
            'message' => 'Your subscription to the ' . $this->planName . ' plan is now active.',
            'plan' => $this->planName,
            'type' => 'account_activated'
        ];
    }
}
