<?php
declare(strict_types=1);

namespace Src\Employees\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Employees\Commands\GetAllEmployees\GetAllEmployeesHandler;

final class GetAllEmployeesListController
{
    public function __invoke(Request $request, GetAllEmployeesHandler $handler): JsonResponse
    {
        return response()->json($handler->handle());
    }
}
