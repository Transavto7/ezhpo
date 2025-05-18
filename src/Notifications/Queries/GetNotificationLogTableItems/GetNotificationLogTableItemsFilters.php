<?php

namespace Src\Notifications\Queries\GetNotificationLogTableItems;

use Src\Notifications\Enums\NotificationLogAction;

final class GetNotificationLogTableItemsFilters
{
    /**
     * @var string
     */
    private $search;

    /**
     * @var int[]
     */
    private $notificationsIds;

    /**
     * @var int[]
     */
    private $userIds;

    /**
     * @var NotificationLogAction[]
     */
    private $actions;

    /**
     * @param string $search
     * @param int[] $notificationsIds
     * @param int[] $userIds
     * @param NotificationLogAction[] $actions
     */
    public function __construct(
        string $search,
        array $notificationsIds,
        array $userIds,
        array $actions
    ) {
        $this->search = $search;
        $this->notificationsIds = $notificationsIds;
        $this->userIds = $userIds;
        $this->actions = $actions;
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getNotificationsIds(): array
    {
        return $this->notificationsIds;
    }

    public function getUserIds(): array
    {
        return $this->userIds;
    }

    public function getActions(): array
    {
        return $this->actions;
    }
}
