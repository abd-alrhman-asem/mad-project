<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    use Queueable;

    protected $customData;

    public function __construct($token, $customData = null)
    {
        parent::__construct($token);
        $this->customData = $customData; // Store any custom data if needed
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Z&N Reset Password Request')
            ->line('You have requested a password reset for your account.')
            ->line('If you did not request a password reset, please ignore this email.')
            ->action('Reset Password', $this->createResetUrl($notifiable))
            ->line('Thank you for using our application!');

    }
    protected function createResetUrl($notifiable)
    {
        return url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));
    }
}
