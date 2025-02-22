<?php

namespace Src\Users\Management\Http\Controllers;

use App\User;
use Illuminate\Bus\Dispatcher;
use Src\Users\Management\Commands\UnblockUser\UnblockUserCommand;
use Symfony\Component\HttpFoundation\Response;

final class UnblockUserController
{
    public function __invoke(int $id, Dispatcher $dispatcher)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Пользователь не найден'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if ($user->blocked === 0) {
            return response()->json([
                'message' => 'Пользователь не был заблокирован'
            ])->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $dispatcher->dispatch(new UnblockUserCommand($user));

        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}