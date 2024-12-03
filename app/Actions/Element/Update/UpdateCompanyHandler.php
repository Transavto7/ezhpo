<?php

namespace App\Actions\Element\Update;

use App\Actions\Element\SyncFieldsHandler;
use App\Company;
use App\Exceptions\EntityAlreadyExistException;
use App\Services\CompanyReqsChecker\CompanyReqsCheckerInterface;
use App\ValueObjects\CompanyReqs;
use App\ValueObjects\Phone;
use Exception;

class UpdateCompanyHandler extends UpdateElementHandler
{
    /**
     * @var SyncFieldsHandler
     */
    private $syncFieldsHandler;

    public function __construct(string $type)
    {
        parent::__construct($type);

        $this->syncFieldsHandler = new SyncFieldsHandler();
    }

    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->validateData($id);
        $this->findElement($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->syncCompanyProducts();
        $this->resetEmptyFields();
        $this->updateReqsValidated();
        $this->element->save();
    }

    /**
     * @throws Exception
     */
    protected function validateData($id)
    {
        $this->validateReqs($id);
        $this->validatePhoneNumber();
    }

    /**
     * @throws Exception
     */
    protected function validateReqs($id)
    {
        $inn = trim($this->data['inn'] ?? '');
        $kpp = trim($this->data['kpp'] ?? '');

        $companyReqs = new CompanyReqs($inn, $kpp);

        if ($companyReqs->isValidFormat()) {
            /** @var CompanyReqsCheckerInterface $companyReqsChecker */
            $companyReqsChecker = resolve(CompanyReqsCheckerInterface::class);
            if ($companyReqsChecker->check($companyReqs)) {
                $this->data['reqs_validated'] = true;
            } else {
                throw new Exception('Невалидные реквизиты компании');
            }
        }

        $query = Company::query()
            ->withTrashed()
            ->where('id', '!=', $id)
            ->where('inn', $inn);

        if ($companyReqs->isOrganizationInnFormat()) {
            $query->where('kpp', $kpp);
        }

        $duplicateElement = $query->first();
        if ($duplicateElement) {
            throw new EntityAlreadyExistException('Найден дубликат компании по ИНН (+КПП)');
        }
    }

    protected function validatePhoneNumber()
    {
        if (!array_key_exists('where_call', $this->data)) {
            return;
        }

        $phoneNumber = $this->data['where_call'];

        if (empty($phoneNumber)) {
            return;
        }

        $phone = new Phone($phoneNumber);

        if (!$phone->isValid()) {
            throw new Exception('Некорректный формат телефона, введите телефон в формате 7ХХХХХХХХХХ');
        }

        $this->data['where_call'] = $phone->getSanitized();
    }

    protected function updateReqsValidated()
    {
        if ($this->element->getAttribute('reqs_validated') === true) {
            return;
        }

        $inn = $this->element->getAttribute('inn');
        $innLength = strlen($inn ?? '');
        $isPersonInn = $innLength === 12;
        $isOrganizationInn = $innLength === 10;
        $kpp = $this->element->getAttribute('kpp');

        if (!$isPersonInn && !$isOrganizationInn) {
            return;
        }

        if ($isOrganizationInn && (strlen($kpp ?? '') !== 9)) {
            return;
        }

        $this->element->setAttribute('reqs_validated', true);
    }

    protected function syncCompanyProducts()
    {
        $element = $this->element;

        $element->required_type_briefing = ($data['required_type_briefing'] ?? null) == 'on';

        if (isset($element->products_id)) {
            $modelsToSync = ['Driver', 'Car'];

            foreach ($modelsToSync as $modelToSync) {
                $this->syncFieldsHandler->handle([
                    'model'          => $modelToSync,
                    'fieldFind'      => 'company_id',
                    'fieldFindId'    => $element->id,
                    'fieldSync'      => 'products_id',
                    'fieldSyncValue' => $element->products_id
                ]);
            }
        }

        $this->element = $element;
    }
}
