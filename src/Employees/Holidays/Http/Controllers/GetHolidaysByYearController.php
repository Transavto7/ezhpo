<?php
declare(strict_types=1);

namespace Src\Employees\Holidays\Http\Controllers;

use DateTimeImmutable;
use Src\Employees\Holidays\Http\Requests\GetHolidaysByYearRequest;
use Src\Employees\Holidays\Queries\GetHolidaysByYear\GetHolidaysByYearHandler;
use Src\Employees\Holidays\Queries\GetHolidaysByYear\GetHolidaysByYearQuery;

final class GetHolidaysByYearController
{
    public function __invoke(GetHolidaysByYearRequest $request, GetHolidaysByYearHandler $handler)
    {
        $date = DateTimeImmutable::createFromFormat('Y', $request->get('year'));

        return response()->json(
            $handler->handle(new GetHolidaysByYearQuery($date))
        );
    }
}
