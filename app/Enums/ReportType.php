<?php
declare(strict_types=1);

namespace App\Enums;

final class ReportType
{
    const REPORT = 'report';

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
            case self::REPORT:
                return self::report();
            default:
                throw new \DomainException('Unknown report type: ' . $value);
        }
    }

    public static function report(): self
    {
        return new self(self::REPORT);
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
