<?php

namespace App\ValueObjects;

use DateTimeImmutable;

class NotifyTelegramMessage
{
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
    public function __construct(int $formId, string $companyId, string $companyName, string $driverFullName, DateTimeImmutable $formDate, string $pointName, string $medicFullName, string $protokolUrl, string $closingUrl)
    {
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
            "Поступил осмотр с отстранением по алкоголю.",
            "ID осмотра — $this->formId.",
            "ID компании — $this->companyHashId.",
            "Название компании — $this->companyName.",
            "ФИО водителя - $this->driverFullName.",
            "Время осмотра — $date.",
            "Пункт выпуска — $this->pointName.",
            "Медицинский сотрудник  — $this->medicFullName.",
            "Документы:",
            "- заключение $this->closingUrl;",
            "- протокол $this->protokolUrl."
        ];

        return implode("\n", $lines);
    }
}
