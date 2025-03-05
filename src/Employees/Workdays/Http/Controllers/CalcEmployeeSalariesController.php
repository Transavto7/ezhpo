<?php

declare(strict_types=1);

namespace Src\Employees\Workdays\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Employees\Workdays\Commands\CalcEmployeeSalaries\CalcEmployeeSalariesCommand;
use Src\Employees\Workdays\Commands\CalcEmployeeSalaries\CalcEmployeeSalariesHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CalcEmployeeSalariesController
{
    public function __invoke(CalcEmployeeSalariesHandler $handler, Request $request): JsonResponse
    {
        try {
            $command = new CalcEmployeeSalariesCommand();

            if ($request->year) {
                $command->setYear((int) $request->year);
            }
            if ($request->month) {
                $command->setMonth((int) $request->month);
            }
            if ($request->town) {
                $command->setTown((int) $request->town);
            }
            if ($request->role) {
                $command->setRole((int) $request->role);
            }
            if ($request->pointList && is_array($request->pointList) && ! empty($request->pointList)) {
                $command->setPointList($request->pointList);
            }
            if ($request->employeeList && is_array($request->employeeList) && ! empty($request->employeeList)) {
                $command->setEmployeeList($request->employeeList);
            }

            $response = $handler->handle($command);

            return response()->json($response);
        } catch (Throwable $exception) {
            $code = $exception->getCode();
            if ($code < 400 || $code >= 600) {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'bt' => $exception->getTrace(),
            ], $code);
        }
    }
}
