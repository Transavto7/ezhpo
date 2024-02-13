<?php

namespace App\Actions\Terminal\Update;

use App\User;
use Illuminate\Http\Request;

final class TerminalUpdateHandler
{
    public function handle(Request $request)
    {
        $user = User::find($request->get('user_id'));
        $user->name = $request->get('name', null);
        $user->timezone = $request->get('timezone', null);
        $user->company_id = $request->get('company_id', null);
        $user->blocked = $request->get('blocked', 0);
        $user->pv_id = $request->get('pv', null);
        $user->stamp_id = $request->get('stamp_id', null);
        $user->save();

        return $user->id;
    }
}
