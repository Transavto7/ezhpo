<?php

namespace App\Exceptions;

use Sentry\Event;

class SentryBeforeSendClosure
{
    public static function beforeSendTransaction(Event $transaction): ?Event
    {
        $ignore = ['api/sdpo/check'];

        $request = $transaction->getRequest();

        $check = array_filter($ignore, function ($url) use ($request) {
            return stripos($request['url'], $url) !== false;
        });

        if (count($check) > 0) {
            return null;
        }

        return $transaction;
    }
}
