<?php

namespace App\Services\FindSimilarElement\Repositories;

use App\Car;
use Illuminate\Database\Eloquent\Builder;

final class CarRepository
{
    public function findByVin(string $vin, int $companyId, $excludeId = null, bool $withTrashed = false): ?Car
    {
        /** @var Car|null $car */
        $car = Car::query()
            ->when($withTrashed, function (Builder $query) {
                $query->withTrashed();
            })
            ->where('company_id', $companyId)
            ->where('vin', $vin)
            ->when($excludeId, function ($builder) use ($excludeId) {
                $builder->where('id', '<>', $excludeId);
            })
            ->orderBy('deleted_at')
            ->first();

        return $car;
    }

    public function findByGosNumber(string $gosNumber, int $companyId, $excludeId = null, bool $withTrashed = false): ?Car
    {
        /** @var Car|null $car */
        $car = Car::query()
            ->when($withTrashed, function (Builder $query) {
                $query->withTrashed();
            })
            ->where('company_id', $companyId)
            ->where('gos_number', $gosNumber)
            ->when($excludeId, function ($builder) use ($excludeId) {
                $builder->where('id', '<>', $excludeId);
            })
            ->orderBy('deleted_at')
            ->first();

        return $car;
    }
}
