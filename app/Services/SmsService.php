<?php

namespace App\Services;
use App\Settings;
use GuzzleHttp\Client as HttpClient;

class SmsService implements Contracts\ServiceInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|string|null
     */
    protected string $apiKey;

    public string $smsPhone;

    public function __construct()
    {
        $this->smsPhone = Settings::setting('sms_text_phone');
        $this->apiKey = Settings::setting('sms_api_key');
    }

    public function send(string $to, string $msg): bool
    {
        $response = (new HttpClient())->get('https://sms.ru/sms/send', ['query' => [
            'api_id' => $this->apiKey,
            'to' => $to,
            'msg' => urldecode($msg)
        ]]);
        /*
         * 100\nZZ
         * 202308-1000106\n
         * balance=3504.36
         * http://sms.ru/
         *
         * @todo Обработать результат запроса и сделать логирование
         * */
        return $response->getStatusCode() === 200;
    }
}