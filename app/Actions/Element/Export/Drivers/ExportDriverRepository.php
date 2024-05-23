<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Drivers;

use DomainException;
use Illuminate\Support\Facades\DB;

final class ExportDriverRepository
{
    public function getExportDrivers(bool $exportAll, ?int $companyId = null): array
    {
        $driversBuilder = DB::table('drivers')
            ->select('drivers.fio', 'companies.name as company_name', 'drivers.hash_id as user_id')
            ->leftJoin('companies', 'companies.id', '=', 'drivers.company_id');

        if (! $exportAll) {
            if ($companyId === null) {
                throw new DomainException('Пользователь без привелегий не может экспортировать данные без привязки к компании!');
            }

            $driversBuilder->where('drivers.company_id', $companyId);
        }

        return $driversBuilder->get()->map(function ($driver) {
            return new ExportDriver(
                $driver->company_name,
                $driver->fio,
                (int)$driver->user_id
            );
        })->toArray();
    }
}
