<?php

namespace App\Actions\Terminal\Store;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

final class TerminalStoreHandler
{
    public function handle(Request $request)
    {
        $api_token = Hash::make(date('H:i:s'));
        $user = User::create([
            'name'     => $request->get('name', null),
            'hash_id'  => mt_rand(1000, 9999).date('s'),
            'timezone' => $request->get('timezone', null),
            'company_id' => $request->get('company_id', null),
            'blocked'  => $request->get('blocked', 0),
            'password' => $api_token,
            'api_token' => $api_token,
            'email' => time() . '@ta-7.ru',
            'login' => time() . '@ta-7.ru',
            'pv_id' => $request->get('pv', null),
            'stamp_id' => $request->get('stamp_id', null),
        ]);

        return $user->id;
    }
}
