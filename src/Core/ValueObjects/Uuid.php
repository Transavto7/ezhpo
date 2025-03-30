<?php

declare(strict_types=1);

namespace Src\Core\ValueObjects;

use Ramsey\Uuid\Uuid as BaseUuid;
use Ramsey\Uuid\UuidInterface;

class Uuid
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
        return new static(BaseUuid::fromString($id));
    }

    /**
     * @return static
     * @throws \Exception
     */
    public static function next(): self
    {
        return new static(BaseUuid::uuid4());
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->id->toString();
    }

    /**
     * @param static $id
     * @return bool
     */
    public function equalTo(self $id): bool
    {
        return $this->value() === $id->value() &&
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
