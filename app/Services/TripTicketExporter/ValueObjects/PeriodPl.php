<?php

namespace App\Services\TripTicketExporter\ValueObjects;

class PeriodPl
{
    const months = [
        'январь' => 1,
        'февраль' => 2,
        'март' => 3,
        'апрель' => 4,
        'май' => 5,
        'июнь' => 6,
        'июль' => 7,
        'август' => 8,
        'сентябрь' => 9,
        'октябрь' => 10,
        'ноябрь' => 11,
        'декабрь' => 12
    ];

    /**
     * @var int
     */
    private $year;
    /**
     * @var int
     */
    private $month;

    /**
     * @param ?int $year
     * @param ?int $month
     */
    private function __construct(?int $year, ?int $month)
    {
        $this->year = $year;
        $this->month = $month;
    }

    public static function fromString(string $value): self
    {
        $result = self::extractYearMonth($value);

        if ($result !== null) {
            return new self($result['year'], $result['month']);
        }

        $searched = mb_strtolower($value);
        if (in_array($searched, self::months)) {
            return new self(null, self::months[$searched]);
        }

        return new self(null, null);
    }

    /**
     * @param $input
     * @return array|null
     */
    private function extractYearMonth($input): ?array
    {
        $pattern = '/^(\d{4})-(0[1-9]|1[0-2])$/';
        $matches = [];

        if (preg_match($pattern, $input, $matches)) {
            return [
                'year' => $matches[1],
                'month' => $matches[2],
            ];
        }

        return null;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }
}
