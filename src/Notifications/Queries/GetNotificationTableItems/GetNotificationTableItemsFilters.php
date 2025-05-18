<?php

namespace Src\Notifications\Queries\GetNotificationTableItems;

use Carbon\Carbon;
use Src\Core\Filters\Dto\DateRange;
use Src\Notifications\Enums\NotificationFilterStatus;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\CompletedAtFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\CreatedAtFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\ExpiresAtFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\InitiatorUsersFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\NotificationsFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\RemindersFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\SearchFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\StatusFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\UsersFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\ViewedAtFilter;

final class GetNotificationTableItemsFilters
{
    /**
     * @var int[]
     */
    private $notificationsIds;

    /**
     * @var string
     */
    private $search;

    /**
     * @var int[]
     */
    private $users;

    /**
     * @var int[]
     */
    private $initiatorUsers;

    /**
     * @var int[]
     */
    private $reminders;

    /**
     * @var Carbon|null
     */
    private $expiresAtBegin;

    /**
     * @var Carbon|null
     */
    private $expiresAtEnd;

    /**
     * @var Carbon|null
     */
    private $viewedAtBegin;

    /**
     * @var Carbon|null
     */
    private $viewedAtEnd;

    /**
     * @var Carbon|null
     */
    private $completedAtBegin;

    /**
     * @var Carbon|null
     */
    private $completedAtEnd;

    /**
     * @var Carbon|null
     */
    private $createdAtBegin;

    /**
     * @var Carbon|null
     */
    private $createdAtEnd;

    /**
     * @var NotificationFilterStatus|null
     */
    private $status;

    /**
     * @param int[] $notificationsIds
     * @param string $search
     * @param int[] $users
     * @param int[] $initiatorUsers
     * @param int[] $reminders
     * @param Carbon|null $expiresAtBegin
     * @param Carbon|null $expiresAtEnd
     * @param Carbon|null $viewedAtBegin
     * @param Carbon|null $viewedAtEnd
     * @param Carbon|null $completedAtBegin
     * @param Carbon|null $completedAtEnd
     * @param Carbon|null $createdAtBegin
     * @param Carbon|null $createdAtEnd
     * @param NotificationFilterStatus|null $status
     */
    public function __construct(
        array $notificationsIds,
        string $search,
        array $users,
        array $initiatorUsers,
        array $reminders,
        ?Carbon $expiresAtBegin,
        ?Carbon $expiresAtEnd,
        ?Carbon $viewedAtBegin,
        ?Carbon $viewedAtEnd,
        ?Carbon $completedAtBegin,
        ?Carbon $completedAtEnd,
        ?Carbon $createdAtBegin,
        ?Carbon $createdAtEnd,
        ?NotificationFilterStatus $status
    ) {
        $this->notificationsIds = $notificationsIds;
        $this->search = $search;
        $this->users = $users;
        $this->initiatorUsers = $initiatorUsers;
        $this->reminders = $reminders;
        $this->expiresAtBegin = $expiresAtBegin;
        $this->expiresAtEnd = $expiresAtEnd;
        $this->viewedAtBegin = $viewedAtBegin;
        $this->viewedAtEnd = $viewedAtEnd;
        $this->completedAtBegin = $completedAtBegin;
        $this->completedAtEnd = $completedAtEnd;
        $this->createdAtBegin = $createdAtBegin;
        $this->createdAtEnd = $createdAtEnd;
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            SearchFilter::NAME => $this->search,
            UsersFilter::NAME => $this->users,
            InitiatorUsersFilter::NAME => $this->initiatorUsers,
            NotificationsFilter::NAME => $this->notificationsIds,
            RemindersFilter::NAME => $this->reminders,
            ViewedAtFilter::NAME => new DateRange($this->viewedAtBegin, $this->viewedAtEnd),
            CompletedAtFilter::NAME => new DateRange($this->completedAtBegin, $this->completedAtEnd),
            ExpiresAtFilter::NAME => new DateRange($this->expiresAtBegin, $this->expiresAtEnd),
            CreatedAtFilter::NAME => new DateRange($this->createdAtBegin, $this->createdAtEnd),
            StatusFilter::NAME => $this->status,
        ];
    }
}
