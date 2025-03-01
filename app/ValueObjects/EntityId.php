<?php

namespace App\ValueObjects;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class EntityId
{
    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @param UuidInterface $id
     */
    private function __construct(UuidInterface $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $id
     * @return static
     */
    public static function fromString(string $id): self
    {
        return new static(Uuid::fromString($id));
    }

    /**
     * @return static
     * @throws \Exception
     */
    public static function next()
    {
        return new static(Uuid::uuid4());
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id->toString();
    }

    /**
     * @param static $id
     * @return bool
     */
    public function equalTo(self $id): bool
    {
        return $this->getId() === $id->getId() &&
            get_class($this) === get_class($id);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id->toString();
    }
}
