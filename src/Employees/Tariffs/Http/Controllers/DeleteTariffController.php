<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Employees\Tariffs\Actions\DeleteTariff\DeleteTariffAction;
use Src\Employees\Tariffs\Actions\DeleteTariff\DeleteTariffHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class DeleteTariffController
{
    public function __invoke(Request $request, DeleteTariffHandler $handler)
    {
        DB::beginTransaction();
        try {
            $handler->handle(new DeleteTariffAction((int) $request->get('tariff_id')));
            DB::commit();

            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
