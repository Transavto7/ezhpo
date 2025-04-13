<?php

namespace Src\Notifications\Entitites;

use DateTimeImmutable;
use Exception;
use Src\Core\ValueObjects\Uuid;

class NotificationLog
{
    /**
     * @var Uuid
     */
    private $notificationId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var string
     */
    private $type;

    /**
     * @var Uuid
     */
    private $id;

    /**
     * @param Uuid $notificationId
     * @param ?int $userId
     * @param DateTimeImmutable $createdAt
     * @param string $type
     * @throws Exception
     */
    public function __construct(Uuid $notificationId, ?int $userId, DateTimeImmutable $createdAt, string $type)
    {
        $this->id = Uuid::next();
        $this->notificationId = $notificationId;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->type = $type;
    }

    /**
     * @return Uuid
     */
    public function getNotificationId(): Uuid
    {
        return $this->notificationId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }
}
