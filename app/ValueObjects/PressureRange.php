<?php

namespace App\ValueObjects;

use App\Exceptions\PressureRangeParseFailedException;
use Carbon\Carbon;

final class PressureRange
{
    const RANGE_SEPARATOR = '/';

    /**
     * @var int|null
     */
    private $down;
    /**
     * @var int|null
     */
    private $up;

    /**
     * @param int|null $down
     * @param int|null $up
     */
    private function __construct(?int $down, ?int $up)
    {
        $this->down = $down;
        $this->up = $up;
    }


    /**
     * @param string $value
     * @return PressureRange
     * @throws PressureRangeParseFailedException()
     */
    public static function from(string $value): PressureRange
    {
        $exploded = explode(self::RANGE_SEPARATOR, $value);

        if (count($exploded) !== 2) {
            throw new PressureRangeParseFailedException("Invalid pressure range value '$value' format");
        }

        $down = null;
        $up = null;

        if ($exploded[0] !== '') {
            $down = intval($exploded[0]);
        }

        if ($exploded[0] !== '') {
            $up = intval($exploded[1]);
        }

        return new self($down, $up);
    }

    public function getDown(): ?int
    {
        return $this->down;
    }

    public function getUp(): ?int
    {
        return $this->up;
    }
}
