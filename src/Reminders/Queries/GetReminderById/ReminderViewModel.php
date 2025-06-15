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

    /** @var array<string, ClassifierViewModel> */
    private $conditions;

    /** @var \DateTimeImmutable|null */
    private $expiresAt;

    /** @var int|null */
    private $expiresInMinutes;

    /** @var ClassifierViewModel */
    private $status;

    /** @var ClassifierViewModel */
    private $action;

    /** @var ClassifierViewModel */
    private $type;

    /**
     * @var bool
     */
    private $hiddenFromInitiator;

    /**
     * @var ClassifierViewModel[]
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
     * @param string $title
     * @param string $content
     * @param ClassifierViewModel[] $conditions
     * @param \DateTimeImmutable|null $expiresAt
     * @param int|null $expiresInMinutes
     * @param ClassifierViewModel $status
     * @param ClassifierViewModel $action
     * @param ClassifierViewModel $type
     * @param bool $hiddenFromInitiator
     * @param ClassifierViewModel[] $usersToNotify
     * @param bool $oneTimePerUser
     * @param bool $untilAnyUserCompletes
     */
    public function __construct(
        Uuid $id,
        string $title,
        string $content,
        array $conditions,
        ?\DateTimeImmutable $expiresAt,
        ?int $expiresInMinutes,
        ClassifierViewModel $status,
        ClassifierViewModel $action,
        ClassifierViewModel $type,
        bool $hiddenFromInitiator,
        array $usersToNotify,
        bool $oneTimePerUser,
        bool $untilAnyUserCompletes
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->conditions = $conditions;
        $this->expiresAt = $expiresAt;
        $this->expiresInMinutes = $expiresInMinutes;
        $this->status = $status;
        $this->action = $action;
        $this->type = $type;
        $this->hiddenFromInitiator = $hiddenFromInitiator;
        $this->usersToNotify = $usersToNotify;
        $this->oneTimePerUser = $oneTimePerUser;
        $this->untilAnyUserCompletes = $untilAnyUserCompletes;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'title' => $this->title,
            'content' => $this->content,
            'conditions' => $this->conditions,
            'expiresAt' => $this->expiresAt ? $this->expiresAt->format('Y-m-d H:i') : null,
            'expiresInMinutes' => $this->expiresInMinutes,
            'status' => $this->status->toArray(),
            'action' => $this->action->toArray(),
            'type' => $this->type->toArray(),
            'hiddenFromInitiator' => $this->hiddenFromInitiator,
            'usersToNotify' => array_map(function (ClassifierViewModel $user) {
                return $user->toArray();
            }, $this->usersToNotify),
            'oneTimePerUser' => $this->oneTimePerUser,
            'untilAnyUserCompletes' => $this->untilAnyUserCompletes,
        ];
    }
}
