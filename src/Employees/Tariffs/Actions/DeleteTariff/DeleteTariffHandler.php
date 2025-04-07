<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Actions\DeleteTariff;

use Src\Employees\Workdays\Eloquent\Tariff;
use Src\Employees\Workdays\Eloquent\TariffHour;

final class DeleteTariffHandler
{
    public function handle(DeleteTariffAction $action): void
    {
        TariffHour::query()->where('tariff_id', '=', $action->getTariffId())->delete();
        Tariff::query()->where('id', '=', $action->getTariffId())->delete();
    }
}
