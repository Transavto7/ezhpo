<?php

namespace App\Services\TripTicketExporter\ViewModels;

final class CarViewModel
{
    /**
     * @var string
     */
    private $id;
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
     * @param string $id
     * @param string $gosNumber
     * @param string $markModel
     * @param string $typeAuto
     */
    public function __construct(
        string $id,
        string $gosNumber,
        string $markModel,
        string $typeAuto
    )
    {
        $this->id = $id;
        $this->gosNumber = $gosNumber;
        $this->markModel = $markModel;
        $this->typeAuto = $typeAuto;
    }

    public function getId(): string
    {
        return $this->id;
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
