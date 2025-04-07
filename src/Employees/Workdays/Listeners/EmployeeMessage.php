<?php

namespace Src\Employees\Workdays\Listeners;

use App\ValueObjects\NotifyTelegramMessages\MessageInterface;
use DateTimeImmutable;

class EmployeeMessage implements MessageInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $employeeFullName;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $pointName;

    /**
     * @param int $id
     * @param string $employeeFullName
     * @param DateTimeImmutable $date
     * @param string $pointName
     */
    public function __construct(
        int $id,
        string $employeeFullName,
        DateTimeImmutable $date,
        string $pointName
    ) {
        $this->id = $id;
        $this->employeeFullName = $employeeFullName;
        $this->date = $date;
        $this->pointName = $pointName;
    }

    public function __toString(): string
    {
        $date = $this->date->format('Y-m-d H:i:s');

        $lines = [
            'Поступил осмотр рабочей смены с отстранением.',
            "ID осмотра — $this->id.",
            "Сотрудник — $this->employeeFullName.",
            "Время осмотра — $date.",
            "Пункт выпуска — $this->pointName.",
        ];

        return implode("\n", $lines);
    }
}
