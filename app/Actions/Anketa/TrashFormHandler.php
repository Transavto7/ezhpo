<?php

namespace App\Actions\Anketa;

use App\Driver;
use App\Enums\FormTypeEnum;
use App\Models\Forms\Form;
use App\User;
use Carbon\Carbon;

final class TrashFormHandler
{
    public function handle(Form $form, $action, User $user)
    {
        $form->in_cart = $action;

        if ($form->type_anketa === FormTypeEnum::MEDIC && $form->driver_id) {
            $this->disableBanIfNeed($form);
        }

        $form->deleted_id = $user->id;

        if ($action) {
            $form->deleted_at = Carbon::now();
        } else {
            $form->deleted_at = null;
        }

        $form->save();
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
