<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
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

    public function apiByType(string $type)
    {
        $user = Auth::user();

        if (!$user->access('openapi_read')) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $view = "swagger.{$type}";

        if (!view()->exists($view)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return view($view, [
            'apiToken' => $user->api_token,
        ]);
    }
}
