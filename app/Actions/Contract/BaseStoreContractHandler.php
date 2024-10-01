<?php

namespace App\Actions\Contract;

use App\Car;
use App\Driver;
use App\Events\Relations\Attached;
use App\Events\Relations\Detached;
use App\Models\Contract;
use App\Product;
use Carbon\Carbon;
use Exception;
use Ramsey\Uuid\Uuid;

class BaseStoreContractHandler
{
    /**
     * @throws Exception
     */
    protected function checkExistContract(array $data)
    {
        $mainForCompany = $data['main_for_company'] ?? 0;
        $companyId = $data['company']['id'] ?? null;
        $dateOfEnd = isset($data['date_of_end']) ? Carbon::parse($data['date_of_end']) : null;
        $dateOfStart = isset($data['date_of_start']) ? Carbon::parse($data['date_of_start']) : null;

        if (!$mainForCompany || !$companyId || !$dateOfEnd || !$dateOfStart) {
            return;
        }

        $contract = Contract::query()
            ->whereNotBetween(
                'date_of_end', [
                $dateOfStart,
                $dateOfEnd,
            ])->whereNotBetween(
                'date_of_end', [
                $dateOfStart,
                $dateOfEnd,
            ])
            ->where('main_for_company', 1)
            //TODO: такого скоупа нет!
            ->whereCompanyId($companyId)
            ->first();

        if ($contract) {
            throw new Exception('Не возможно установить главный договор, так как на данный интервал у данной компании есть главный договор');
        }
    }

    protected function syncRelations(Contract $contract, array $data): Contract
    {
        $servicesToSync = [];
        foreach ($data['services'] ?? [] as $service) {
            $servicesToSync[$service['id']] = [
                'service_cost' => $service['pivot']['service_cost'] ?? $service['price_unit'],
            ];
        }

        $syncEventUuid = Uuid::uuid4();

        $changes = $contract->services()->sync($servicesToSync);
        event(new Attached($contract, $changes['attached'], Product::class, $syncEventUuid));
        event(new Detached($contract, $changes['detached'], Product::class, $syncEventUuid));

        $changes = $contract->cars()->sync($data['cars'] ?? []);
        event(new Attached($contract, $changes['attached'], Car::class, $syncEventUuid));
        event(new Detached($contract, $changes['detached'], Car::class, $syncEventUuid));

        $changes = $contract->drivers()->sync($data['drivers'] ?? []);
        event(new Attached($contract, $changes['attached'], Driver::class, $syncEventUuid));
        event(new Detached($contract, $changes['detached'], Driver::class, $syncEventUuid));

        $contract->save();

        return $contract;
    }
}
