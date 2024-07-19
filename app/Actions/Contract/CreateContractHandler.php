<?php

namespace App\Actions\Contract;

use App\Models\Contract;
use Carbon\Carbon;
use Exception;

class CreateContractHandler extends BaseStoreContractHandler
{
    /**
     * @throws Exception
     */
    public function handle(array $data): Contract
    {
        $this->checkExistContract($data);

        $mainForCompany = $data['main_for_company'] ?? 0;
        $companyId = $data['company']['id'] ?? null;
        $dateOfEnd = isset($data['date_of_end']) ? Carbon::parse($data['date_of_end']) : null;
        $dateOfStart = isset($data['date_of_start']) ? Carbon::parse($data['date_of_start']) : null;

        /** @var Contract $contract */
        $contract = Contract::create([
            'name' => $data['name'] ?? null,
            'date_of_end' => $dateOfEnd,
            'date_of_start' => $dateOfStart,
            'company_id' => $companyId,
            'our_company_id' => $data['our_company']['id'] ?? null,
            'main_for_company' => $mainForCompany,
        ]);

        return $this->syncRelations($contract, $data);
    }
}
