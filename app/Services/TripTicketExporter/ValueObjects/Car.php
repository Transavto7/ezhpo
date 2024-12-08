<?php

namespace App\Services\TripTicketExporter\ValueObjects;

use App\Car as CarModel;

class Car
{
    /**
     * @var string
     */
    private $gosNumber;
    /**
     * @var string
     */
    private $markModel;
    /**
     * @var string
     */
    private $typeAuto;

    /**
     * @param string $gosNumber
     * @param string $markModel
     * @param string $typeAuto
     */
    private function __construct(string $gosNumber, string $markModel, string $typeAuto)
    {
        $this->gosNumber = $gosNumber;
        $this->markModel = $markModel;
        $this->typeAuto = $typeAuto;
    }

    public static function fromEloquent(CarModel $car): self
    {
        return new self($car->gos_number, $car->mark_model, $car->type_auto);
    }

    public function getGosNumber(): string
    {
        return $this->gosNumber;
    }

    public function getMarkModel(): string
    {
        return $this->markModel;
    }

    public function getTypeAuto(): string
    {
        return $this->typeAuto;
    }
}
