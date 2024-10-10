<?php

namespace App\Http\Controllers;

use Auth;

class OpenApiUiPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('openapi', [
            'apiToken' => $user->api_token,
        ]);
    }
}
