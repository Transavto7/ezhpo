<?php

namespace Src\Employees\Workdays\Commands\CalcEmployeeSalaries;

class CalcEmployeeSalaryPrice
{
    private $price;
    private $priceCfg;

    public function __construct(int $price, int $priceCfg)
    {
        $this->price = $price;
        $this->priceCfg = $priceCfg;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceCfg(): int
    {
        return $this->priceCfg;
    }

    public function setPriceCfg(int $priceCfg): self
    {
        $this->priceCfg = $priceCfg;

        return $this;
    }
}
