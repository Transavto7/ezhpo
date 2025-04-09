<?php

namespace App\Actions\Element;

use App\Car;
use App\Company;
use App\Enums\UserActionTypesEnum;
use App\Events\Relations\Attached;
use App\Events\UserActions\ClientAddRecord;
use App\Exceptions\CarWithSameGosNumberAlreadyExist;
use App\Exceptions\EntityAlreadyExistException;
use App\Exceptions\CarWithSameVinAlreadyExist;
use App\Exceptions\WrongCarGosNumberException;
use App\Exceptions\WrongCarVinException;
use App\Models\Contract;
use App\Services\FindSimilarElement\Repositories\CarRepository;
use App\ValueObjects\GosNumber;
use App\ValueObjects\Vin;
use Auth;
use Exception;

class CreateCarHandler extends AbstractCreateElementHandler implements CreateElementHandlerInterface
{
    /**
     * @var CarRepository
     */
    private $carRepository;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->carRepository = new CarRepository();

        parent::__construct('Car');
    }

    /**
     * @throws Exception
     */
    public function handle($data)
    {
        $data = $this->validateData($data);
        $company = Company::withTrashed()->find($data['company_id']);

        $validator = function (int $hashId) {
            if (Car::where('hash_id', $hashId)->first()) {
                return false;
            }

            return true;
        };

        $data['hash_id'] = $this->generateHashId(
            $validator,
            config('app.hash_generator.car.min'),
            config('app.hash_generator.car.max'),
            config('app.hash_generator.car.tries')
        );

        $attributesToSync = ['products_id'];
        foreach ($attributesToSync as $attributeName) {
            $attributeValue = $company->getAttribute($attributeName);

            if (!$attributeValue) {
                continue;
            }

            $data[$attributeName] = $attributeValue;
        }

        $created = $this->createElement($data);

        $user = Auth::user();
        if ($user) {
            event(new ClientAddRecord($user, UserActionTypesEnum::ADD_CAR_VIA_FORM));
        }

        /** @var Contract $contract */
        $contract = Contract::query()
            ->where('company_id', $company->id)
            ->where('main_for_company', 1)
            ->first();

        if ($contract) {
            $contract->cars()->attach($created->id);
            event(new Attached($contract, [$created->id], Car::class));
        }

        return $created;
    }

    /**
     * @throws EntityAlreadyExistException
     * @throws Exception
     */
    protected function validateData(array $data): array
    {
        $company = Company::withTrashed()->find($data['company_id']);
        if (!$company) {
            throw new Exception('Компания не найдена');
        }

        $data = $this->validateCarGosNumber($data);
        $data = $this->validateVin($data);

        return $data;
    }

    /**
     * @throws EntityAlreadyExistException
     * @throws Exception
     */
    private function validateCarGosNumber(array $data): array
    {
        if (empty($data['gos_number'])) {
            throw new Exception('Не заполнен гос.номер Автомобиля');
        }

        $gosNumber = new GosNumber($data['gos_number']);

        if (!$gosNumber->isValid()) {
            throw new WrongCarGosNumberException();
        }

        $data['gos_number'] = $gosNumber->getSanitized();
        $existItemByGosNumber = $this->carRepository->findByGosNumber($gosNumber->getSanitized(), $data['company_id']);
        if ($existItemByGosNumber) {
            throw new CarWithSameGosNumberAlreadyExist();
        }

        return $data;
    }

    /**
     * @throws EntityAlreadyExistException
     * @throws Exception
     */
    private function validateVin(array $data): array
    {
        if (empty($data['vin'])) {
            return $data;
        }

        $vin = new Vin($data['vin']);

        if (!$vin->isValid()) {
            throw new WrongCarVinException();
        }

        $data['vin'] = $vin->getSanitized();
        $existItemByVin = $this->carRepository->findByVin($vin->getSanitized(), $data['company_id']);
        if ($existItemByVin) {
            throw new CarWithSameVinAlreadyExist();
        }

        return $data;
    }
}
