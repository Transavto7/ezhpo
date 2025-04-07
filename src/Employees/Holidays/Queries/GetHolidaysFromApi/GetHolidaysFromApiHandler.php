<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Queries\GetHolidaysFromApi;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetHolidaysFromApiHandler
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(
        HttpClientInterface $client
    ) {
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function handle(GetHolidaysFromApiQuery $query): string
    {
        $response = $this->client->request(
            'GET',
            'https://isdayoff.ru/api/getdata?year='.$query->getYear()
        );

        return $response->getContent();
    }
}
