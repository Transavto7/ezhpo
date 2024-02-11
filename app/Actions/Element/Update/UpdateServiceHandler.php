<?php

namespace App\Actions\Element\Update;

class UpdateServiceHandler extends UpdateElementHandler
{
    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();

        if ($this->element->type_product === 'Абонентская плата без реестров') {
            $this->element->type_anketa = null;
            $this->element->type_view   = null;
        }

        $this->resetEmptyFields();
        $this->element->save();
    }
}
