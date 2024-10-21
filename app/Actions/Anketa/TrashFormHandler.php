<?php

namespace App\Actions\Anketa;

use App\Driver;
use App\Enums\FormTypeEnum;
use App\Models\Forms\Form;
use App\User;

final class TrashFormHandler
{
    public function handle(Form $form, $action, User $user)
    {
        $form->deleted_id = $user->id;

        if (!$action) {
            $form->restore();

            return;
        }

        if ($form->type_anketa === FormTypeEnum::MEDIC && $form->driver_id) {
            $this->disableBanIfNeed($form);
        }

        $form->delete();
    }

    protected function disableBanIfNeed(Form $form)
    {
        $driver = Driver::where('hash_id', $form->driver_id)->first();

        if (!$driver) {
            return;
        }

        if (!$driver->end_of_ban) {
            return;
        }

        $last = Form::query()
            ->select([
                'id'
            ])
            ->where('type_anketa', FormTypeEnum::MEDIC)
            ->where('driver_id', $form->driver_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($last->id === $form->id) {
            $driver->end_of_ban = null;
            $driver->save();
        }
    }
}
