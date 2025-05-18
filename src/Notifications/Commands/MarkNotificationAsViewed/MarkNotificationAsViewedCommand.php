<?php

namespace Src\Notifications\Commands\MarkNotificationAsViewed;

use Src\Core\ValueObjects\Uuid;

class MarkNotificationAsViewedCommand
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
     * @param Uuid $notificationId
     * @param int $userId
     */
    public function __construct(Uuid $notificationId, int $userId)
    {
        $this->notificationId = $notificationId;
        $this->userId = $userId;
    }

    public function getNotificationId(): Uuid
    {
        return $this->notificationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }


}
