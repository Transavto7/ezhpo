<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Http\Controllers;

use Illuminate\Http\Request;
use Src\Employees\Holidays\Queries\GetHolidaysFromApi\GetHolidaysFromApiHandler;
use Src\Employees\Holidays\Queries\GetHolidaysFromApi\GetHolidaysFromApiQuery;
use Throwable;

final class GetHolidaysFromApiController
{
    public function __invoke(Request $request, GetHolidaysFromApiHandler $handler)
    {
        try {
            $response = $handler->handle(
                new GetHolidaysFromApiQuery((int) $request->input('year'))
            );

            return response()->json($response);
        } catch (Throwable $exception) {
            return response($exception->getMessage(), 500);
        }
    }
}
