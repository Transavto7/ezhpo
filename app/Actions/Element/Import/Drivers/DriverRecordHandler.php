<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Drivers;

use App\Actions\Element\Import\Core\ElementRecordHandler;
use App\Actions\Element\Import\Drivers\ImportObjects\ErrorDriver;
use App\Actions\Element\Import\Drivers\ImportObjects\ImportedDriver;
use App\Company;
use App\Driver;
use App\Enums\UserActionTypesEnum;
use App\Events\UserActions\ClientDocImport;
use App\GenerateHashIdTrait;
use App\Models\Contract;
use App\Services\BriefingService;
use App\Services\HashIdGenerator\DefaultHashIdValidators;
use App\Services\HashIdGenerator\HashedType;
use App\Services\HashIdGenerator\HashIdGenerator;
use App\Services\UserService;
use Auth;
use Carbon\Carbon;

final class DriverRecordHandler extends ElementRecordHandler
{
    use GenerateHashIdTrait;

    /**
     * @throws \Exception
     */
    public function handle(ImportedDriver $importedDriver): bool
    {
        $company = $this->fetchCompany($importedDriver->getCompanyInn());
        if (!$company) {
            $this->errors[] = ErrorDriver::fromImportedDriver(
                $importedDriver,
                sprintf('Компания с ИНН %n не найдена!', $importedDriver->getCompanyInn())
            );

            return false;
        }

        $driver = $this->fetchDriver($importedDriver, $company);
        if ($driver) {
            $this->errors[] = ErrorDriver::fromImportedDriver(
                $importedDriver,
                'Водитель с такими данными уже существует!'
            );

            return false;
        }

        $this->createDriver($importedDriver, $company);

        return true;
    }

    private function fetchCompany(string $companyInn): ?Company
    {
        /** @var Company|null $company */
        $company = Company::query()->where('inn', '=', $companyInn)->first();
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

    /**
     * @throws \Exception
     */
    private function createDriver(ImportedDriver $importedDriver, Company $company)
    {
        event(new ClientDocImport(Auth::user(), UserActionTypesEnum::DRIVER_IMPORT));

        $hashId = HashIdGenerator::generateWithType(DefaultHashIdValidators::driver(), HashedType::driver());
        $productsId = $company->getAttribute('products_id');

        /** @var Driver $driver */
        $driver = Driver::query()->create(
            array_merge(
                [
                    'hash_id' => $hashId,
                    'products_id' => $productsId,
                    'company_id' => $company->id,
                    'date_of_employment' => Carbon::now()->format('Y-m-d'),
                ],
                $importedDriver->toArray()
            )
        );

        UserService::createUserFromDriver($driver);

        /** @var Contract|null $contract */
        $contract = Contract::query()
            ->where('company_id', $company->id)
            ->where('main_for_company', 1)
            ->first();

        if ($contract) {
            $contract->drivers()->attach($driver->id);
        }

        if ($company->required_type_briefing) {
            BriefingService::createFirstBriefingForDriver($driver, $company);
        }
    }
}
