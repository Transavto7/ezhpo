<?php

namespace App\Services\CompanyReqsChecker;

use App\ValueObjects\CompanyReqs;
use Dadata\DadataClient;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Throwable;

class DaDataCompanyReqsChecker implements CompanyReqsCheckerInterface
{
    const PAUSE_BETWEEN_REQUEST_MICROSECONDS = 34000;

    /** @var DadataClient  */
    private $daData;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $token = config('services.dadata.token');
        if (empty($token)) {
            throw new Exception('Отсутствует токен DaData');
        }

        $this->daData = new DadataClient($token, null);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function check(CompanyReqs $companyReqs): bool
    {
        $companies = $this->getCompaniesByInn($companyReqs->getInn());

        if ($companyReqs->isPersonalInnFormat()) {
            return $this->checkPerson($companyReqs, $companies);
        }

        if ($companyReqs->isOrganizationInnFormat()) {
            return $this->checkOrganization($companyReqs, $companies);
        }

        return false;
    }

    /**
     * @param string $inn
     * @return DaDataCompany[]
     * @throws GuzzleException
     */
    private function getCompaniesByInn(string $inn): array
    {
        try {
            usleep(self::PAUSE_BETWEEN_REQUEST_MICROSECONDS);

            $result = array_map(function ($result) {
                return new DaDataCompany(
                    $result['value'],
                    $result['data']['inn'],
                    $result['data']['kpp'] ?? null
                );
            }, $this->daData->findById("party", $inn));

            Log::channel('da-data-api')->info('Поиск по ИНН: ' . $inn . ' ,найдено: ' . count($result));

            return $result;
        } catch (Throwable $exception) {
            Log::channel('da-data-api')->error('Ошибка обращения к API: ' . $exception->getMessage());

            return [];
        }
    }

    /**
     * @param CompanyReqs $companyReqs
     * @param DaDataCompany[] $companies
     * @return bool
     */
    private function checkPerson(CompanyReqs $companyReqs, array $companies): bool
    {
        //TODO: нужно позднее не просто разделять на ИНН человека\компании, а еще проверять, ИП это или физик
        if (count($companies) === 0) {
            return true;
        }

        foreach ($companies as $company) {
            if ($company->getInn() === $companyReqs->getInn()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param CompanyReqs $companyReqs
     * @param DaDataCompany[] $companies
     * @return bool
     */
    private function checkOrganization(CompanyReqs $companyReqs, array $companies): bool
    {
        if (count($companies) === 0) {
            return false;
        }

        $hasSubCompanyWithoutKpp = false;

        foreach ($companies as $company) {
            if (empty($company->getKpp())) {
                $hasSubCompanyWithoutKpp = true;

                continue;
            }

            if ($company->getKpp() === $companyReqs->getKpp()) {
                return true;
            }
        }

        if ($hasSubCompanyWithoutKpp) {
            return true;
        }

        return false;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function restoreOrganization(CompanyReqs $companyReqs): ?CompanyReqs
    {
        if (!$companyReqs->isOrganizationInnFormat()) {
            throw new Exception('Восстановление КПП для ФЛ!');
        }

        $companies = $this->getCompaniesByInn($companyReqs->getInn());
        $companiesCount = count($companies);

        if ($companiesCount === 1) {
            $organization = array_values($companies)[0];

            if ($organization->getKpp()) {
                return new CompanyReqs($companyReqs->getInn(), $organization->getKpp());
            }

            if ($companyReqs->isValidFormat()) {
                return $companyReqs;
            }

            return null;
        }

        $hasSubCompanyWithoutKpp = false;

        foreach ($companies as $organization) {
            if (empty($organization->getKpp())) {
                $hasSubCompanyWithoutKpp = true;

                continue;
            }

            if ($organization->getKpp() === $companyReqs->getKpp()) {
                return $companyReqs;
            }
        }

        if ($hasSubCompanyWithoutKpp && $companyReqs->isValidFormat()) {
            return $companyReqs;
        }

        return null;
    }
}
