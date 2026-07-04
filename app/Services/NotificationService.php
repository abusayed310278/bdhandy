<?php

namespace App\Services;

use App\Channels\OneSignalChannel;
use App\Channels\SmsChannel;
use App\Models\User;
use App\Notifications\BaseNotification;

class NotificationService
{
    /**
     * Send a notification to a single user.
     * Database channel is always included.
     * Additional channels are resolved from the user's NotificationPreference
     * layered on top of system-wide defaults in config/notifications.php.
     */
    public function send(User $user, BaseNotification $notification): void
    {
        $channels = $this->resolveChannels($user, $notification->getEventType());
        $notification->setChannels($channels);
        $user->notify($notification);
    }

    /**
     * Send to multiple users (e.g. notify all nearby providers).
     * Clones the notification for each recipient so channel lists are independent.
     */
    public function sendToMany(iterable $users, BaseNotification $notification): void
    {
        foreach ($users as $user) {
            $this->send($user, clone $notification);
        }
    }

    /**
     * Notify all admin / support users.
     */
    public function notifyAdmins(BaseNotification $notification): void
    {
        $admins = User::role(['super_admin', 'admin', 'support'])->get();
        $this->sendToMany($admins, $notification);
    }

    // ──────────────────────────────────────────────────────────────────────
    // Internal helpers
    // ──────────────────────────────────────────────────────────────────────

    private function resolveChannels(User $user, string $eventType): array
    {
        // Database is always on — powers the bell icon and mobile inbox
        $channels = ['database'];

        $pref = $user->notificationPreference;

        if ($this->isChannelEnabled($pref, 'email', $eventType)) {
            $channels[] = 'mail';
        }

        if ($this->isChannelEnabled($pref, 'push', $eventType)) {
            $channels[] = OneSignalChannel::class;
        }

        if ($this->isChannelEnabled($pref, 'sms', $eventType)) {
            $channels[] = SmsChannel::class;
        }

        return $channels;
    }

    /**
     * Resolve whether a channel is active for a given event type.
     *
     * Priority:
     *   1. User's global master switch (e.g. sms_enabled = false → never)
     *   2. User's per-event override in event_preferences JSON
     *   3. System default in config/notifications.defaults
     */
    private function isChannelEnabled(
        ?\App\Models\NotificationPreference $pref,
        string $channel,
        string $eventType
    ): bool {
        // No preference record → fall back to system defaults only
        if (!$pref) {
            return (bool) config("notifications.defaults.{$eventType}.{$channel}", false);
        }

        // 1. Global master switch
        $globalKey = $channel . '_enabled';
        if (isset($pref->$globalKey) && !$pref->$globalKey) {
            return false;
        }

        // 2. Per-event user override
        $eventPrefs = $pref->event_preferences ?? [];
        if (isset($eventPrefs[$eventType][$channel])) {
            return (bool) $eventPrefs[$eventType][$channel];
        }

        // 3. System default
        return (bool) config("notifications.defaults.{$eventType}.{$channel}", false);
    }
}
