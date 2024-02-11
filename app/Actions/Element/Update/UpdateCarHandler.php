<?php

namespace App\Actions\Element\Update;

use App\Models\Contract;
use Exception;

class UpdateCarHandler extends UpdateElementHandler
{
    /**
     * @throws Exception
     */
    public function handle($id, array $data)
    {
        $this->setData($data);
        $this->findElement($id);
        $this->wrapNullFieldsToEmptyString();
        $this->updateFiles();
        $this->updateFields();
        $this->syncCompanyFields();
        $this->resetEmptyFields();
        $this->element->save();
        $this->attachContracts();
    }

    protected function attachContracts()
    {
        $this->element
            ->contracts()
            ->sync($this->data['contract_ids'] ?? []);

        $contract = Contract::query()
            ->where('company_id', $this->data['company_id'])
            ->where('main_for_company', 1)
            ->first();

        if (!$contract) {
            return;
        }

        $contract->cars()->attach($this->element->id);

        $contract->save();
    }
}
