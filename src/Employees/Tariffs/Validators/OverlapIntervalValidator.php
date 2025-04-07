<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Validators;

use DateTimeImmutable;
use Src\Core\Constants\DateFormats;
use Src\Employees\Workdays\Eloquent\Tariff;

final class OverlapIntervalValidator
{
    private $errorMessage = 'Есть пересечение интервалов! ';

    public function validate(
        DateTimeImmutable $dateFrom,
        DateTimeImmutable $dateTo,
        int $townId,
        int $roleId,
        ?int $pointId,
        ?int $excludedTariffId = null
    ): bool {
        $tariffs = Tariff::query()
            ->where('town_id', '=', $townId)
            ->where('role_id', '=', $roleId)
            ->when($pointId !== null, function ($query) use ($pointId) {
                return $query->where('point_id', '=', $pointId);
            })
            ->when($excludedTariffId !== null, function ($query) use ($excludedTariffId) {
                return $query->where('id', '!=', $excludedTariffId);
            })
            ->where('date_from', '<=', $dateTo->format(DateFormats::SYSTEM_DATE))
            ->where('date_to', '>=', $dateFrom->format(DateFormats::SYSTEM_DATE))
            ->get();

        if ($tariffs->isNotEmpty()) {
            $messages = [];
            foreach ($tariffs as $tariff) {
                $messages[] = $tariff->name.': '.$tariff->date_from->format(DateFormats::USER_SHOW_DATE).' - '.$tariff->date_to->format(DateFormats::USER_SHOW_DATE);
            }

            $this->errorMessage .= implode(', ', $messages);

            return true;
        }

        return false;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
