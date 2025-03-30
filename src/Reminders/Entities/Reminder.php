<?php

declare(strict_types=1);

namespace Src\Reminders\Entities;

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

    /**
     * @param Uuid $id
     * @param string $title
     * @param string $content
     * @param ReminderAction $action
     * @param Condition[] $context
     * @param ReminderStatus $status
     * @param ReminderType $type
     */
    public function __construct(
        Uuid $id,
        string $title,
        string $content,
        ReminderAction $action,
        array $context,
        ReminderStatus $status,
        ReminderType $type
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->action = $action;
        $this->context = $context;
        $this->status = $status;
        $this->type = $type;
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
}
