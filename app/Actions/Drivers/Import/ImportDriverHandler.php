<?php
declare(strict_types=1);

namespace App\Actions\Drivers\Import;

use App\Actions\Drivers\Import\Exceptions\FoundedNotValidDrivers;
use App\Actions\Drivers\Import\ImportObjects\ErrorDriver;
use App\Actions\Drivers\Import\ImportObjects\ImportedDriver;
use App\Actions\Drivers\Import\Reader\ExcelReader;
use App\Company;
use App\Driver;

final class ImportDriverHandler
{

    /**
     * @throws FoundedNotValidDrivers
     * @throws \Exception
     */
    public function handle(ImportDriverAction $action)
    {
        $reader = new ExcelReader($action->getFilePath());
        /** @var ErrorDriver[] $errors */
        $errors = [];

        foreach ($reader->importingDrivers() as $importedDriver) {
            $company = $this->fetchCompany($importedDriver->getCompanyInn());

            if (! $company) {
                $errors[] = ErrorDriver::fromImportedDriver(
                    $importedDriver,
                    sprintf('Компания с ИНН %n не найдена!', $importedDriver->getCompanyInn())
                );

                continue;
            }

            $driver = $this->fetchDriver($importedDriver, $company);

            if (! $driver) {
                $errors[] = ErrorDriver::fromImportedDriver(
                    $importedDriver,
                    'Водитель с такими данными уже существует!'
                );

                continue;
            }


            $this->createDriver($importedDriver, $company);
        }

        if ($reader->hasErrors()) {
            $errors = array_merge($errors, $reader->getErrorDrivers());
        }

        if (count($errors) !== 0) {
            // TODO write error excel
            throw new FoundedNotValidDrivers();
        }
    }

    private function fetchCompany(int $companyInn): ?Company
    {
        /** @var Company|null $company */
        $company = Company::query()->where('inn', $companyInn)->first();
        return $company;
    }

    private function fetchDriver(ImportedDriver $importedDriver, Company $company): ?Driver
    {
        /** @var Driver|null $driver */
        $driver = Driver::query()
            ->where('company_id', $company->id)
            ->where('fio', $importedDriver->getFullName())
            ->whereDate('year_birthday', $importedDriver->getBirthday())
            ->first();
        return $driver;
    }

    private function createDriver(ImportedDriver $importedDriver, Company $company)
    {
        // TODO
    }
}
