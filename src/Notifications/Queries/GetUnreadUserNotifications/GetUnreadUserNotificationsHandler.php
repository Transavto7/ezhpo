<?php

namespace Src\Notifications\Queries\GetUnreadUserNotifications;

class GetUnreadUserNotificationsHandler
{
    /** @var GetUnreadUserNotificationsRepository */
    private $repository;

    /**
     * @param GetUnreadUserNotificationsRepository $repository
     */
    public function __construct(GetUnreadUserNotificationsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param GetUnreadUserNotificationsQuery $query
     * @return NotificationViewModel[]
     */
    public function handle(GetUnreadUserNotificationsQuery $query): array
    {
        return $this->repository->getUnread($query->getUserId());
    }
}
