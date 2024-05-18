<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Cars;

use Illuminate\Support\Facades\DB;

final class ExportCarsRepository
{
    public function getExportCars(): array
    {
        $drivers = DB::table('cars')
            ->select(
                'companies.name as company_name',
                'cars.gos_number as number',
                'cars.mark_model',
                'cars.type_auto as category',
                'cars.hash_id'
            )
            ->leftJoin('companies', 'companies.id', '=', 'cars.company_id')
            ->get();

        return $drivers->map(function ($car) {
            return new ExportCar(
                $car->company_name,
                $car->number,
                $car->mark_model,
                $car->category,
                (int)$car->hash_id,
            );
        })->toArray();
    }
}
