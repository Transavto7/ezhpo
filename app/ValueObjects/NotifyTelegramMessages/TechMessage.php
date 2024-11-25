<?php

namespace App\ValueObjects\NotifyTelegramMessages;

use DateTimeImmutable;

class TechMessage implements MessageInterface
{
    /**
     * @var string
     */
    private $responsiblePerson;

    /**
     * @var array
     */
    private $reasons;

    /**
     * @var int
     */
    private $formId;

    /**
     * @var string
     */
    private $companyHashId;

    /**
     * @var string
     */
    private $companyName;

    /**
     * @var string
     */
    private $driverFullName;

    /**
     * @var string|null
     */
    private $carNumber;

    /**
     * @var DateTimeImmutable
     */
    private $formDate;

    /**
     * @var string
     */
    private $pointName;

    /**
     * @var string
     */
    private $medicFullName;

    /**
     * @param string $responsiblePerson
     * @param array $reasons
     * @param int $formId
     * @param string $companyId
     * @param string $companyName
     * @param string $driverFullName
     * @param string $carNumber
     * @param DateTimeImmutable $formDate
     * @param string $pointName
     * @param string $medicFullName
     */
    public function __construct(
        string $responsiblePerson,
        array $reasons,
        int $formId,
        string $companyId,
        string $companyName,
        string $driverFullName,
        string $carNumber,
        DateTimeImmutable $formDate,
        string $pointName,
        string $medicFullName
    )
    {
        $this->responsiblePerson = $responsiblePerson;
        $this->reasons = $reasons;
        $this->formId = $formId;
        $this->companyHashId = $companyId;
        $this->companyName = $companyName;
        $this->driverFullName = $driverFullName;
        $this->carNumber = $carNumber;
        $this->formDate = $formDate;
        $this->pointName = $pointName;
        $this->medicFullName = $medicFullName;
    }

    public function __toString(): string
    {
        $date = $this->formDate->format("Y-m-d H:i:s");

        $lines = [
            "*Ответственный за компанию — $this->responsiblePerson.*",
            "Поступил техосмотр с отстранением по причине: _{$this->reasonsToString()}_.",
            "ID осмотра — $this->formId.",
            "ID компании — $this->companyHashId.",
            "Название компании — $this->companyName.",
            "ФИО водителя — $this->driverFullName.",
            "Госномер ТС — $this->carNumber.",
            "Время осмотра — $date.",
            "Пункт выпуска — $this->pointName.",
            "Сотрудник — $this->medicFullName.",
        ];

        return implode("\n", $lines);
    }

    private function reasonsToString(): string
    {
        return implode(', ', $this->reasons);
    }
}
