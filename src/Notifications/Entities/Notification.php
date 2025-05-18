<?php

namespace Src\Notifications\Entities;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;

class Notification
{
    /** @var Uuid */
    private $id;

    /**
     * @var Uuid|null
     */
    private $reminderId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var int
     */
    private $recipientId;

    /**
     * @var int|null
     */
    private $senderId;

    /**
     * @var DateTimeImmutable|null
     */
    private $viewedAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $readAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $completedAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $expiresAt;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @param Uuid $id
     * @param Uuid|null $reminderId
     * @param string $title
     * @param string $content
     * @param int $recipientId
     * @param int|null $senderId
     * @param DateTimeImmutable|null $viewedAt
     * @param DateTimeImmutable|null $readAt
     * @param DateTimeImmutable|null $completedAt
     * @param DateTimeImmutable|null $expiresAt
     * @param DateTimeImmutable $createdAt
     */
    public function __construct(
        Uuid $id,
        ?Uuid $reminderId,
        string $title,
        string $content,
        int $recipientId,
        ?int $senderId,
        ?DateTimeImmutable $viewedAt,
        ?DateTimeImmutable $readAt,
        ?DateTimeImmutable $completedAt,
        ?DateTimeImmutable $expiresAt,
        DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->reminderId = $reminderId;
        $this->title = $title;
        $this->content = $content;
        $this->recipientId = $recipientId;
        $this->senderId = $senderId;
        $this->viewedAt = $viewedAt;
        $this->readAt = $readAt;
        $this->completedAt = $completedAt;
        $this->expiresAt = $expiresAt;
        $this->createdAt = $createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getReminderId(): ?Uuid
    {
        return $this->reminderId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function getViewedAt(): ?DateTimeImmutable
    {
        return $this->viewedAt;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt <= $this->createdAt;
    }

    public function viewed(DateTimeImmutable $now)
    {
        if ($this->viewedAt === null) {
            $this->viewedAt = $now;
        }
    }

    public function read(DateTimeImmutable $now)
    {
        if ($this->readAt === null) {
            $this->readAt = $now;
        }
    }

    public function completed(DateTimeImmutable $now)
    {
        if ($this->completedAt === null) {
            $this->completedAt = $now;
        }
    }
}
