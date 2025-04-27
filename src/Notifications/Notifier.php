<?php

declare(strict_types=1);

namespace Src\Notifications;

interface Notifier
{
    /**
     * Sends a notification to the specified recipient with the provided message.
     *
     * @param string $subject The recipient of the notification.
     * @param string $message The message content of the notification.
     */
    public function notify(string $subject, string $message): bool;
}
