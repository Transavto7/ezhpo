<?php

declare(strict_types=1);

namespace Src\Terminals\Queries\GetSyncPageQuery;

final class TerminalViewModel
{
    /** @var int */
    private $id;

    /** @var string */
    private $text;

    /** @var string|null */
    private $description;

    public function __construct(int $id, string $text, ?string $description = null)
    {
        $this->id = $id;
        $this->text = $text;
        $this->description = $description;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'description' => $this->description,
        ];
    }
}
