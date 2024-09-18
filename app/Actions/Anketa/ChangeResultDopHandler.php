<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Enums\FormTypeEnum;
use App\Services\DuplicatesCheckerService;
use Exception;
use Illuminate\Support\Carbon;

class ChangeResultDopHandler
{
    /**
     * @throws Exception
     */
    public function handle(Anketa $form, string $result)
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
    protected function handleTech(Anketa $form, string $result)
    {
        if (!$form->date || !$form->car_id) {
            throw new Exception('Указаны не полные данные осмотра');
        }

        $existForms = DuplicatesCheckerService::getExistTechForms([$form->car_id]);

        DuplicatesCheckerService::checkExist($existForms, Carbon::parse($form->date)->timestamp);

        if ($form->number_list_road === null) {
            $form->number_list_road = $form->car_id . '-' . date('d.m.Y', strtotime($form->date));
        }

        $form->result_dop = $result;

        $form->save();
    }

    /**
     * @throws Exception
     */
    protected function handleMedic(Anketa $form, string $result)
    {
        $existForms = DuplicatesCheckerService::getExistMedicForms([$form->driver_id]);;

        DuplicatesCheckerService::checkExist($existForms, Carbon::parse($form->date)->timestamp);

        $form->result_dop = $result;

        $form->save();
    }
}
