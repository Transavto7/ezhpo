<?php

namespace App\Actions\Element\Update;

use App\Company;
use Exception;
use Illuminate\Support\Facades\Storage;

class UpdateElementHandler implements UpdateElementHandlerInterface
{
    protected $model;

    protected $element;

    /** @var array */
    protected $data;

    /** @var array */
    protected $oldDataModel;
    /**
     * @var array|mixed
     */
    private $files;

    /**
     * @throws Exception
     */
    public function __construct(string $type)
    {
        $model = app("App\\$type");
        if (!$model) {
            throw new Exception('Попытка обновления несуществующего элемента CRM');
        }

        $this->model = $model;
    }

    /**
     * @throws Exception
     */
    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->resetEmptyFields();
        $this->element->save();
    }

    protected function setData($data)
    {
        $this->files = $data['files_from_request'] ?? [];

        unset($data['files_from_request']);

        $this->data = $data;
    }

    protected function wrapNullFieldsToEmptyString()
    {
        $fieldsForNullToStringWrap = [
            'town_id',
            'comment'
        ];

        $data = $this->data;

        foreach ($fieldsForNullToStringWrap as $field) {
            if (!array_key_exists($field, $data)){
                continue;
            }

            if ($data[$field] !== null) {
                continue;
            }

            $data[$field] = "";
        }

        $this->data = $data;
    }

    /**
     * @throws Exception
     */
    protected function findElement($id)
    {
        $element = $this->model->find($id);
        if (!$element) {
            throw new Exception('Элемент с таким ID не найден');
        }

        $this->element = $element;
    }

    protected function updateFiles()
    {
        $data = $this->data;
        $element = $this->element;

        foreach ($this->files as $fileKey => $file) {
            if (isset($data[$fileKey]) && !isset($data[$fileKey.'_base64'])) {
                Storage::disk('public')->delete($element[$fileKey]);

                $element[$fileKey] = Storage::disk('public')->putFile('elements', $file);
            }
        }

        $this->element = $element;
    }

    protected function resetEmptyFields()
    {
        /**
         * Пустые поля обновляем (На самом деле нет)
         */
        foreach ($this->oldDataModel as $oldDataItemKey => $oldDataItemValue) {
            $fieldsToReset = ['note', 'document_bdd'];

            if (isset($this->data[$oldDataItemKey])) {
                continue;
            }

            if (!in_array($oldDataItemKey, $fieldsToReset)) {
                continue;
            }

            $this->element[$oldDataItemKey] = '';
        }
    }

    protected function updateFields()
    {
        $element = $this->element;
        $data = $this->data;
        $oldDataModel = [];

        foreach ($data as $key => $value) {
            $oldDataModel[$key] = $element[$key];

            if (in_array($key, ['contracts', 'contract_id', 'contract_ids'])) {
                continue;
            }

            if (in_array($key, ['pressure_systolic', 'pressure_diastolic'])) {
                $element[$key] = null;

                continue;
            }

            if (is_array($value)) {
                $element[$key] = join(',', $value);

                continue;
            }

            if (preg_match('/^data:image\/(\w+);base64,/', $value)) {
                $key = str_replace('_base64', '', $key);

                $base64_image = base64_decode(substr($value, strpos($value, ',') + 1));

                $name = $this->model . '_' . $element->id;
                $path = "elements/$name.png";

                Storage::disk('public')->put($path, $base64_image);

                $element[$key] = $path;

                continue;
            }

            if (isset($value) && !isset($this->files[$key])) {
                $element[$key] = $value;
            }
        }

        $this->data = $data;
        $this->element = $element;
        $this->oldDataModel = $oldDataModel;
    }

    /**
     * @throws Exception
     */
    protected function syncCompanyFields()
    {
        $companyId = $this->element->company_id ?? 0;
        $company = Company::find($companyId);
        if (!$company) {
            throw new Exception('Компания не найдена');
        }

        if ($this->oldDataModel['company_id'] != $companyId) {
            $aSyncFields = explode(',', $this->element->autosync_fields);

            foreach ($aSyncFields as $fSync) {
                $this->element->$fSync = $company->$fSync;
            }
        }
    }
}
