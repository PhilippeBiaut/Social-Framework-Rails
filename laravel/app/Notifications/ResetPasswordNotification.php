<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Queued so the request doesn't block on SMTP, mirroring the Rails side's
 * `PasswordsMailer.reset(user).deliver_later`.
 */
class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], absolute: false));

        return (new MailMessage)
            ->subject('Reset your password')
            ->greeting('Hi '.$notifiable->displayName().',')
            ->line('Someone asked to reset the password for your account.')
            ->action('Reset password', url($url))
            ->line('This link expires in 60 minutes.')
            ->line("If you didn't request this, you can safely ignore this email.");
    }
}
