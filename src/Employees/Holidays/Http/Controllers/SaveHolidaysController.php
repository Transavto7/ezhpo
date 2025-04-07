<?php

declare(strict_types=1);

namespace Src\Employees\Holidays\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Src\Employees\Holidays\Actions\SaveHolidays\SaveHolidaysAction;
use Src\Employees\Holidays\Actions\SaveHolidays\SaveHolidaysHandler;
use Src\Employees\Holidays\Http\Requests\SaveHolidaysRequest;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class SaveHolidaysController
{
    /**
     * @param SaveHolidaysRequest $request
     * @param SaveHolidaysHandler $handler
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws Throwable
     */
    public function __invoke(SaveHolidaysRequest $request, SaveHolidaysHandler $handler)
    {
        DB::beginTransaction();
        try {
            $handler->handle(new SaveHolidaysAction(
                $request->input('added_days'),
                $request->input('deleted_days'),
            ));
            DB::commit();

            return response('', Response::HTTP_NO_CONTENT);
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
