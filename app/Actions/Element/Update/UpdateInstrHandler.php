<?php

namespace App\Actions\Element\Update;

class UpdateInstrHandler extends UpdateElementHandler
{
    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->updateDefaultInstr();
        $this->resetEmptyFields();
        $this->element->save();
    }

    protected function updateDefaultInstr()
    {
        $this->model::query()
            ->where('type_briefing', $this->element->type_briefing)
            ->where('is_default', 1)
            ->update(["is_default" => 0]);

        $this->element->is_default = ($data['is_default'] ?? null) == 'on';
    }
}
