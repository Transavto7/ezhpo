<?php

namespace Src\Users\Management\Queries\GetUserItem;

use JsonSerializable;

final class EntityViewModel implements JsonSerializable
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
     * @var bool
     */
    private $isDeleted;
    /**
     * @var string
     */
    private $url;

    /**
     * @param string $hashId
     * @param string $name
     * @param bool $isDeleted
     * @param string $url
     */
    public function __construct(string $hashId, string $name, bool $isDeleted, string $url)
    {
        $this->hashId = $hashId;
        $this->name = $name;
        $this->isDeleted = $isDeleted;
        $this->url = $url;
    }

    public function getHashId(): string
    {
        return $this->hashId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function jsonSerialize(): array
    {
        return [
            'hashId' => $this->hashId,
            'name' => $this->name,
            'isDeleted' => $this->isDeleted,
            'url' => $this->url,
        ];
    }
}