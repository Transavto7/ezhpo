<?php

namespace Src\Notifications\TelegramNotifier;

use Illuminate\Support\Facades\Log;

final class TelegramNotifierMock extends TelegramNotifier
{
    public function notify(string $subject, string $message): bool
    {
        Log::channel('tg-api')->info("Вызов уведомления по Telegram: $subject, Сообщение: $message");

        return true;
    }
}
