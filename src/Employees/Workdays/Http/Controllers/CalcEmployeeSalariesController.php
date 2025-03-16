<?php
declare(strict_types=1);

namespace Src\Employees\Workdays\Http\Controllers;

use App\ValueObjects\ForeignDevice\Alcometer;
use App\ValueObjects\ForeignDevice\Pulse;
use App\ValueObjects\ForeignDevice\Temperature;
use App\ValueObjects\ForeignDevice\Tonometer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Src\Employees\Workdays\Commands\CalcEmployeeSalaries\CalcEmployeeSalariesCommand;
use Src\Employees\Workdays\Commands\CalcEmployeeSalaries\CalcEmployeeSalariesHandler;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class CalcEmployeeSalariesController
{
    public function __invoke(CalcEmployeeSalariesHandler $handler, Request $request): JsonResponse
    {
        try {
            $command = (new CalcEmployeeSalariesCommand())
                ->setDateFrom($request->dateFrom)
                ->setDateTo($request->dateTo);

            $response = $handler->handle($command);

            return response()->json($response);
        } catch (Throwable $exception) {
            $code = $exception->getCode();
            if ($code < 400 || $code >= 600) {
                $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'bt' => $exception->getTrace()
            ], $code);
        }
    }
}
