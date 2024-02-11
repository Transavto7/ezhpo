<?php

namespace App\Actions\Element\Update;

use App\Actions\Element\SyncFieldsHandler;
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
        $this->validateData();
        $this->findElement($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->syncCompanyProducts();
        $this->resetEmptyFields();
        $this->element->save();
    }

    /**
     * @throws Exception
     */
    protected function validateData()
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
}
