<?php

namespace App\Actions\Anketa;

use App\Enums\FormTypeEnum;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Models\Forms\TechForm;
use App\Services\DuplicatesCheckerService;
use Exception;
use Illuminate\Support\Carbon;

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
        $details->save();

        $form->touch();
        $form->save();
    }

    /**
     * @throws Exception
     */
    protected function handleMedic(Form $form, string $result)
    {
        $existForms = DuplicatesCheckerService::getExistTechForms([$form->driver_id]);;

        DuplicatesCheckerService::checkExist($existForms, Carbon::parse($form->date)->timestamp);

        /** @var MedicForm $details */
        $details = $form->details;

        $details->result_dop = $result;
        $details->save();
    }
}
