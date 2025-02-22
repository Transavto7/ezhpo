<?php

namespace Src\Users\Management\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Src\Users\Management\Queries\GetUsersListPage\GetUsersListPageHandler;
use Src\Users\Management\Queries\GetUsersListPage\GetUsersListPageQuery;

final class UsersListPageController
{
    public function __invoke(GetUsersListPageHandler $handler)
    {
        $page = $handler->handle(new GetUsersListPageQuery(Auth::user()));

        return view('UsersManagement::list', ['page' => $page]);
    }
}