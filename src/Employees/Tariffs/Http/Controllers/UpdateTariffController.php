<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Core\Constants\DateFormats;
use Src\Employees\Tariffs\Actions\UpdateTariff\UpdateTariffAction;
use Src\Employees\Tariffs\Actions\UpdateTariff\UpdateTariffHandler;
use Src\Employees\Tariffs\ValueObjects\PriceHourInterval;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class UpdateTariffController
{
    /**
     * @throws Throwable
     */
    public function __invoke(int $tariffId, Request $request, UpdateTariffHandler $handler)
    {
        DB::beginTransaction();
        try {
            $handler->handle(new UpdateTariffAction(
                $tariffId,
                $request->input('name'),
                (int) $request->input('town.id'),
                $request->input('point.id') ? (int) $request->input('point.id') : null,
                (int) $request->input('role.id'),
                floatval($request->input('price_cfg')),
                DateTimeImmutable::createFromFormat(DateFormats::SYSTEM_DATE, $request->input('date_from')),
                DateTimeImmutable::createFromFormat(DateFormats::SYSTEM_DATE, $request->input('date_to')),
                (int) $request->input('default_price'),
                array_map(function ($interval) {
                    return new PriceHourInterval(
                        $interval['start'],
                        $interval['end'],
                        $interval['price']
                    );
                }, $request->input('hours')),
            ));

            DB::commit();

            return response('', Response::HTTP_NO_CONTENT);
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
