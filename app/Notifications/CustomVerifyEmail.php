<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->greeting('Hello!')
            ->line('Thanks for signing up! Please verify your email address by clicking the button below.')
            ->action('Verify Email Address', $url)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Thanks, ' . config('app.name'));
    }
}