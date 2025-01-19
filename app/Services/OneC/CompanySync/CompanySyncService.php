<?php

namespace App\Services\OneC\CompanySync;

use App\Company;
use App\Exceptions\OneCIntegration\OneCIntegrationEmptyConfigException;
use App\Exceptions\OneCIntegration\OneCIntegrationException;
use App\Exceptions\WrongCompanyReqsException;
use App\Services\OneC\OneCIntegrationService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;

class CompanySyncService extends OneCIntegrationService implements CompanySyncServiceInterface
{
    /**
     * @throws WrongCompanyReqsException
     * @throws OneCIntegrationEmptyConfigException
     * @throws GuzzleException
     * @throws OneCIntegrationException
     */
    public function create(Company $company)
    {
        if (!$this->clientInit) throw new OneCIntegrationEmptyConfigException();

        if (!$company->getAttribute('reqs_validated')) {
            throw new WrongCompanyReqsException();
        }

        $url = $this->getUrl('contractors');
        $body = $company->only([
            'hash_id',
            'official_name',
            'name',
            'inn',
            'kpp',
            'ogrn',
            'address'
        ]);

        $response = $this->client->post($url,  [
            RequestOptions::JSON => $body
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $this->handleError($response);
        }
    }

    /**
     * @throws OneCIntegrationEmptyConfigException
     * @throws WrongCompanyReqsException
     * @throws Exception
     * @throws GuzzleException
     */
    public function update(Company $company)
    {
        if (!$this->clientInit) throw new OneCIntegrationEmptyConfigException();

        if (!$company->getAttribute('reqs_validated')) {
            throw new WrongCompanyReqsException();
        }

        $url = $this->getUrl("contractors/{$company->getAttribute('hash_id')}");
        $body = $company->only([
            'official_name',
            'name',
        ]);

        $response = $this->client->patch($url,  [
            RequestOptions::JSON => $body
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $this->handleError($response);
        }
    }
}
