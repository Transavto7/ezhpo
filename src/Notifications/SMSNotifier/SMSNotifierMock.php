<?php

namespace Src\Notifications\SMSNotifier;

use Illuminate\Support\Facades\Log;

final class SMSNotifierMock extends SMSNotifier
{
    public function notify(string $subject, string $message): bool
    {
        Log::channel('sms-api')->info("Вызов уведомления по SMS: $subject, Сообщение: $message");

        return true;
    }
}
