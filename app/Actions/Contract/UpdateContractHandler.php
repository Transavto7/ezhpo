<?php

namespace App\Actions\Contract;

use App\Models\Contract;
use Carbon\Carbon;
use Exception;

class UpdateContractHandler extends BaseStoreContractHandler
{
    /**
     * @throws Exception
     */
    public function handle(array $data): Contract
    {
        $contract = Contract::find($data['id']);
        if (!$contract) {
            throw new Exception('Контракт с таким ID не найден');
        }

        $this->checkExistContract($data);

        $mainForCompany = $data['main_for_company'] ?? 0;
        $companyId = $data['company']['id'] ?? null;
        $dateOfEnd = isset($data['date_of_end']) ? Carbon::parse($data['date_of_end']) : null;
        $dateOfStart = isset($data['date_of_start']) ? Carbon::parse($data['date_of_start']) : null;

        $contract->update([
            'name' => $data['name'] ?? null,
            'date_of_start' => $dateOfStart,
            'date_of_end' => $dateOfEnd,
            'company_id' => $companyId,
            'our_company_id' => $data['our_company']['id'] ?? null,
            'main_for_company' => $mainForCompany,
            'finished' => $data['finished'] ?? 0,
        ]);

        return $this->syncRelations($contract, $data);
    }
}
