<?php

namespace App\Dto;

use JsonSerializable;

class ElementDto implements JsonSerializable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $hashId;

    /**
     * @var string
     */
    private $modelType;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @param int $id
     * @param int $hashId
     * @param string $modelType
     * @param string|null $name
     */
    public function __construct(int $id, int $hashId, string $modelType, ?string $name)
    {
        $this->id = $id;
        $this->hashId = $hashId;
        $this->modelType = $modelType;
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model_type' => $this->modelType,
            'hash_id' => $this->hashId
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
