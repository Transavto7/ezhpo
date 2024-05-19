<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars\ImportObjects;

use Carbon\Carbon;

final class ImportedCar
{
    /** @var string|null */
    private $companyName;

    /** @var int */
    private $companyInn;

    /** @var string */
    private $number;

    /** @var string|null */
    private $markAndModel;

    /** @var string|null */
    private $category;

    /** @var string|null */
    private $trailer;

    /** @var Carbon|null */
    private $dateTechView;

    /** @var Carbon|null */
    private $dateOsago;

    /** @var Carbon|null */
    private $dateSkzi;

    /**
     * @param string|null $companyName
     * @param int $companyInn
     * @param string $number
     * @param string|null $markAndModel
     * @param string|null $category
     * @param string|null $trailer
     * @param Carbon|null $dateTechView
     * @param Carbon|null $dateOsago
     * @param Carbon|null $dateSkzi
     */
    public function __construct(
        ?string $companyName,
        int     $companyInn,
        string  $number,
        ?string $markAndModel,
        ?string $category,
        ?string $trailer,
        ?Carbon $dateTechView,
        ?Carbon $dateOsago,
        ?Carbon $dateSkzi
    )
    {
        $this->companyName = $companyName;
        $this->companyInn = $companyInn;
        $this->number = $number;
        $this->markAndModel = $markAndModel;
        $this->category = $category;
        $this->trailer = $trailer;
        $this->dateTechView = $dateTechView;
        $this->dateOsago = $dateOsago;
        $this->dateSkzi = $dateSkzi;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function getCompanyInn(): int
    {
        return $this->companyInn;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getMarkAndModel(): ?string
    {
        return $this->markAndModel;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function getTrailer(): ?string
    {
        return $this->trailer;
    }

    public function getDateTechView(): ?string
    {
        return $this->dateTechView ? $this->dateTechView->format('Y-m-d') : null;
    }

    public function getDateOsago(): ?string
    {
        return $this->dateOsago ? $this->dateOsago->format('Y-m-d') : null;
    }

    public function getDateSkzi(): ?string
    {
        return $this->dateSkzi ? $this->dateSkzi->format('Y-m-d') : null;
    }

    public function toArray(): array
    {
        return [
            'gos_number' => mb_strtoupper($this->number),
            'mark_model' => $this->markAndModel,
            'type_auto' => $this->category,
            'trailer' => $this->trailer,
            'date_techview' => $this->dateTechView ? $this->dateTechView->format('Y-m-d') : null,
            'date_osago' => $this->dateOsago ? $this->dateOsago->format('Y-m-d') : null,
            'time_skzi' => $this->dateSkzi ? $this->dateSkzi->format('Y-m-d') : null,
        ];
    }
}
