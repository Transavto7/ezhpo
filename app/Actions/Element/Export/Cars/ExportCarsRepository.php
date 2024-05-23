<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Cars;

use DomainException;
use Illuminate\Support\Facades\DB;

final class ExportCarsRepository
{
    /**
     * @param bool $exportAll
     * @param int|null $companyId
     * @return array
     * @throws DomainException
     */
    public function getExportCars(bool $exportAll, ?int $companyId = null): array
    {
        $carsBuilder = DB::table('cars')
            ->select(
                'companies.name as company_name',
                'cars.gos_number as number',
                'cars.mark_model',
                'cars.type_auto as category',
                'cars.hash_id'
            )
            ->leftJoin('companies', 'companies.id', '=', 'cars.company_id');

        if (! $exportAll) {
            if ($companyId === null) {
                throw new DomainException('Пользователь без привелегий не может экспортировать данные без привязки к компании!');
            }

            $carsBuilder->where('company_id', $companyId);
        }

        return $carsBuilder->get()->map(function ($car) {
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
