<?php

namespace App\Services\Notifier;

use App\Settings;
use App\ValueObjects\Phone;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

class SMSNotifierService
{
    public function notify($to, string $message)
    {
        try {
            if (config('app.env') !== 'production') return '';

            $apiKey = Settings::setting('sms_api_key');
            if (empty($apiKey)) return '';

            $phone = new Phone($to);
            if (!$phone->isValid()) {
                $errorMessage = 'Не валидный номер телефона';
                throw new Exception($errorMessage);
            }

            $to = $phone->getSanitized();

            $body = file_get_contents("https://sms.ru/sms/send?api_id=$apiKey&to=$to&msg=".urlencode($message)."&json=1");

            $json = json_decode($body);

            if (!$json) {
                $errorMessage = "Запрос не выполнился. Не удалось установить связь с сервером.";
                throw new Exception($errorMessage);
            }

            if ($json->status != "OK") {
                $errorMessage = "Запрос не выполнился. Код ошибки: $json->status_code. Текст ошибки: $json->status_text";
                throw new Exception($errorMessage);
            }

            $wrongNumbers = [];
            foreach ($json->sms as $phone => $data) { // Перебираем массив СМС сообщений
                if ($data->status == "OK") { // Сообщение отправлено
                    continue;
                }

                $errorMessage = "Сообщение на номер $phone не отправлено. Код ошибки: $data->status_code. Текст ошибки: $data->status_text. ";
                $wrongNumbers[] = $errorMessage;
            }

            if (count($wrongNumbers)) {
                throw new Exception(implode('', $wrongNumbers));
            }

            return $json;
        } catch (Throwable $exception) {
            $logData = [
                'to' => $to,
                'message' => $message,
                'exception' => $exception->getMessage()
            ];

            Log::channel('sms-api')->info(json_encode($logData));

            return '';
        }
    }
}
