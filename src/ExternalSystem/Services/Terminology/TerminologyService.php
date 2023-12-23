<?php

namespace Src\ExternalSystem\Services\Terminology;

use DomainException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final class TerminologyService
{
    /**
     * @throws GuzzleException
     */
    public function getSexList(): array
    {
        return $this->getList(config('external-system.term_sex_code'));
    }

    /**
     * @throws GuzzleException
     */
    public function getSocialGroupList(): array
    {
        return $this->getList(config('external-system.term_social_group_code'));
    }

    /**
     * @throws GuzzleException
     */
    public function getIdBloodTypeList(): array
    {
        return $this->getList(config('external-system.term_id_blood_type_code'));
    }

    /**
     * @throws GuzzleException
     */
    public function getIdLivingAreaTypeList(): array
    {
        return $this->getList(config('external-system.term_id_living_area_type_code'));
    }

    /**
     * @throws GuzzleException
     */
    public function getSocialStatusList(): array
    {
        return $this->getList(config('external-system.term_social_status_code'));
    }

    /**
     * @throws GuzzleException
     */
    private function getList(string $code): array
    {
        try {
            $client = new Client();

            $body = [
                "resourceType" => "Parameters",
                "parameter" => [
                    ["name" => "system", "valueString" => 'urn:oid:'.$code],
                ]
            ];

            $response = $client->request(
                'post',
                config('external-system.api_term').'ValueSet/$expand',
                [
                    'json' => $body
                ]
            );

            if ($response->getStatusCode() === 200) {
                $data = $response->getBody()->getContents();
                $data = json_decode($data);

                return $data->parameter[0]->resource->expansion->contains;
            }
            else {
                throw new DomainException("Загрузка справочника '$code'. завершилась с кодом ".$response->getStatusCode());
            }
        } catch (Exception $exception) {
            throw new DomainException("Ошибка загрузки справочника '$code'");
        }
    }
}
