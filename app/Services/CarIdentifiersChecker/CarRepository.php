<?php

namespace App\Services\CarIdentifiersChecker;

use App\Car;

class CarRepository
{
    public function findByVin(string $vin, int $companyId, $excludeId = null): ?Car
    {
        /** @var Car|null $car */
        $car = Car::query()
            ->where('company_id', $companyId)
            ->where('vin', $vin)
            ->when($excludeId, function ($builder) use ($excludeId) {
                $builder->where('id', '<>', $excludeId);
            })
            ->first();

        return $car;
    }

    public function findByGosNumber(string $gosNumber, int $companyId, $excludeId = null): ?Car
    {
        /** @var Car|null $car */
        $car = Car::query()
            ->where('company_id', $companyId)
            ->where('gos_number', $gosNumber)
            ->when($excludeId, function ($builder) use ($excludeId) {
                $builder->where('id', '<>', $excludeId);
            })
            ->first();

        return $car;
    }
}
