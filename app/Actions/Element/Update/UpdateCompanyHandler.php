<?php

namespace App\Actions\Element\Update;

use App\Actions\Element\SyncFieldsHandler;
use App\Company;
use App\Enums\OneCSyncStatusEnum;
use App\Exceptions\EntityAlreadyExistException;
use App\Exceptions\WrongCompanyReqsException;
use App\Services\CompanyReqsChecker\CompanyRepository;
use App\Services\CompanyReqsChecker\CompanyReqsCheckerInterface;
use App\Services\OneC\CompanySync\CompanySyncServiceInterface;
use App\ValueObjects\CompanyReqs;
use App\ValueObjects\Phone;
use Exception;
use Throwable;

class UpdateCompanyHandler extends UpdateElementHandler
{
    /**
     * @var SyncFieldsHandler
     */
    private $syncFieldsHandler;

    /**
     * @var CompanySyncServiceInterface
     */
    private $companySyncService;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(string $type)
    {
        parent::__construct($type);

        $this->companySyncService = resolve(CompanySyncServiceInterface::class);

        $this->syncFieldsHandler = new SyncFieldsHandler();

        $this->companyRepository = new CompanyRepository();
    }

    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->validateData($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->syncCompanyProducts();
        $this->resetEmptyFields();
        $this->syncWith1CIfNeed();
        $this->element->save();
    }

    /**
     * @throws Exception
     */
    protected function validateData($id)
    {
        $existItem = Company::query()
            ->where('id', '!=', $id)
            ->where('name', trim($this->data['name'] ?? ''))
            ->first();
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат по названию компании');
        }

        $this->validateReqs($id, $this->element);
        $this->validatePhoneNumber();
    }

    /**
     * @throws Exception
     */
    protected function validateReqs($id, Company $company)
    {
        if (!(isset($this->data['inn']))) {
            return;
        }

        if ($company->getAttribute('reqs_validated')) {
            throw new Exception('Попытка смены корректных реквизитов компании!');
        }

        $companyReqs = new CompanyReqs($this->data['inn'] ?? '', $this->data['kpp'] ?? '', $this->data['ogrn'] ?? '');
        //TODO: проверять отдельно ЮЛ, СЗ и ФЛ
        if ($companyReqs->isValidFormat()) {
            /** @var CompanyReqsCheckerInterface $companyReqsChecker */
            $companyReqsChecker = resolve(CompanyReqsCheckerInterface::class);
            if ($companyReqsChecker->check($companyReqs)) {
                $this->data['reqs_validated'] = true;
            } else {
                throw new WrongCompanyReqsException();
            }
        }

        $existItem = $this->companyRepository->findByReqs($companyReqs, $id);
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат компании по ИНН (+КПП)');
        }
    }

    /**
     * @throws Exception
     */
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

    protected function syncWith1CIfNeed()
    {
        if (!$this->element->getAttribute('reqs_validated')) {
            return;
        }

        if ($this->element->getAttribute('one_c_synced') === OneCSyncStatusEnum::NON_CREATED) {
            $this->createCompanyInOneC();

            return;
        }

        $changedProperties = array_keys($this->element->getDirty());
        $propertiesToUpdateInOneC = [
            'name',
            'official_name'
        ];
        $needUpdate = count(array_intersect($propertiesToUpdateInOneC, $changedProperties)) > 0;
        if ($needUpdate) {
            $this->element->setAttribute('one_c_synced', OneCSyncStatusEnum::NEED_UPDATE);

            $this->updateCompanyInOneC();
        }
    }

    protected function createCompanyInOneC()
    {
        try {
            $this->companySyncService->create($this->element);

            $this->element->setAttribute('one_c_synced', OneCSyncStatusEnum::SYNCED);
        } catch (Throwable $exception) {

        }
    }

    protected function updateCompanyInOneC()
    {
        try {
            $this->companySyncService->update($this->element);

            $this->element->setAttribute('one_c_synced', OneCSyncStatusEnum::SYNCED);
        } catch (Throwable $exception) {

        }
    }
}
