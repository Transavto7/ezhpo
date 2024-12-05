<?php

namespace App\Services\OneC\CompanySync;

use App\Company;
use App\Exceptions\OneCIntegration\OneCIntegrationEmptyConfigException;
use App\Exceptions\OneCIntegration\OneCIntegrationException;
use App\Exceptions\WrongCompanyReqsException;
use App\Services\OneC\OneCIntegrationService;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
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
            //TODO: заменить на official_name
            'name',
            'inn',
            'kpp',
        ]);

        $response = $this->client->post($url,  [
            RequestOptions::JSON => $body
        ]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            $this->handleError($response);
        }
    }
}
