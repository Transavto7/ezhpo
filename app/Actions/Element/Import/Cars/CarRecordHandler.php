<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars;

use App\Actions\Element\Import\Cars\ImportObjects\ErrorCar;
use App\Actions\Element\Import\Cars\ImportObjects\ImportedCar;
use App\Actions\Element\Import\Core\ElementRecordHandler;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\UserActionTypesEnum;
use App\Events\UserActions\ClientDocImport;
use App\Models\Contract;
use App\Services\HashIdGenerator\DefaultHashIdValidators;
use App\Services\HashIdGenerator\HashedType;
use App\Services\HashIdGenerator\HashIdGenerator;
use Auth;

final class CarRecordHandler extends ElementRecordHandler
{
    /**
     * @throws \Exception
     */
    public function handle(ImportedCar $importedCar): bool
    {
        $company = $this->fetchCompany($importedCar->getCompanyInn());
        if (!$company) {
            $this->errors[] = ErrorCar::fromImportedCar(
                $importedCar,
                sprintf('Компания с ИНН %n не найдена!', $importedCar->getCompanyInn())
            );

            return false;
        }

        $driver = $this->fetchCar($importedCar, $company);
        if ($driver) {
            $this->errors[] = ErrorCar::fromImportedCar(
                $importedCar,
                'ТС с такими данными уже существует!'
            );

            return false;
        }

        $this->createCar($importedCar, $company);

        return true;
    }

    private function fetchCompany(string $companyInn): ?Company
    {
        /** @var Company|null $company */
        $company = Company::query()->where('inn', $companyInn)->first();
        return $company;
    }

    private function fetchCar(ImportedCar $importedCar, Company $company): ?Car
    {
        /** @var Car|null $driver */
        $driver = Car::query()
            ->where('company_id', $company->id)
            ->where('gos_number', $importedCar->getNumber())
            ->first();
        return $driver;
    }

    /**
     * @throws \Exception
     */
    private function createCar(ImportedCar $importedCar, Company $company)
    {
        event(new ClientDocImport(Auth::user(), UserActionTypesEnum::CAR_IMPORT));

        $hashId = HashIdGenerator::generateWithType(DefaultHashIdValidators::car(), HashedType::car());
        $productsId = $company->getAttribute('products_id');

        /** @var Car $car */
        $car = Car::query()->create(
            array_merge(
                ['hash_id' => $hashId, 'products_id' => $productsId, 'company_id' => $company->id],
                $importedCar->toArray()
            )
        );

        /** @var Contract|null $contract */
        $contract = Contract::query()
            ->where('company_id', $company->id)
            ->where('main_for_company', 1)
            ->first();

        if ($contract) {
            $contract->cars()->attach($car->id);
        }
    }
}
