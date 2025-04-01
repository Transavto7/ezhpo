<?php

namespace App\Services\OneC\CompanyDebt;

use App\Company;
use App\Exceptions\OneCIntegration\OneCIntegrationEmptyConfigException;
use App\Services\OneC\OneCIntegrationService;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Src\Companies\Entities\CompanyOneCDebt;
use Src\Companies\Entities\CompanyOneCShortDebt;
use Src\Companies\Entities\DebtStructure;
use Throwable;

final class CompanyDebtService extends OneCIntegrationService implements CompanyDebtServiceInterface
{
    /**
     * @throws OneCIntegrationEmptyConfigException
     * @throws Exception
     */
    public function get(Company $company): CompanyOneCDebt
    {
        if (! $this->clientInit) {
            throw new OneCIntegrationEmptyConfigException();
        }

        $url = $this->getUrl("debt/$company->hash_id");

        try {
            $response = $this->client->get($url);
        } catch (BadResponseException $exception) {
            $this->handleError($exception->getResponse());
        } catch (Throwable $exception) {
            throw new Exception('Ошибка 1С. Обратитесь к администратору.');
        }

        $debtInfo = json_decode($response->getBody()->getContents());

        $debtStructure = array_reduce($debtInfo->debt_structure, function (array $carry, $item) {
            $carry[] = new DebtStructure($item->object, $item->debt);

            return $carry;
        }, []);

        return new CompanyOneCDebt(
            $company->hash_id,
            $debtInfo->is_debt,
            $debtStructure,
            $debtInfo->message
        );
    }

    /**
     * @throws OneCIntegrationEmptyConfigException
     * @throws Exception|GuzzleException
     */
    public function getAll(): array
    {
        if (! $this->clientInit) {
            throw new OneCIntegrationEmptyConfigException();
        }

        $url = $this->getUrl('debt');

        try {
            $response = $this->client->get($url);
        } catch (BadResponseException $exception) {
            $this->handleError($exception->getResponse());
        } catch (Throwable $exception) {
            throw new Exception('Ошибка 1С. Обратитесь к администратору.');
        }

        $debtsInfo = json_decode($response->getBody()->getContents());

        return array_reduce($debtsInfo->contractors ?? [], function (array $carry, $item) {
            $carry[] = new CompanyOneCShortDebt($item->hash_id, (int) $item->debt);

            return $carry;
        }, []);
    }
}
