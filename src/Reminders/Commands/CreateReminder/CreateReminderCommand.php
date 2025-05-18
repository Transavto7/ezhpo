<?php

declare(strict_types=1);

namespace Src\Reminders\Commands\CreateReminder;

use DateTimeImmutable;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;

final class CreateReminderCommand
{
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

    /** @var bool */
    private $hiddenFromInitiator;

    /** @var array */
    private $usersToNotify;

    /** @var DateTimeImmutable|null */
    private $expiresAt;

    /** * @var int|null */
    private $expiresInMinutes;

    /**
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
        string $title,
        string $content,
        ReminderAction $action,
        array $context,
        ReminderStatus $status,
        ReminderType $type,
        bool $hiddenFromInitiator,
        array $usersToNotify,
        ?DateTimeImmutable $expiresAt,
        ?int $expiresInMinutes
    ) {
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAction(): ReminderAction
    {
        return $this->action;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getStatus(): ReminderStatus
    {
        return $this->status;
    }

    public function getType(): ReminderType
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function hiddenFromInitiator(): bool
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

    /**
     * @return DateTimeImmutable|null
     */
    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return int|null
     */
    public function getExpiresInMinutes(): ?int
    {
        return $this->expiresInMinutes;
    }
}
