<?php

declare(strict_types=1);

namespace Src\Core\ValueObjects;

use FontLib\Table\Type\name;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

final class ClassifierViewModel implements Arrayable, JsonSerializable
{
    /** @var int|string */
    private $id;

    /** @var string */
    private $name;

    /**
     * @param int|string $id
     * @param string $name
     */
    public function __construct($id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array{id: int|string, name:string}
     */
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
