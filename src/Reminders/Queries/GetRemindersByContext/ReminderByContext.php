<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;

final class ReminderByContext
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $content;

    /** @var DateTimeImmutable|null */
    private $expiresAt;
    /**
     * @var DateTimeImmutable|null
     */
    private $expiresInMinutes;
    /**
     * @var bool
     */
    private $hiddenFromInitiator;

    /**
     * @var array
     */
    private $usersToNotify;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $content
     * @param bool $hiddenFromInitiator
     * @param int[] $usersToNotify
     * @param DateTimeImmutable|null $expiresAt
     * @param int|null $expiresInMinutes
     */
    public function __construct(Uuid $id, string $name, string $content, bool $hiddenFromInitiator, array $usersToNotify, ?DateTimeImmutable $expiresAt, ?int $expiresInMinutes)
    {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->hiddenFromInitiator = $hiddenFromInitiator;
        $this->usersToNotify = $usersToNotify;
        $this->expiresAt = $expiresAt;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getExpiresAt(): ?DateTimeImmutable
    {
        if ($this->expiresAt) {
            return $this->expiresAt;
        }

        if ($this->expiresInMinutes !== null) {
            return (new DateTimeImmutable())->modify("+$this->expiresInMinutes minutes");
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isHiddenFromInitiator(): bool
    {
        return $this->hiddenFromInitiator;
    }

    /**
     * @return array
     */
    public function getUsersToNotify(): array
    {
        return $this->usersToNotify;
    }
}
