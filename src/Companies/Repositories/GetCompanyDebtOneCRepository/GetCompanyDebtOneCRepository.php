<?php

namespace Src\Companies\Repositories\GetCompanyDebtOneCRepository;

use App\Company;
use App\Exceptions\OneCIntegration\OneCIntegrationEmptyConfigException;
use App\Services\OneC\OneCIntegrationService;
use Src\Companies\Entities\CompanyOneCDebt;
use Src\Companies\Entities\DebtStructure;
use Throwable;

final class GetCompanyDebtOneCRepository extends OneCIntegrationService
{
    public function get(Company $company): ?CompanyOneCDebt
    {
        if (!$this->clientInit) {
            throw new OneCIntegrationEmptyConfigException();
        }

        $url = $this->getUrl("debt/$company->hash_id");

        try {
            $response = $this->client->get($url);
        } catch (Throwable $exception) {
            if ($exception->getCode()) {
                return null;
            }

            throw new \Exception('Ошибка 1С. Обратитесь к администратору. '.$exception->getMessage());
        }

        $debtInfo = json_decode($response->getBody()->getContents());

        $debtStructure = array_reduce($debtInfo->debt_structure, function (array $carry, $item) {
            $carry[] = new DebtStructure($item->object, $item->debt);

            return $carry;
        }, []);

        return new CompanyOneCDebt(
            $debtInfo->is_debt,
            $debtStructure,
            $debtInfo->message
        );
    }
}
