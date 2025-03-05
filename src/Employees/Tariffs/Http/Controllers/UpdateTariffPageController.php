<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

use Src\Employees\Tariffs\Queries\GetTariff\GetTariffHandler;
use Src\Employees\Tariffs\Queries\GetTariff\GetTariffQuery;

final class UpdateTariffPageController
{
    public function __invoke(int $tariffId, GetTariffHandler $handler)
    {
        $tariff = $handler->handle(new GetTariffQuery($tariffId));

        return view('Tariffs::edit', ['tariff' => $tariff]);
    }
}
