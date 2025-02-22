<?php

namespace Src\Users\Management\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Src\Users\Management\Queries\GetUserShowPage\GetUserShowPageHandler;
use Src\Users\Management\Queries\GetUserShowPage\GetUserShowPageQuery;

final class ShowUserPageController
{
    public function __invoke(int $id, GetUserShowPageHandler $handler)
    {
        $page = $handler->handle(new GetUserShowPageQuery(Auth::user()));

        return view('UsersManagement::show', ['page' => $page, 'id' => $id]);
    }
}