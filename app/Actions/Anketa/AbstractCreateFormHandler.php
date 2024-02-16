<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Car;
use App\DDates;
use App\Driver;
use App\Point;
use App\User;
use DateTime;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

abstract class AbstractCreateFormHandler
{
    /** @var array */
    protected $data;

    /** @var array */
    protected $redDates;

    /** @var Collection */
    protected $existForms;

    /** @var array */
    protected $errors;

    /** @var Collection */
    protected $createdForms;

    /** @var string */
    protected $time;

    /**
     * @var Authenticatable
     */
    protected $user;

    public function handle(array $data, Authenticatable $user): array
    {
        $this->init();

        $this->data = $data;
        $this->user = $user;

        $this->createAdditionalForms();
        $this->addUserInfo();

        $pointId = $data['pv_id'] ?? 0;
        $this->data['point_id'] = $pointId;
        $point = Point::find($pointId);
        if ($point) {
            $this->data['pv_id'] = $point->name;
        }

        date_default_timezone_set('UTC');
        $this->time = date('Y-m-d H:i:s', time() + ($user->timezone ?: 3) * 3600);

        $this->validateData();
        if (count($this->errors ?? [])) {
            return [
                'errors' => $this->errors
            ];
        }

        $this->fetchExistForms();

        foreach ($this->data['anketa'] as $form) {
            $this->createForm($form);
        }

        $responseData = [
            'createdId' => $this->createdForms->pluck('id')->toArray(),
            'errors' => array_unique($this->errors)
        ];

        if (count($this->redDates ?? []) > 0) {
            $responseData['redDates'] = $this->redDates;
        }

        if ($this->data['is_dop'] ?? 0 === 1) {
            $responseData['is_dop'] = 1;
        }

        return $responseData;
    }

    protected function addUserInfo()
    {
        $user = $this->user;

        /** @var User $user */
        $this->data['user_id'] = $user->id;
        $this->data['user_name'] = $user->name;
        $this->data['operator_id'] = $user->id;
        $this->data['user_eds'] = $user->eds;
        $this->data['user_validity_eds_start'] = $user->validity_eds_start;
        $this->data['user_validity_eds_end'] = $user->validity_eds_end;
    }

    protected function init()
    {
        $this->errors = [];
        $this->redDates = [];
        $this->existForms = collect([]);
        $this->createdForms = collect([]);
    }

    /**
     * @throws Exception
     */
    protected function createAdditionalForms()
    {
        $mainForms = $this->data['anketa'] ?? [];

        foreach ($mainForms ?? [] as $form) {
            $additionalDates = explode(', ', $form['dates'] ?? '') ?? [];

            $maxDatesCount = 31;
            if (count($additionalDates) > 31) {
                throw new Exception("Нельзя вносить осмотры более чем за $maxDatesCount день");
            }

            $baseDateTime = new DateTime($form['date']);
            $baseTime = $baseDateTime->format('H:i');
            $baseDate = $baseDateTime->format('Y-m-d');

            foreach ($additionalDates as $additionalDate) {
                if ($additionalDate === $baseDate) continue;

                if (strlen($additionalDate) === 0) continue;

                $additionalDateTime = "$additionalDate"."T"."$baseTime";

                $additionalForm = $form;

                $additionalForm['date'] = $additionalDateTime;

                $this->data['anketa'][] = $additionalForm;
            }
        }

        foreach ($this->data['anketa'] ?? [] as &$form) {
            unset($form['dates']);
        }
    }

    protected function saveSdpoFormWithError(array $form, string $comment = '')
    {
        if (!isset($form['is_pak'])) {
            return;
        }

        $form['type_anketa'] = 'pak';
        $form['comments'] = $comment;

        Anketa::create($form);
    }

    protected function validateData()
    {

    }

    protected function fetchExistForms()
    {
        $this->existForms = collect([]);
    }

    protected abstract function createForm(array $form);

    protected function mergeFormData(array $form, array $defaultData): array
    {
        $excludedFieldsToMerge = [
            '_token',
            'anketa'
        ];

        /**
         * Парсим данные в анкете, удаляем главную анкету и ставим актуальную
         */
        foreach ($this->data as $dk => $dv) {
            if (in_array($dk, $excludedFieldsToMerge)) {
                continue;
            }

            $form[$dk] = $dv;
        }

        $isDop = $form['is_dop'] ?? 0;

        /**
         * Проверяем дефолтные значения
         */
        foreach ($defaultData as $dk => $dv) {
            if ($dk === 'date' && $isDop) continue;

            if (empty($form[$dk])) {
                $form[$dk] = $dv;
            }
        }

        return $form;
    }

    /**
     * @throws Exception
     */
    protected function checkRedDates(string $date, $dateCheckModel)
    {
        $dateCheckModelClass = get_class($dateCheckModel);
        switch ($dateCheckModelClass) {
            case Driver::class:
                $itemModelName = 'Driver';
                break;
            case Car::class:
                $itemModelName = 'Car';
                break;
            default:
                throw new Exception("Попытка контроля дат для неизвестной модели - {$dateCheckModelClass}");
        }
        $dateCheck = DDates::where('item_model', $itemModelName)->get();

        foreach ($dateCheck ?? [] as $dateCheckItem) {
            $fieldDateCheck = $dateCheckItem->field;

            if (!isset($dateCheckModel[$fieldDateCheck])) {
                continue;
            }

            $fieldDateItemValue = $dateCheckModel[$fieldDateCheck];

            $dateAction = $dateCheckItem->action . ' ' . $dateCheckItem->days . ' days';

            $dateCheckWithForm = date('Y-m-d', strtotime($fieldDateItemValue . ' ' . $dateAction));

            if ($dateCheckWithForm > $date) {
                continue;
            }

            //TODO: здесь берется только по последней анкете
            $this->redDates[$fieldDateCheck] = [
                'value' => $fieldDateItemValue,
                'item_model' => $dateCheckItem->item_model,
                'item_id' => $dateCheckModel->id,
                'item_field' => $fieldDateCheck
            ];
        }
    }

    /**
     * Проверка на дубликат из ТЗ
     *
     * Мы должны дать техническую возможность внесение осмотров любой даты (год назад, месяц назад.
     * При внесении осмотра, система должна смотреть, есть ли подобный.
     *
     * Например, сегодня 13.02.21 в 09.00 до 10.00.
     */
    protected function findDuplicates(array $form): bool
    {
        if ($form['is_dop']) {
            return true;
        }

        $formTimestamp = Carbon::parse($form['date'])->timestamp;

        $formDuplicates = 0;
        $errorMessage = null;
        foreach ($this->data['anketa'] as $otherForm) {
            if ($this->isDuplicate($formTimestamp, $otherForm['date'])) {
                $errorMessage = "Найден дубликат осмотра при добавлении (Дата: $otherForm[date])";
                $formDuplicates++;
            }
        }

        if ($formDuplicates > 1) {
           $this->errors[] = $errorMessage;

            return false;
        }

        foreach($this->existForms as $existForm) {
            if ($this->isDuplicate($formTimestamp, $existForm->date)) {
                $this->errors[] = "Найден дубликат осмотра (ID: $existForm->id, Дата: $existForm->date)";

                return false;
            }
        }

        return true;
    }

    protected function isDuplicate($first, $second): bool
    {
        $diffInMinutes = round(($first - Carbon::parse($second)->timestamp)/60, 1);

        return $diffInMinutes < 1 && $diffInMinutes >= 0;
    }
}
