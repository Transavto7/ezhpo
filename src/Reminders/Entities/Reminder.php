<?php

declare(strict_types=1);

namespace Src\Reminders\Entities;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;

final class Reminder
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $content;

    /** @var ReminderAction */
    private $action;

    /** @var array<Condition> */
    private $context;

    /** @var ReminderStatus */
    private $status;

    /** @var ReminderType */
    private $type;

    /** @var DateTimeImmutable|null */
    private $expiresAt;

    /** @var int|null */
    private $expiresInMinutes;

    /** @var bool */
    private $hiddenFromInitiator;

    /** @var int[] */
    private $usersToNotify;

    /**
     * @param Uuid $id
     * @param string $title
     * @param string $content
     * @param ReminderAction $action
     * @param Condition[] $context
     * @param ReminderStatus $status
     * @param ReminderType $type
     * @param bool $hiddenFromInitiator
     * @param array $usersToNotify
     * @param DateTimeImmutable|null $expiresAt
     * @param int|null $expiresInMinutes
     */
    public function __construct(
        Uuid $id,
        string $title,
        string $content,
        ReminderAction $action,
        array $context,
        ReminderStatus $status,
        ReminderType $type,
        bool $hiddenFromInitiator,
        array $usersToNotify,
        ?DateTimeImmutable $expiresAt = null,
        ?int $expiresInMinutes = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->action = $action;
        $this->context = $context;
        $this->status = $status;
        $this->type = $type;
        $this->hiddenFromInitiator = $hiddenFromInitiator;
        $this->usersToNotify = $usersToNotify;
        $this->expiresAt = $expiresAt;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * @param DateTimeImmutable|null $expiresAt
     */
    public function setExpiresAt(?DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @param int|null $expiresInMinutes
     */
    public function setExpiresInMinutes(?int $expiresInMinutes): void
    {
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * @param bool $hiddenFromInitiator
     */
    public function setHiddenFromInitiator(bool $hiddenFromInitiator): void
    {
        $this->hiddenFromInitiator = $hiddenFromInitiator;
    }

    /**
     * @param array|int[] $usersToNotify
     */
    public function setUsersToNotify(array $usersToNotify): void
    {
        $this->usersToNotify = $usersToNotify;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getAction(): ReminderAction
    {
        return $this->action;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): ReminderStatus
    {
        return $this->status;
    }

    public function getType(): ReminderType
    {
        return $this->type;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function setAction(ReminderAction $action): void
    {
        $this->action = $action;
    }

    public function setStatus(ReminderStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function setType(ReminderType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array<Condition> $contexts
     * @return self
     */
    public function setContext(array $contexts): self
    {
        $this->context = $contexts;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getExpiresInMinutes(): ?int
    {
        return $this->expiresInMinutes;
    }

    public function isHiddenFromInitiator(): bool
    {
        return $this->hiddenFromInitiator;
    }

    public function getUsersToNotify(): array
    {
        return $this->usersToNotify;
    }
}
