<?php
declare(strict_types=1);

namespace Src\Employees\Workdays\Commands\StoreWorkdayManually;

use DateTimeImmutable;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;

final class StoreWorkdayCommand
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
     * @var WorkdayEventTypeEnum
     */
    private $type;

    /**
     * @param string $employeeId
     * @param int $pointId
     * @param DateTimeImmutable $date
     * @param string $type
     */
    public function __construct(string $employeeId, int $pointId, DateTimeImmutable $date, string $type)
    {
        $this->employeeId = $employeeId;
        $this->pointId = $pointId;
        $this->date = $date;
        $this->type = WorkdayEventTypeEnum::create($type);
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

    /**
     * @return WorkdayEventTypeEnum
     */
    public function getType(): WorkdayEventTypeEnum
    {
        return $this->type;
    }
}
