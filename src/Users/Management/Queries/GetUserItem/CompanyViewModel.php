<?php

namespace Src\Users\Management\Queries\GetUserItem;

use JsonSerializable;

final class CompanyViewModel implements JsonSerializable
{
    /**
     * @var string
     */
    private $hashId;
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $hashId
     * @param string $name
     */
    public function __construct(string $hashId, string $name)
    {
        $this->hashId = $hashId;
        $this->name = $name;
    }

    public function getHashId(): string
    {
        return $this->hashId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'hashId' => $this->hashId,
            'name' => $this->name,
        ];
    }
}