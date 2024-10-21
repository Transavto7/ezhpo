<?php

namespace App\Actions\Anketa\GetAnketaVerificationDetails;

use App\Anketa;
use App\Enums\AnketLabelingType;
use App\Repositories\AnketaVerifications\AnketaVerificationsRepository;
use App\ViewModels\AnketaVerificationDetails;
use Carbon\Carbon;
use Http\Client\Common\Exception\HttpClientNotFoundException;

final class GetAnketaVerificationDetailsQuery
{
    /**
     * @var AnketaVerificationsRepository
     */
    private $verificationRepository;

    /**
     * @param AnketaVerificationsRepository $verificationRepository
     */
    public function __construct(AnketaVerificationsRepository $verificationRepository)
    {
        $this->verificationRepository = $verificationRepository;
    }

    public function get(GetAnketaVerificationDetailsParams $params): AnketaVerificationDetails
    {
        $anketa = Anketa::where('uuid', '=', $params->getAnketaUuid())->first();

        if (!$anketa) {
            throw new HttpClientNotFoundException();
        }

        $anketaNumber = null;
        $companyName = null;
        $anketaDate = null;
        $driverName = null;
        $carGosNumber = null;

        $verificationDates = $this->verificationRepository->findVerificationDatesByUuid($anketa->uuid);

        $existedVerifications = $this->verificationRepository->findVerificationsByParams($anketa->uuid, $params->getClientHash()->value());
        if (!count($existedVerifications) && !$params->getUserId()) {
            $this->verificationRepository->addAnketVerification($anketa->uuid, $params->getClientHash()->value());
        }

        if ($anketa->in_cart) {
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

            if ($anketa->driver && $anketa->driver->name) {
                $driverName = $anketa->driver->name;
            }

            if ($anketa->date) {
                $anketaDate = Carbon::parse($anketa->date);
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
            $driverName,
            $carGosNumber,
            $verificationDates
        );
    }
}
