<?php

namespace App\Http\Controllers;

use Auth;
use Symfony\Component\HttpFoundation\Response;

class OpenApiUiPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->access('openapi_read')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('openapi', [
            'apiToken' => $user->api_token,
        ]);
    }
}
