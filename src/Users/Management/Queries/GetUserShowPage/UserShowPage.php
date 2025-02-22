<?php

namespace Src\Users\Management\Queries\GetUserShowPage;

final class UserShowPage
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
    private $canAccessChange;
    /**
     * @var bool
     */
    private $canReadLogs;

    /**
     * @param bool $canRead
     * @param bool $canPasswordChange
     * @param bool $canBlock
     * @param bool $canAccessChange
     * @param bool $canReadLogs
     */
    public function __construct(
        bool $canRead,
        bool $canPasswordChange,
        bool $canBlock,
        bool $canAccessChange,
        bool $canReadLogs
    )
    {
        $this->canRead = $canRead;
        $this->canPasswordChange = $canPasswordChange;
        $this->canBlock = $canBlock;
        $this->canAccessChange = $canAccessChange;
        $this->canReadLogs = $canReadLogs;
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

    public function isCanAccessChange(): bool
    {
        return $this->canAccessChange;
    }

    public function isCanReadLogs(): bool
    {
        return $this->canReadLogs;
    }
}