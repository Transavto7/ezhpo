<?php
declare(strict_types=1);

namespace App\Enums;

final class ReportStatus
{
    const CREATED = 'created';
    const PROCESSING = 'processing';
    const READY = 'ready';
    const DELETED = 'deleted';
    const ERROR = 'error';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public static function fromString(string $value): self
    {
        switch ($value) {
            case self::CREATED:
                return self::created();
            case self::PROCESSING:
                return self::processing();
            case self::READY:
                return self::ready();
            case self::DELETED:
                return self::deleted();
            case self::ERROR:
                return self::error();
            default:
                throw new \DomainException('Unknown report status: ' . $value);
        }
    }

    public static function created(): self
    {
        return new self(self::CREATED);
    }

    public static function processing(): self
    {
        return new self(self::PROCESSING);
    }

    public static function ready(): self
    {
        return new self(self::READY);
    }

    public static function deleted(): self
    {
        return new self(self::DELETED);
    }

    public static function error(): self
    {
        return new self(self::ERROR);
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
