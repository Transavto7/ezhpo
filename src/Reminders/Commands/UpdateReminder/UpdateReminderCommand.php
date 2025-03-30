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
}
