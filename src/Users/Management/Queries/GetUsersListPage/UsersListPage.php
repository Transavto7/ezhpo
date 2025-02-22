<?php

namespace Src\Users\Management\Queries\GetUsersListPage;

final class UsersListPage
{
    /**
     * @var bool
     */
    private $canRead;

    /**
     * @var bool
     */
    private $canPasswordChange;

    /**
     * @var bool
     */
    private $canBlock;

    /**
     * @var bool
     */
    private $canReadLogs;

    /**
     * @var FilterOption[]
     */
    private $statusFilterOptions;

    /**
     * @var FilterOption[]
     */
    private $entityTypeFilterOptions;

    /**
     * @param bool $canRead
     * @param bool $canPasswordChange
     * @param bool $canBlock
     * @param bool $canReadLogs
     * @param FilterOption[] $statusFilterOptions
     * @param FilterOption[] $entityTypeFilterOptions
     */
    public function __construct(
        bool $canRead,
        bool $canPasswordChange,
        bool $canBlock,
        bool $canReadLogs,
        array $statusFilterOptions,
        array $entityTypeFilterOptions
    ) {
        $this->canRead = $canRead;
        $this->canPasswordChange = $canPasswordChange;
        $this->canBlock = $canBlock;
        $this->canReadLogs = $canReadLogs;
        $this->statusFilterOptions = $statusFilterOptions;
        $this->entityTypeFilterOptions = $entityTypeFilterOptions;
    }

    public function isCanRead(): bool
    {
        return $this->canRead;
    }

    public function isCanPasswordChange(): bool
    {
        return $this->canPasswordChange;
    }

    public function isCanBlock(): bool
    {
        return $this->canBlock;
    }

    public function isCanReadLogs(): bool
    {
        return $this->canReadLogs;
    }

    public function getStatusFilterOptions(): array
    {
        return $this->statusFilterOptions;
    }

    public function getEntityTypeFilterOptions(): array
    {
        return $this->entityTypeFilterOptions;
    }
}
