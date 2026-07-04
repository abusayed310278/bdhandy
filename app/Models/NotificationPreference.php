<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_enabled',
        'sms_enabled',
        'push_enabled',
        'whatsapp_enabled',
        'marketing_enabled',
        'event_preferences',   // JSON: per-event-type channel overrides
    ];

    protected $attributes = [
        'email_enabled'     => true,
        'sms_enabled'       => true,
        'push_enabled'      => true,
        'whatsapp_enabled'  => true,
        'marketing_enabled' => true,
        'event_preferences' => '{}',
    ];

    protected $casts = [
        'email_enabled'      => 'boolean',
        'sms_enabled'        => 'boolean',
        'push_enabled'       => 'boolean',
        'whatsapp_enabled'   => 'boolean',
        'marketing_enabled'  => 'boolean',
        'event_preferences'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a given channel is active for a specific event type.
     *
     * Priority:
     *   1. Global master switch (e.g. sms_enabled = false → never send SMS)
     *   2. Per-event user override stored in event_preferences JSON
     *   3. System default from config/notifications.php
     */
    public function isChannelEnabled(string $channel, string $eventType): bool
    {
        // 1. Global master switch
        $globalKey = $channel . '_enabled';
        if (property_exists($this, $globalKey) || isset($this->attributes[$globalKey])) {
            if (!$this->$globalKey) {
                return false;
            }
        }

        // 2. Per-event override
        $eventPrefs = $this->event_preferences ?? [];
        if (isset($eventPrefs[$eventType][$channel])) {
            return (bool) $eventPrefs[$eventType][$channel];
        }

        // 3. System default
        return (bool) config("notifications.defaults.{$eventType}.{$channel}", false);
    }
}
