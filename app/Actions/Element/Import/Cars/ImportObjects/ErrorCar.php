<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars\ImportObjects;

use App\Actions\Element\Import\Core\ErrorObject;

final class ErrorCar implements ErrorObject
{
    /** @var string|null */
    private $companyName;

    /** @var string|null */
    private $companyInn;

    /** @var string|null */
    private $number;

    /** @var string|null */
    private $markAndModel;

    /** @var string|null */
    private $category;

    /** @var string|null */
    private $trailer;

    /** @var string|null */
    private $dateTechView;

    /** @var string|null */
    private $dateOsago;

    /** @var string|null */
    private $dateSkzi;

    /** @var string */
    private $description;

    /**
     * @param string|null $companyName
     * @param string|null $companyInn
     * @param string|null $number
     * @param string|null $markAndModel
     * @param string|null $category
     * @param string|null $trailer
     * @param string|null $dateTechView
     * @param string|null $dateOsago
     * @param string|null $dateSkzi
     * @param string $description
     */
    public function __construct(
        $companyName,
        $companyInn,
        $number,
        $markAndModel,
        $category,
        $trailer,
        $dateTechView,
        $dateOsago,
        $dateSkzi,
        string  $description
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
        $this->description = $description;
    }


    public function toArray(): array
    {
        return [
            $this->companyName,
            $this->companyInn,
            $this->number,
            $this->markAndModel,
            $this->category,
            $this->trailer,
            $this->dateTechView,
            $this->dateOsago,
            $this->dateSkzi,
            $this->description
        ];
    }

    public static function fromImportedCar(ImportedCar $car, string $reason): self
    {
        return new self(
            $car->getCompanyName(),
            $car->getCompanyInn(),
            $car->getNumber(),
            $car->getMarkAndModel(),
            $car->getCategory(),
            $car->getTrailer(),
            $car->getDateTechView(),
            $car->getDateOsago(),
            $car->getDateSkzi(),
            $reason
        );
    }
}
