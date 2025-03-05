<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Actions\SaveHolidays;

final class SaveHolidaysAction
{
    /** @var array */
    private $newHolidays;

    /** @var array */
    private $deleteHolidays;

    /**
     * @param array $newHolidays
     * @param array $deleteHolidays
     */
    public function __construct(array $newHolidays, array $deleteHolidays)
    {
        $this->newHolidays = $newHolidays;
        $this->deleteHolidays = $deleteHolidays;
    }

    public function getNewHolidays(): array
    {
        return $this->newHolidays;
    }

    public function getDeleteHolidays(): array
    {
        return $this->deleteHolidays;
    }
}
