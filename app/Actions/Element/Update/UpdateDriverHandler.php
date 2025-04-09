<?php

namespace App\Actions\Element\Update;

use App\Company;
use App\Exceptions\DriverWithSameNameAlreadyExist;
use App\Services\FindSimilarElement\Repositories\DriverRepository;
use Exception;

class UpdateDriverHandler extends UpdateElementHandler
{
    /**
     * @var DriverRepository
     */
    private $repository;

    public function __construct(string $type)
    {
        parent::__construct($type);

        $this->repository = new DriverRepository();
    }

    /**
     * @throws Exception
     */
    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->validateData();
        $this->validatePhone($data['phone']);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->syncCompanyFields();
        $this->resetEmptyFields();
        $this->element->save();
        $this->attachContracts();
    }

    private function validatePhone(string $phone = null)
    {
        if ($phone !== null && preg_match("/^[+\-\d\s]+$/", $phone) !== 1) {
            throw new Exception('Ошибка! Неправильный формат телефона: '.$phone);
        }
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

        $this->validateFio();
    }

    private function validateFio()
    {
        if (! isset($this->data['fio'])) {
            return;
        }

        $existItem = $this->repository->findByName($this->data['fio'], $this->data['company_id'], $this->element->id);

        if ($existItem) {
            throw new DriverWithSameNameAlreadyExist();
        }
    }
}
