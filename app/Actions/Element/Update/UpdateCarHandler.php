<?php

namespace App\Actions\Element\Update;

use App\Company;
use App\Exceptions\CarWithSameGosNumberAlreadyExist;
use App\Exceptions\CarWithSameVinAlreadyExist;
use App\Exceptions\WrongCarGosNumberException;
use App\Exceptions\WrongCarVinException;
use App\Services\FindSimilarElement\Repositories\CarRepository;
use App\ValueObjects\GosNumber;
use App\ValueObjects\Vin;
use Exception;

class UpdateCarHandler extends UpdateElementHandler
{
    /**
     * @var CarRepository
     */
    private $carRepository;

    /**
     * @throws Exception
     */
    public function __construct(string $type)
    {
        $this->carRepository = new CarRepository();

        parent::__construct($type);
    }

    /**
     * @throws Exception
     */
    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->validateData();
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->syncCompanyFields();
        $this->resetEmptyFields();
        $this->element->save();
        $this->attachContracts();
    }

    /**
     * @throws Exception
     */
    protected function validateData()
    {
        $company = Company::withTrashed()->find($this->data['company_id']);
        if (!$company) {
            throw new Exception('Компания не найдена');
        }

        $this->validateCarGosNumber();
        $this->validateVin();
    }

    private function validateCarGosNumber()
    {
        if (! isset($this->data['gos_number'])) {
            return;
        }

        $gosNumber = new GosNumber($this->data['gos_number'], $this->data['type_auto']);

        if (!$gosNumber->isValid()) {
            throw new WrongCarGosNumberException();
        }

        $this->data['gos_number'] = $gosNumber->getSanitized();
        $this->data['gos_number_details'] = json_encode($gosNumber->getDetails());

        $existItemByGosNumber = $this->carRepository->findByGosNumber($gosNumber->getSanitized(), $this->data['company_id'], $this->element->id);
        if ($existItemByGosNumber) {
            throw new CarWithSameGosNumberAlreadyExist();
        }
    }

    /**
     * @throws CarWithSameVinAlreadyExist
     * @throws Exception
     */
    private function validateVin()
    {
        if (!isset($this->data['vin'])) {
            return;
        }

        $vin = new Vin($this->data['vin']);

        if (! $vin->isValid()) {
            throw new WrongCarVinException();
        }

        $this->data['vin'] = $vin->getSanitized();

        $existItemByVin = $this->carRepository->findByVin($vin->getSanitized(), $this->data['company_id'], $this->element->id);
        if ($existItemByVin) {
            throw new CarWithSameVinAlreadyExist();
        }
    }
}
