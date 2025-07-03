<?php

declare(strict_types=1);

namespace App\Actions\Terminals\SetManyTerminalsDescription;

final class SetManyTerminalsDescriptionCommand
{
    /** @var array */
    private $terminalIds;

    /** @var null|string */
    private $description;

    public function __construct(array $terminalIds, ?string $description)
    {
        $this->terminalIds = $terminalIds;
        $this->description = $description;
    }

    public function getTerminalIds(): array
    {
        return $this->terminalIds;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
