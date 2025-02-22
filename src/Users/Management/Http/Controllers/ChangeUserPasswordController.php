<?php

namespace Src\Users\Management\Http\Controllers;

use App\Enums\UserEntityType;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

final class ChangeUserPasswordController
{
    public function __invoke(int $id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if ($user->entity_type === UserEntityType::TERMINAL) {
            return response()->json([
                'message' => 'Нельзя указать пароль для терминала'
            ])->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $password = $request->input('password');
        $conformPassword = $request->input('confirm_password');

        if ($password !== $conformPassword) {
            return response()->json([
                'message'
            ])->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $user->password = Hash::make($password);
        $user->save();

        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}