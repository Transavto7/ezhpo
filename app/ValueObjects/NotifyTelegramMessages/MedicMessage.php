<?php

namespace App\ValueObjects\NotifyTelegramMessages;

use DateTimeImmutable;

class MedicMessage implements MessageInterface
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
     * @var string
     */
    private $protokolUrl;

    /**
     * @var string
     */
    private $closingUrl;

    /**
     * @param string $responsiblePerson
     * @param array $reasons
     * @param int $formId
     * @param string $companyId
     * @param string $companyName
     * @param string $driverFullName
     * @param DateTimeImmutable $formDate
     * @param string $pointName
     * @param string $medicFullName
     * @param string $protokolUrl
     * @param string $closingUrl
     */
    public function __construct(
        string $responsiblePerson,
        array $reasons,
        int $formId,
        string $companyId,
        string $companyName,
        string $driverFullName,
        DateTimeImmutable $formDate,
        string $pointName,
        string $medicFullName,
        string $protokolUrl,
        string $closingUrl
    )
    {
        $this->responsiblePerson = $responsiblePerson;
        $this->reasons = $reasons;
        $this->formId = $formId;
        $this->companyHashId = $companyId;
        $this->companyName = $companyName;
        $this->driverFullName = $driverFullName;
        $this->formDate = $formDate;
        $this->pointName = $pointName;
        $this->medicFullName = $medicFullName;
        $this->protokolUrl = $protokolUrl;
        $this->closingUrl = $closingUrl;
    }

    public function __toString(): string
    {
        $date = $this->formDate->format("Y-m-d H:i:s");

        $lines = [
            "*Ответственный за компанию — $this->responsiblePerson.*",
            "Поступил медосмотр с отстранением по причине: _{$this->reasonsToString()}_.",
            "ID осмотра — $this->formId.",
            "ID компании — $this->companyHashId.",
            "Название компании — $this->companyName.",
            "ФИО водителя — $this->driverFullName.",
            "Время осмотра — $date.",
            "Пункт выпуска — $this->pointName.",
            "Сотрудник — $this->medicFullName.",
            "Документы:",
            "— заключение $this->closingUrl;",
            "— протокол $this->protokolUrl."
        ];

        return implode("\n", $lines);
    }

    private function reasonsToString(): string
    {
        return implode(', ', $this->reasons);
    }
}
