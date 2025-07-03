<?php

namespace App\Actions\Terminals\CreateTerminal;

final class CreateTerminalCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $timezone;

    /**
     * @var int
     */
    private $companyId;

    /**
     * @var int
     */
    private $blocked;

    /**
     * @var int
     */
    private $pvId;

    /**
     * @var int|null
     */
    private $stampId;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(
        string $name,
        string $timezone,
        int $companyId,
        int $blocked,
        int $pvId,
        ?int $stampId,
        ?string $description
    ) {
        $this->name = $name;
        $this->timezone = $timezone;
        $this->companyId = $companyId;
        $this->blocked = $blocked;
        $this->pvId = $pvId;
        $this->stampId = $stampId;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getBlocked(): int
    {
        return $this->blocked;
    }

    public function getPvId(): int
    {
        return $this->pvId;
    }

    public function getStampId(): ?int
    {
        return $this->stampId;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
