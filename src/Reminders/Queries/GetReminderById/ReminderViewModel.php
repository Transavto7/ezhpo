<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetReminderById;

use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Core\ValueObjects\Uuid;

final class ReminderViewModel
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $content;

    /** @var ClassifierViewModel */
    private $action;

    /** @var array<string, ClassifierViewModel> */
    private $conditions;

    /** @var string */
    private $status;

    /** @var string */
    private $type;

    /**
     * @param Uuid $id
     * @param string $title
     * @param string $content
     * @param ClassifierViewModel $action
     * @param ClassifierViewModel[] $conditions
     * @param string $status
     * @param string $type
     */
    public function __construct(
        Uuid $id,
        string $title,
        string $content,
        ClassifierViewModel $action,
        array $conditions,
        string $status,
        string $type
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->action = $action;
        $this->conditions = $conditions;
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

    public function getAction(): ClassifierViewModel
    {
        return $this->action;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'title' => $this->title,
            'content' => $this->content,
            'action' => $this->action->toArray(),
            'conditions' => $this->conditions,
            'status' => $this->status,
            'type' => $this->type,
        ];
    }
}
