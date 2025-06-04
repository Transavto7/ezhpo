<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems;

use Src\Core\Filters\Filter;
use Src\Notifications\Enums\NotificationFilterStatus;

abstract class NotificationsStatusFilter implements Filter
{
    protected $column = 'default';

    /**
     * @var NotificationFilterStatus|null
     */
    protected $value;

    final private function __construct($value)
    {
        $this->value = $value;
    }

    public function apply($query)
    {
        if ($this->value->equal(NotificationFilterStatus::READ)) {
            $query->whereNotNull('notifications.read_at');
        }

        if ($this->value->equal(NotificationFilterStatus::COMPLETED)) {
            $query->whereNotNull('notifications.completed_at');
        }

        if ($this->value->equal(NotificationFilterStatus::EXPIRED)) {
            $query->where('notifications.is_expired', '!=', 0);
        }
    }

    public static function create($value): Filter
    {
        return new static($value);
    }

    public function validateValue(): bool
    {
        return $this->value instanceof NotificationFilterStatus;
    }
}
