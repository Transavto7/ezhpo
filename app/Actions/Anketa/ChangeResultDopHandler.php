<?php

namespace App\Actions\Anketa;

use App\Enums\FormLogActionTypesEnum;
use App\Enums\FormTypeEnum;
use App\Events\Forms\FormAction;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Models\Forms\TechForm;
use App\Services\DuplicatesCheckerService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ChangeResultDopHandler
{
    /**
     * @throws Exception
     */
    public function handle(Form $form, string $result)
    {
        switch ($form->type_anketa) {
            case FormTypeEnum::MEDIC:
                $this->handleMedic($form, $result);
                break;
            case FormTypeEnum::TECH:
                $this->handleTech($form, $result);
                break;
            default:
                throw new Exception('Ввод ПЛ доступен только для МО и ТО!');
        }
    }

    /**
     * @throws Exception
     */
    protected function handleTech(Form $form, string $result)
    {
        $this->validate($form);

        /** @var TechForm $details */
        $details = $form->details;

        if (!$form->date || !$details->car_id) {
            throw new Exception('Указаны не полные данные осмотра');
        }

        $existForms = DuplicatesCheckerService::getExistTechForms([$details->car_id]);

        DuplicatesCheckerService::checkExist($existForms, Carbon::parse($form->date)->timestamp);

        if ($details->number_list_road === null) {
            $details->number_list_road = $details->car_id . '-' . date('d.m.Y', strtotime($form->date));
        }

        $details->result_dop = $result;
        $form->touch();

        $this->fireEvent($form);

        $details->save();
        $form->save();
    }

    /**
     * @throws Exception
     */
    protected function handleMedic(Form $form, string $result)
    {
        $this->validate($form);

        $existForms = DuplicatesCheckerService::getExistTechForms([$form->driver_id]);;

        DuplicatesCheckerService::checkExist($existForms, Carbon::parse($form->date)->timestamp);

        /** @var MedicForm $details */
        $details = $form->details;

        $details->result_dop = $result;

        $this->fireEvent($form);

        $details->save();
    }

    /**
     * @throws Exception
     */
    protected function validate(Form $form)
    {
        $details = $form->details;

        if ($details->is_dop !== 1) {
            throw new Exception("Осмотр с id $form->id добавлен не в режиме ввода ПЛ");
        }

        if ($details->result_dop !== null) {
            throw new Exception("Осмотр с id $form->id уже утвержден");
        }
    }

    private function fireEvent(Form $form)
    {
        event(new FormAction(Auth::user(), $form, FormLogActionTypesEnum::APPROVAL));
    }
}
