<?php

namespace App\Services;
use App\Services\Contracts\SmsServiceInterface;
use App\Settings;
use GuzzleHttp\Client as HttpClient;
use PHPUnit\Framework\Constraint\Count;

class SmsRuService implements SmsServiceInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|string|null
     */
    protected string $apiKey;

    /**
     * Номер, если посылаем на один.
     * @var string
     */
    public string $to = '';

    /**
     * Сообщение.
     * @var string
     */
    public string $msg = '';

    /**
     * Массив номеров. ['Номер' => 'Сообщение']
     * От 1 пары до 100 штук.
     * @var array
     */
    public array $multi = [];


    /**
     * Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
     * @var int
     */
    public int $test = 0;


    public function __construct()
    {
        $this->smsPhone = Settings::setting('sms_text_phone');
        $this->apiKey = Settings::setting('sms_api_key');
    }

    public function send(): bool
    {
        /*
         * 100\nZZ
         * 202308-1000106\n
         * balance=3504.36
         * http://sms.ru/
         *
         * @todo Обработать результат запроса и сделать логирование
         * */

        if (count($this->multi) > 0) {
            $response = (new HttpClient())->post('https://sms.ru/sms/send', ['form_params' => [
                'api_id' => $this->apiKey,
                'to' => '+7 978 0310905',
                'msg' => urldecode('13213123213')
            ]]);
        }
        dump($response->getBody()->getContents());
        return $response->getStatusCode() === 200;
    }
}