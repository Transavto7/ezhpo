<?php

namespace Src\Notifications\Queries;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Entitites\Notification;

class NotificationViewModel
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
     * @param Uuid $id
     * @param string $title
     * @param string $content
     * @param DateTimeImmutable $createdAt
     * @param DateTimeImmutable|null $expiresAt
     * @param bool $isExpired
     */
    public function __construct(Uuid $id, string $title, string $content, DateTimeImmutable $createdAt, ?DateTimeImmutable $expiresAt, bool $isExpired)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->expiresAt = $expiresAt;
        $this->isExpired = $isExpired;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return $this->isExpired;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
            'expires_at' => $this->getExpiresAt() ? $this->getExpiresAt()->format('Y-m-d H:i:s') : null,
            'is_expired' => $this->isExpired()
        ];
    }

    public static function createFromNotification(Notification $notification): self
    {
        return new self($notification->getId(), $notification->getTitle(), $notification->getContent(), $notification->getCreatedAt(), $notification->getExpiresAt(), $notification->isExpired());
    }
}
