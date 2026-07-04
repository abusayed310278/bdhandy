<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Reset Your Password — ' . config('app.name'))
            ->view('emails.reset-password', [
                'user'     => $notifiable,
                'resetUrl' => $resetUrl,
                'count'    => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
            ]);
    }
}
