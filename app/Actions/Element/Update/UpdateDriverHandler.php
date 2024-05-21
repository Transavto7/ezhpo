<?php

namespace App\Actions\Element\Update;

use App\Driver;
use App\Events\Relations\Attached;
use App\Events\Relations\Detached;
use App\Models\Contract;
use Exception;

class UpdateDriverHandler extends UpdateElementHandler
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
        $changes = $this->element
            ->contracts()
            ->sync($this->data['contract_ids'] ?? []);
        event(new Attached($this->element, $changes['attached'], Contract::class));
        event(new Detached($this->element, $changes['detached'], Contract::class));

        /** @var Contract $contract */
        $contract = Contract::query()
            ->where('company_id', $this->data['company_id'])
            ->where('main_for_company', 1)
            ->first();

        if (!$contract) {
            return;
        }

        $contract->drivers()->attach($this->element->id);
        event(new Attached($contract, $changes['attached'], Driver::class));

        $contract->save();
    }
}
