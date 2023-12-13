<?php

namespace App\Http\Controllers;

use App\Settings;

class SmsController extends Controller
{
    public function sms ($to, $msg) {
        if (config('app.env') !== 'production') return '';

        $api_key = Settings::setting('sms_api_key');

        if($api_key) {
            $body = file_get_contents("https://sms.ru/sms/send?api_id=$api_key&to=$to&msg=".urlencode($msg)."&json=1");

            return json_decode($body);
        }

        return '';
    }
}
