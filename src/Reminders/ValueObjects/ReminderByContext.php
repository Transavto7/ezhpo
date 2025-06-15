<?php

declare(strict_types=1);

namespace Src\Reminders\ValueObjects;

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
     * @var bool
     */
    private $oneTimePerUser;

    /**
     * @var bool
     */
    private $untilAnyUserCompletes;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $content
     * @param DateTimeImmutable|null $expiresAt
     * @param DateTimeImmutable|null $expiresInMinutes
     * @param bool $hiddenFromInitiator
     * @param array $usersToNotify
     * @param bool $oneTimePerUser
     * @param bool $untilAnyUserCompletes
     */
    public function __construct(
        Uuid $id,
        string $name,
        string $content,
        ?DateTimeImmutable $expiresAt,
        ?DateTimeImmutable $expiresInMinutes,
        bool $hiddenFromInitiator,
        array $usersToNotify,
        bool $oneTimePerUser,
        bool $untilAnyUserCompletes
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
        $this->expiresAt = $expiresAt;
        $this->expiresInMinutes = $expiresInMinutes;
        $this->hiddenFromInitiator = $hiddenFromInitiator;
        $this->usersToNotify = $usersToNotify;
        $this->oneTimePerUser = $oneTimePerUser;
        $this->untilAnyUserCompletes = $untilAnyUserCompletes;
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

    public function isOneTimePerUser(): bool
    {
        return $this->oneTimePerUser;
    }

    public function isUntilAnyUserCompletes(): bool
    {
        return $this->untilAnyUserCompletes;
    }
}
