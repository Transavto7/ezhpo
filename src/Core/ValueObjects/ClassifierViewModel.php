<?php

declare(strict_types=1);

namespace Src\Core\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class ClassifierViewModel implements Arrayable, JsonSerializable
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @param int $id
     * @param string $name
     */
    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
