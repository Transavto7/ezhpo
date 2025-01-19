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

        if ($companyReqs->isPersonalFormat()) {
            return $this->checkPerson($companyReqs, $companies);
        }

        if ($companyReqs->isOrganizationFormat()) {
            return $this->checkOrganization($companyReqs, $companies);
        }

        return false;
    }

    /**
     * @param string $inn
     * @return CompanyInfo[]
     * @throws GuzzleException
     */
    private function getCompaniesByInn(string $inn): array
    {
        try {
            usleep(self::PAUSE_BETWEEN_REQUEST_MICROSECONDS);

            $result = array_map(function ($result) {
                return new CompanyInfo(
                    $result['data']['name']['full_with_opf'],
                    $result['data']['inn'],
                    $result['data']['ogrn'],
                $result['data']['kpp'] ?? null,
                    $result['data']['address']['unrestricted_value']
                );
            }, $this->daData->findById("party", $inn, 300));

            Log::channel('da-data-api')->info('Поиск по ИНН: ' . $inn . ' ,найдено: ' . count($result));

            return $result;
        } catch (Throwable $exception) {
            Log::channel('da-data-api')->error('Ошибка обращения к API: ' . $exception->getMessage());

            return [];
        }
    }

    /**
     * @param CompanyReqs $companyReqs
     * @param CompanyInfo[] $companies
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
     * @param CompanyInfo[] $companies
     * @return bool
     */
    private function checkOrganization(CompanyReqs $companyReqs, array $companies): bool
    {
        if (count($companies) === 0) {
            return false;
        }

        $hasSubCompanyWithoutKpp = false;

        foreach ($companies as $company) {
            if ($company->getOgrn() !== $companyReqs->getOgrn()) {
                continue;
            }

            if (empty($company->getKpp())) {
                $hasSubCompanyWithoutKpp = true;

                continue;
            }

            if ($company->getKpp() !== $companyReqs->getKpp()) {
                continue;
            }

            return true;
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
    public function restoreCompany(CompanyReqs $companyReqs): ?CompanyInfo
    {
        $companies = $this->getCompaniesByInn($companyReqs->getInn());

        if (count($companies) !== 1) {
            return null;
        }

        return array_values($companies)[0];
    }
}
