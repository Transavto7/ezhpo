<?php

namespace Src\Notifications\Entitites;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;

class Notification
{
    /**
     * @var DateTimeImmutable
     */
    private $createdAt;
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
    private $expiresAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $viewedAt;

    /**
     * @var DateTimeImmutable|null
     */
    private $completedAt;

    /** @var Uuid */
    private $id;

    /**
     * @param Uuid $id
     * @param DateTimeImmutable $createdAt
     * @param Uuid|null $reminderId
     * @param string $title
     * @param string $content
     * @param int $recipientId
     * @param int|null $senderId
     * @param DateTimeImmutable|null $expiresAt
     * @param DateTimeImmutable|null $viewedAt
     */
    public function __construct(
        Uuid               $id,
        DateTimeImmutable  $createdAt,
        ?Uuid              $reminderId,
        string             $title,
        string             $content,
        int                $recipientId,
        ?int               $senderId,
        ?DateTimeImmutable $expiresAt,
        ?DateTimeImmutable $viewedAt,
        ?DateTimeImmutable $completedAt
    ) {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->reminderId = $reminderId;
        $this->title = $title;
        $this->content = $content;
        $this->recipientId = $recipientId;
        $this->senderId = $senderId;
        $this->expiresAt = $expiresAt;
        $this->viewedAt = $viewedAt;
        $this->completedAt = $completedAt;
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
     * @return int
     */
    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    /**
     * @return Uuid|null
     */
    public function getReminderId(): ?Uuid
    {
        return $this->reminderId;
    }

    /**
     * @return int|null
     */
    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return $this->expiresAt <= $this->createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getViewedAt(): ?DateTimeImmutable
    {
        return $this->viewedAt;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function viewed(DateTimeImmutable $now)
    {
        if ($this->viewedAt === null) {
            $this->viewedAt = $now;
        }
    }

    public function completed(DateTimeImmutable $now)
    {
        if ($this->completedAt === null) {
            $this->completedAt = $now;
        }
    }
}
