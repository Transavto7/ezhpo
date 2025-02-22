<?php

namespace Src\Users\Management\Http\Controllers;

use App\User;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
use Src\Users\Management\Commands\UpdateUserAccess\UpdateUserAccessCommand;
use Symfony\Component\HttpFoundation\Response;

final class UpdateUserAccessController
{
    public function __invoke(int $id, Request $request, Dispatcher  $dispatcher)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Пользователь не найден',
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $dispatcher->dispatch(new UpdateUserAccessCommand(
            $user,
            $request->input('role_ids'),
            $request->input('permission_ids')
        ));
    }
}