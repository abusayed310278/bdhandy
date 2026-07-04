<?php

namespace App\Notifications;

use App\Channels\OneSignalChannel;
use App\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $channels = ['database'];

    /**
     * Return the unique event-type key used for config-based channel resolution.
     * Example: 'service_request.status_changed'
     */
    abstract public function getEventType(): string;

    /**
     * Injected by NotificationService before dispatching.
     */
    public function setChannels(array $channels): static
    {
        $this->channels = $channels;
        return $this;
    }

    public function via(mixed $notifiable): array
    {
        return $this->channels;
    }

    // ──────────────────────────────────────────────────────────────────────
    // Channel payload methods
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Every subclass must implement this — it powers the DB record AND
     * provides the default push/SMS body fallback.
     *
     * Must return:
     *   [
     *     'type'  => 'service_request.status_changed',
     *     'title' => 'Request Status Updated',
     *     'body'  => 'Your request #SR-001 is now In Progress.',
     *     'url'   => 'https://…',   // optional — deep-link for mobile
     *     'meta'  => [...],         // optional — arbitrary extra data
     *   ]
     */
    abstract public function toDatabase(mixed $notifiable): array;

    /**
     * Default push payload for OneSignal.
     * Subclasses can override for richer push content.
     */
    public function toOneSignal(mixed $notifiable): array
    {
        $data = $this->toDatabase($notifiable);

        return [
            'headings' => ['en' => $data['title']],
            'contents' => ['en' => $data['body']],
            'url'      => $data['url'] ?? null,
            'data'     => $data['meta'] ?? [],
        ];
    }

    /**
     * Default SMS body.
     * Subclasses can override for a custom SMS message.
     */
    public function toSms(mixed $notifiable): string
    {
        $data = $this->toDatabase($notifiable);
        return $data['title'] . ': ' . $data['body'];
    }

    /**
     * Default mail message. Subclasses should override for branded emails.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $data = $this->toDatabase($notifiable);

        $mail = (new MailMessage)
            ->subject($data['title'] . ' — ' . config('app.name'))
            ->line($data['body']);

        if (!empty($data['url'])) {
            $mail->action('View Details', $data['url']);
        }

        return $mail;
    }
}
