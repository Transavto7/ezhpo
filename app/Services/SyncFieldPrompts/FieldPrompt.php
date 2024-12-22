<?php

namespace App\Services\SyncFieldPrompts;

use DateTimeImmutable;

class FieldPrompt
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $field;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $content;
    /**
     * @var string|null
     */
    private $deletedId;
    /**
     * @var DateTimeImmutable|null
     */
    private $deletedAt;
    /**
     * @var DateTimeImmutable|null
     */
    private $createdAt;
    /**
     * @var DateTimeImmutable|null
     */
    private $updatedAt;
    /**
     * @var int
     */
    private $sort;

    /**
     * @param int $id
     * @param string $type
     * @param string $field
     * @param string $name
     * @param string|null $content
     * @param string|null $deletedId
     * @param DateTimeImmutable|null $deletedAt
     * @param DateTimeImmutable|null $createdAt
     * @param DateTimeImmutable|null $updatedAt
     * @param int $sort
     */
    public function __construct(
        int                $id,
        string             $type,
        string             $field,
        string             $name,
        ?string            $content,
        ?string            $deletedId,
        ?DateTimeImmutable $deletedAt,
        ?DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
        int                $sort
    )
    {
        $this->id = $id;
        $this->type = $type;
        $this->field = $field;
        $this->name = $name;
        $this->content = $content;
        $this->deletedId = $deletedId;
        $this->deletedAt = $deletedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->sort = $sort;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getDeletedId(): ?string
    {
        return $this->deletedId;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getSort(): int
    {
        return $this->sort;
    }
}
