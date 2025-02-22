<?php

namespace Src\Users\Management\Http\Controllers;

use Src\Users\Management\Queries\GetUserItem\GetUserItemHandler;
use Src\Users\Management\Queries\GetUserItem\GetUserItemQuery;

final class GetUserItemController
{
    public function __invoke(int $id, GetUserItemHandler $handler)
    {
        $item = $handler->handle(new GetUserItemQuery($id));

        return response()->json($item);
    }
}