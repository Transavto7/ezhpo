<?php

namespace App\Actions\Element\Update;

use App\Car;
use App\Company;
use App\Exceptions\EntityAlreadyExistException;
use App\Exceptions\WrongCarGosNumberException;
use App\ValueObjects\Vin;
use Exception;

class UpdateCarHandler extends UpdateElementHandler
{
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

    /**
     * @throws EntityAlreadyExistException
     */
    private function validateCarGosNumber()
    {
        if (!isset($this->data['gos_number'])) {
            return;
        }

        $this->data['gos_number'] = preg_replace('/\s+/', '', $this->data['gos_number']);

        $existItem = Car::query()
            ->where('id', '!=', $this->element->id)
            ->where('company_id', $this->data['company_id'])
            ->where('gos_number', $this->data['gos_number'])
            ->first();
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат по гос.номеру Автомобиля');
        }
    }

    /**
     * @throws EntityAlreadyExistException
     * @throws Exception
     */
    private function validateVin()
    {
        if (!isset($this->data['vin'])) {
            return;
        }

        $vin = new Vin($this->data['vin']);
        if (!$vin->isValid()) {
            throw new WrongCarGosNumberException();
        }

        $this->data['vin'] = $vin->getSanitized();

        $existItem = Car::query()
            ->where('id', '!=', $this->element->id)
            ->where('company_id', $this->data['company_id'])
            ->where('vin', $this->data['vin'])
            ->first();
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат по VIN-коду Автомобиля');
        }
    }
}
