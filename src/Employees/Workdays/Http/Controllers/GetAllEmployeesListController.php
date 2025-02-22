<?php

declare(strict_types=1);

namespace Src\Employees\Workdays\Http\Controllers;

use App\Enums\BlockActionReasonsEnum;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Employees\Workdays\Commands\GetAllEmployees\GetEmployeeCommand;
use Src\Employees\Workdays\Commands\GetAllEmployees\GetEmployeeHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class GetAllEmployeesListController
{
    public function __invoke(Request $request, GetEmployeeHandler $handler): JsonResponse
    {
        try {
            /** @var User $user */
            $user = $request->user('api');
            if ($user->isBlocked()) {
                throw new Exception(BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::TERMINAL_BLOCK), 400);
            }

            return response()->json($handler->handle(new GetEmployeeCommand()));
        } catch (Throwable $exception) {
            $code = $exception->getCode();
            if ($code < 400 || $code >= 600) {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json([
                'message' => $exception->getMessage(),
            ], $code);
        }
    }
}
