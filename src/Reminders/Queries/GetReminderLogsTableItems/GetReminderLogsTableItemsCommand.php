<?php

namespace Src\Reminders\Queries\GetReminderLogsTableItems;

use Src\Reminders\Queries\GetRemindersTableItems\RemindersTableFilters;

class GetReminderLogsTableItemsCommand
{
    private $reminderFilters = null;
    private $page;
    private $perPage;
    private $sortBy;
    private $orderBy;

    public function __construct(?RemindersTableFilters $reminderFilters, int $page, int $perPage, string $sortBy, string $orderBy)
    {
        $this->reminderFilters = $reminderFilters;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->orderBy = $orderBy;
    }

    public function getReminderFilters(): ?RemindersTableFilters
    {
        return $this->reminderFilters;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }
}
