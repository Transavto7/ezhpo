<?php

namespace Src\Notifications\TelegramNotifier;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Src\Notifications\Notifier;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class TelegramNotifier implements Notifier
{
    public function notify(string $subject, string $message): bool
    {
        try {
            $token = config('telegram.bot_token');

            if (empty($token)) {
                return false;
            }

            $client = new Client();

            $url = "https://api.telegram.org/bot$token/sendMessage";

            $query = [
                'chat_id' => $subject,
                'text' => $message,
                'parse_mode' => 'markdown',
            ];

            $response = $client->post($url, ['query' => $query]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpException(
                    $response->getStatusCode(),
                    'Ошибка при выполнении запроса к telegram API!',
                );
            }

            return true;
        } catch (Throwable $exception) {
            $logData = [
                'to' => $subject,
                'message' => $message,
                'exception' => $exception->getMessage(),
            ];

            Log::channel('tg-api')->info(json_encode($logData));

            return false;
        }
    }
}
