<?php

namespace App\Services\Notifier;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramNotifierService
{
    public function notify(string $chatId, string $message)
    {
        try {
            if (config('app.env') !== 'production') return '';

            $token = config('telegram.bot_token');

            if (empty($token)) return '';

            $client = new Client();

            $url = "https://api.telegram.org/bot$token/sendMessage";

            $query = [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'markdown',
            ];

            $response = $client->post($url,  [ 'query' => $query ]);

            return json_encode($response->getBody());
        } catch (Throwable $exception) {
            $logData = [
                'to' => $chatId,
                'message' => $message,
                'exception' => $exception->getMessage()
            ];

            Log::channel('tg-api')->info(json_encode($logData));

            return '';
        }
    }
}
