<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Drivers;

use Illuminate\Support\Facades\DB;

final class ExportDriverRepository
{
    public function getExportDrivers(): array
    {
        $drivers = DB::table('drivers')
            ->select('drivers.fio', 'companies.name as company_name', 'users.hash_id as user_id')
            ->leftJoin('companies', 'companies.id', '=', 'drivers.company_id')
            ->leftJoin('users', 'users.login', '=', 'drivers.hash_id')
            ->get();

        return $drivers->map(function ($driver) {
            return new ExportDriver(
                $driver->company_name,
                $driver->fio,
                (int)$driver->user_id
            );
        })->toArray();
    }
}
