<?php

namespace App\Services\OneC;

use App\Exceptions\OneCIntegration\OneCIntegrationException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

abstract class OneCIntegrationService implements OneCIntegrationServiceInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var bool
     */
    protected $clientInit = false;

    /**
     * @var string
     */
    private $baseUrl;

    public function __construct()
    {
        $url = config('services.one-c.url');
        $login = config('services.one-c.login');
        $password = config('services.one-c.password');

        if (!$url || !$login || !$password) {
            return;
        }

        $this->baseUrl = rtrim($url, '/');

        $this->client = new Client([
            'auth' => [$login, $password]
        ]);

        $this->clientInit = true;
    }

    protected function getUrl(string $url): string
    {
        $url = ltrim(trim($url), '/');

        return $this->baseUrl . '/' . $url;
    }

    /**
     * @throws OneCIntegrationException
     */
    protected function handleError(ResponseInterface $response)
    {
        $statusCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody()->getContents(), true);

        Log::channel('one-c-api')->error(json_encode($responseBody));

        $exceptionMessage = $statusCode . " : " . $responseBody['message'] ?? 'Ошибка интеграции';

        throw new OneCIntegrationException($exceptionMessage);
    }

    public function healthCheck(): bool
    {
        //TODO: реализовать healthcheck
        return true;
    }
}
