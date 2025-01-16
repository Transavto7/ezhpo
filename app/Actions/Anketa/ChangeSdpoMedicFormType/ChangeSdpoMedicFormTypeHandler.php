<?php

namespace App\Actions\Anketa\ChangeSdpoMedicFormType;

use App\Enums\FlagPakEnum;
use App\Enums\FormLogActionTypesEnum;
use App\Enums\FormTypeEnum;
use App\Events\Forms\FormAction;
use App\Models\Forms\Form;
use App\User;
use Exception;

class ChangeSdpoMedicFormTypeHandler
{
    /**
     * @throws Exception
     */
    public function handle($id, User $user)
    {
        $inspection = Form::withTrashed()->find($id);
        if (!$inspection) {
            throw new Exception('Осмотр не найден');
        }

        if ($inspection->deleted_at !== null) {
            throw new Exception('Осмотр удален');
        }

        if ($inspection->type_anketa !== FormTypeEnum::PAK_QUEUE) {
            throw new Exception('Осмотр не находится в очереди утверждения');
        }

        $inspection->fill([
            'type_anketa' => FormTypeEnum::MEDIC,
        ]);

        $details = $inspection->details;

        if (!$details) {
            throw new Exception('У осмотра не найдены подробности');
        }

        $details->fill([
            'flag_pak' => FlagPakEnum::SDPO_A
        ]);

        event(new FormAction($user, $inspection, FormLogActionTypesEnum::QUEUE_PROCESSING));

        $inspection->save();
        $details->save();
    }
}
