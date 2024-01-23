<?php

namespace App\Dto;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;

class NotifyParams
{
    /**
     * @var Company|null
     */
    private $company;

    /**
     * @var Driver|null
     */
    private $driver;

    /**
     * @var Car|null
     */
    private $car;

    /**
     * @var Anketa|null
     */
    private $form;

    /**
     * @param Company|null $company
     * @param Driver|null $driver
     * @param Car|null $car
     * @param Anketa|null $form
     */
    public function __construct(?Company $company, ?Driver $driver, ?Car $car, ?Anketa $form)
    {
        $this->company = $company;
        $this->driver = $driver;
        $this->car = $car;
        $this->form = $form;
    }

    /**
     * @return Company|null
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @return Driver|null
     */
    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    /**
     * @return Car|null
     */
    public function getCar(): ?Car
    {
        return $this->car;
    }

    /**
     * @return Anketa|null
     */
    public function getForm(): ?Anketa
    {
        return $this->form;
    }
}
