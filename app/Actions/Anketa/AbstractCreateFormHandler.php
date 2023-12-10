<?php

namespace App\Actions\Anketa;

use App\DDates;
use App\Enums\FormTypeEnum;
use App\Point;
use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

abstract class AbstractCreateFormHandler
{
    const FORM_TYPE = null;

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

    public function handle(array $data, Authenticatable $user): array
    {
        $this->data = $data;

        /** @var User $user */
        $this->data['user_id'] = $user->id;
        $this->data['user_name'] = $user->name;
        $this->data['operator_id'] = $user->id;
        $this->data['user_eds'] = $user->eds;

        $pointId = $data['pv_id'] ?? 0;
        $this->data['point_id'] = $pointId;
        $point = Point::find($pointId);
        if ($point) {
            $this->data['pv_id'] = $point->name;
        }

        date_default_timezone_set('UTC');
        $this->time = date('Y-m-d H:i:s', time() + ($user->timezone ?: 3) * 3600);

        $this->validateData();
        if (count($this->errors)) {
            return [
                'errors' => $this->errors,
                'type' => self::FORM_TYPE
            ];
        }

        $this->fetchExistForms();

        foreach ($data['anketa'] as $form) {
            $this->createForm($form);
        }

        $responseData = [
            'createdId' => $this->createdForms->pluck('id')->toArray(),
            'errors' => array_unique($this->errors),
            'type' => self::FORM_TYPE
        ];

        if (count($this->redDates) > 0) {
            $responseData['redDates'] = $this->redDates;
        }

        if ($this->data['is_dop'] ?? 0 === 1) {
            $responseData['is_dop'] = 1;
        }

        return $responseData;
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
            'ankets'
        ];

        $is_dop = $form['is_dop'] ?? 0;

        /**
         * Парсим данные в анкете, удаляем главную анкету и ставим актуальную
         */
        foreach ($this->data as $dk => $dv) {
            if (in_array($dk, $excludedFieldsToMerge)) {
                continue;
            }

            $form[$dk] = $dv;
        }

        /**
         * Проверяем дефолтные значения
         */
        foreach ($defaultData as $dk => $dv) {
            if (empty($form[$dk]) && !($dk === 'date' && $is_dop)) {
                $form[$dk] = $dv;
            }
        }

        return $form;
    }

    protected function checkRedDates(string $date, $dateCheckModel)
    {
        $itemModelName = self::FORM_TYPE === FormTypeEnum::MEDIC ? 'Driver' : 'Car';

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

        foreach ($this->data['anketas'] as $otherForm) {
            $diffInHours = round(($formTimestamp - Carbon::parse($otherForm['date'])->timestamp)/60, 1);

            if ($diffInHours < 1 && $diffInHours >= 0) {
                $this->errors[] = "Найден дубликат осмотра при добавлении (Дата: $otherForm[date])";

                return false;
            }
        }

        foreach($this->existForms as $existForm) {
            $diffInHours = round(($formTimestamp - Carbon::parse($existForm->date)->timestamp)/60, 1);

            if ($diffInHours < 1 && $diffInHours >= 0) {
                $this->errors[] = "Найден дубликат осмотра (ID: $existForm->id, Дата: $existForm->date)";

                return false;
            }
        }

        return true;
    }
}
