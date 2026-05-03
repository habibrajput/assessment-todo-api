<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Class VerifyEmailNotification
 *
 * Sends a verification link to a newly registered user.
 * The link embeds the raw token; the API endpoint validates it.
 *
 * @package App\Notifications
 */
class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * @param  string $verificationCode  Random 64-char token stored on the User
     */
    public function __construct(private readonly string $verificationCode) {}

    // ── Channels ──────────────────────────────────────────────────────────────

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // ── Mail builder ──────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        // Build the verification URL that will be embedded in the e-mail.
        $verificationUrl = config('app.url')
            . '/api/auth/verify/'
            . $this->verificationCode;

        return (new MailMessage)
            ->subject('Please verify your email address')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Thanks for signing up. Before you can log in, we need to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This link will remain active. If you did not create an account, no further action is required.')
            ->salutation('The ' . config('app.name') . ' Team');
    }
}
