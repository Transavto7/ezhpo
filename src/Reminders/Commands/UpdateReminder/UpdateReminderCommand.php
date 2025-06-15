<?php

declare(strict_types=1);

namespace Src\Reminders\Commands\UpdateReminder;

use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;

final class UpdateReminderCommand
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

    /** @var bool */
    private $hiddenFromInitiator;

    /** @var array */
    private $usersToNotify;

    /** @var \DateTimeImmutable|null */
    private $expiresAt;

    /** @var int|null */
    private $expiresInMinutes;

    /** @var bool */
    private $oneTimePerUser;

    /** @var bool */
    private $untilAnyUserCompletes;

    /** @var ?int */
    private $userId;

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
     * @param \DateTimeImmutable|null $expiresAt
     * @param int|null $expiresInMinutes
     * @param bool $oneTimePerUser
     * @param bool $untilAnyUserCompletes
     * @param int|null $userId
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
        ?\DateTimeImmutable $expiresAt,
        ?int $expiresInMinutes,
        bool $oneTimePerUser,
        bool $untilAnyUserCompletes,
        ?int $userId
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
        $this->userId = $userId;
        $this->oneTimePerUser = $oneTimePerUser;
        $this->untilAnyUserCompletes = $untilAnyUserCompletes;
    }

    public function getId(): Uuid
    {
        return $this->id;
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

    public function getUsersToNotify(): array
    {
        return $this->usersToNotify;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function getExpiresInMinutes(): ?int
    {
        return $this->expiresInMinutes;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function isHiddenFromInitiator(): bool
    {
        return $this->hiddenFromInitiator;
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
