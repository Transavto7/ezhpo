<?php

namespace App\Observers;

use App\Anketa;
use App\Models\Forms\Form;

class AnketaObserver
{
    /**
     * Handle the form "updating" event.
     *
     * @param Anketa $anketa
     * @return void
     */
    public function updating(Anketa $anketa)
    {
        $notTransfer = $anketa->getOriginal('transfer_status');
        if (!$notTransfer) {
            return;
        }

        $form = Form::withTrashed()->find($anketa->uuid);
        if (!$form) {
            return;
        }

        $form->details()->delete();
        $form->forceDelete();

        $anketa->setAttribute('transfer_status', false);
    }
}
