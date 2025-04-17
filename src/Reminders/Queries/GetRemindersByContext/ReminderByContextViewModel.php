<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use Src\Core\ValueObjects\Uuid;

final class ReminderByContextViewModel
{
    /** @var Uuid */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $content;

    /**
     * @param Uuid $id
     * @param string $name
     * @param string $content
     */
    public function __construct(Uuid $id, string $name, string $content)
    {
        $this->id = $id;
        $this->name = $name;
        $this->content = $content;
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

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name,
            'content' => $this->content,
            'created_at' => (new \DateTime())->format('Y-m-d H:i'),
            'expires_at' => (new \DateTime())->format('Y-m-d H:i'),
            'is_expired' => true,
        ];
    }
}
