<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationCodeNotification extends Notification
{
    use Queueable;

    protected $verificationCode;

    /**
     * Create a new notification instance.
     *
     * @param string $verificationCode
     */
    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Verification Code')
            ->line('Your verification code is: ' . $this->verificationCode)
            ->line('Please use this code to verify your email address.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'verification_code' => $this->verificationCode,
        ];
    }
}
