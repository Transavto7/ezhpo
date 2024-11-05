<?php

namespace App\Actions\Anketa\GetAnketaVerificationDetails;

use App\Anketa;
use App\Enums\AnketLabelingType;
use App\Exceptions\ExpiredFormPeriodPlException;
use App\ViewModels\AnketaVerificationDetails\AnketaVerificationDetails;
use Carbon\Carbon;
use Http\Client\Common\Exception\HttpClientNotFoundException;

final class GetAnketaVerificationDetailsQuery
{
    /**
     * @throws ExpiredFormPeriodPlException
     */
    public function get(GetAnketaVerificationDetailsParams $params): AnketaVerificationDetails
    {
        $anketa = Anketa::where('uuid', '=', $params->getAnketaUuid())->first();

        if (!$anketa) {
            throw new HttpClientNotFoundException();
        }

        $anketaNumber = null;
        $companyName = null;
        $anketaDate = null;
        $anketaPeriod = null;
        $driverName = null;
        $carGosNumber = null;

        if ($anketa->in_cart === 1) {
            $verified = false;
        }
        else {
            $verified = true;

            $prefixes = [
                AnketLabelingType::MEDIC => 'МО',
                AnketLabelingType::TECH => 'ТО',
            ];
            $anketaNumber = $prefixes[$anketa->type_anketa] . '-' . $anketa->id;

            if ($anketa->company && $anketa->company->name) {
                $companyName = $anketa->company->name;
            }

            if ($anketa->driver && $anketa->driver->fio) {
                $driverName = $anketa->driver->fio;
            }

            if ($anketa->date) {
                $anketaDate = Carbon::parse($anketa->date);
            }
            else if ($anketa->period_pl) {
                $now = Carbon::now();
                $anketaPeriod = Carbon::parse($anketa->period_pl);

                if ($anketaPeriod->year < $now->year || ($anketaPeriod->year === $now->year && $anketaPeriod->month < $now->month)) {
                    throw new ExpiredFormPeriodPlException();
                }
            }

            if ($anketa->car && $anketa->car->gos_number) {
                $carGosNumber = $anketa->car->gos_number;
            }
        }

        return new AnketaVerificationDetails(
            $verified,
            $params->getAnketaUuid(),
            $anketa->id,
            AnketLabelingType::fromString($anketa->type_anketa),
            $anketaNumber,
            $companyName,
            $anketaDate,
            $anketaPeriod,
            $driverName,
            $carGosNumber,
        );
    }
}
