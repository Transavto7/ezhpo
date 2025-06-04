<?php

namespace Src\Notifications\Queries\GetUnreadUserNotifications;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Entities\Notification;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderType;

class NotificationViewModel implements \JsonSerializable
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $expiresAt;

    /**
     * @var bool
     */
    private $isExpired;

    /**
     * @var bool
     */
    private $isImmediate;

    /**
     * @var ReminderType
     */
    private $reminderType;

    /**
     * @var ReminderAction
     */
    private $reminderAction;

    /**
     * @param Uuid $id
     * @param string $title
     * @param string $content
     * @param DateTimeImmutable $createdAt
     * @param DateTimeImmutable|null $expiresAt
     * @param bool $isExpired
     * @param bool $isImmediate
     * @param ReminderType $reminderType
     * @param ReminderAction $reminderAction
     */
    public function __construct(
        Uuid $id,
        string $title,
        string $content,
        DateTimeImmutable $createdAt,
        ?DateTimeImmutable $expiresAt,
        bool $isExpired,
        bool $isImmediate,
        ReminderType $reminderType,
        ReminderAction $reminderAction
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
        $this->isExpired = $isExpired;
        $this->isImmediate = $isImmediate;
        $this->reminderType = $reminderType;
        $this->reminderAction = $reminderAction;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'title' => $this->title,
            'content' => $this->content,
            'expiresAt' => $this->expiresAt ? $this->expiresAt->format('H:i d.m.Y') : null,
            'isExpired' => $this->isExpired,
            'isImmediate' => $this->isImmediate,
            'reminderType' => [
                'value' => $this->reminderType->value(),
                'title' => $this->reminderType->getTitle(),
            ],
            'reminderAction' => [
                'value' => $this->reminderAction->value(),
                'title' => $this->reminderAction->getTitle(),
            ],
        ];
    }

    public static function createFrom(Notification $notification, ReminderType $reminderType, ReminderAction $reminderAction): self
    {
        $isImmediate = ! $notification->getReadAt() && $notification->getSenderId() === $notification->getRecipientId();

        return new self(
            $notification->getId(),
            $notification->getTitle(),
            $notification->getContent(),
            $notification->getCreatedAt(),
            $notification->getExpiresAt(),
            $notification->isExpired(),
            $isImmediate,
            $reminderType,
            $reminderAction
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
