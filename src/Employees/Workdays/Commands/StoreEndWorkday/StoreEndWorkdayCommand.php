<?php
declare(strict_types=1);

namespace Src\Employees\Workdays\Commands\StoreEndWorkday;

use DateTimeImmutable;

final class StoreEndWorkdayCommand
{
    /**
     * @var string
     */
    private $employeeId;

    /**
     * @var int
     */
    private $pointId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @param string $employeeId
     * @param int $pointId
     * @param DateTimeImmutable $date
     */
    public function __construct(string $employeeId, int $pointId, DateTimeImmutable $date)
    {
        $this->employeeId = $employeeId;
        $this->pointId = $pointId;
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    /**
     * @return int
     */
    public function getPointId(): int
    {
        return $this->pointId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
