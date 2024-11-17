<?php

namespace App\Actions\Anketa;

use App\Enums\FormTypeEnum;
use Exception;

class CreateFormHandlerFactory
{
    /**
     * @throws Exception
     */
    public function make(string $formType): CreateFormHandlerInterface
    {
        switch ($formType) {
            case FormTypeEnum::MEDIC:
                return new CreateMedicFormHandler();
            case FormTypeEnum::TECH:
                return new CreateTechFormHandler();
            case FormTypeEnum::BDD:
                return new CreateBddFormHandler();
            case FormTypeEnum::REPORT_CARD:
                return new CreateReportCardFormHandler();
            case FormTypeEnum::PRINT_PL:
                return new CreatePrintPlFormHandler();
            case FormTypeEnum::PAK:
            case FormTypeEnum::VID_PL:
                throw new Exception("Тип анкеты - $formType не может быть создан через веб-интерфейс");
            default:
                throw new Exception("Неизвестный тип анкеты - $formType");
        }
    }
}
