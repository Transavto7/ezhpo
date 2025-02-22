<?php

namespace Src\Users\Agreement\Http\Controllers;

use Illuminate\Support\Facades\Auth;

final class AgreementPageController
{
    public function __invoke()
    {
        $user = Auth::user();

        $isDriver = $user->isDriver();

        return view('UsersAgreement::index', [
            'isDriver' => $isDriver,
        ]);
    }
}