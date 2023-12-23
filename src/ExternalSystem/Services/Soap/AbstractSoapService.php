<?php

namespace Src\ExternalSystem\Services\Soap;

use DomainException;
use Exception;
use Log;
use SimpleXMLElement;
use SoapClient;

abstract class AbstractSoapService
{
    /**
     * @param string $soapEndpoint
     * @param string $methodName
     * @param string $xmlRequest
     * @return array
     * @throws Exception
     */
    final protected function _request(string $soapEndpoint, string $methodName, string $xmlRequest): array
    {
        $client = new SoapClient(
            $soapEndpoint,
            [
                'trace' => 1
            ]
        );

        $xmlResponse = $client->__doRequest($xmlRequest, $soapEndpoint, $methodName, SOAP_1_1);

        $xmlResponse = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xmlResponse);
        $xml = new SimpleXMLElement($xmlResponse);
        $body = $xml->xpath('//sBody')[0];

        $response = json_decode(json_encode((array)$body), TRUE);

        if (array_key_exists('sFault', $response)) {
            $message = 'Произошла ошибка во время обращения к API';

            if (array_key_exists('faultstring', $response['sFault'])) {
                $message = $response['sFault']['faultstring'];
            }

            $log = compact('soapEndpoint', 'methodName', 'response', 'xmlRequest');
            Log::channel('daily')->error($message, $log);

            throw new DomainException($message);
        }

        return $response[$methodName.'Response'];
    }
}
