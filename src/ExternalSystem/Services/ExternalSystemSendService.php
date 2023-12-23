<?php

namespace Src\ExternalSystem\Services;

use App\Anketa;
use App\Driver;
use App\MisAnketa;
use App\MisDriver;
use Carbon\Carbon;
use DomainException;
use Src\ExternalSystem\Exceptions\DriverNotFoundException;
use Src\ExternalSystem\Factories\CaseAmbFactory;
use Src\ExternalSystem\Factories\PatientFactory;
use Src\ExternalSystem\Services\Soap\SoapService;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

final class ExternalSystemSendService implements ExternalSystemSendServiceInterface
{
    /**
     * @var SoapService
     */
    private $soapService;

    public function __construct()
    {
        $this->soapService = new SoapService();
    }

    /**
     * @param int $anketaId
     * @return void
     * @throws Throwable
     */
    public function send(int $anketaId)
    {
        $anketa = Anketa::query()->findOrFail($anketaId);

        if (MisAnketa::where('anketa_id', '=', $anketaId)->exists()) {
            throw new DomainException('Анкета уже отправлена');
        }

        $anketaIdMis = Str::uuid()->toString();

        $driverIdMis = optional(MisDriver::where('driver_id', '=', $anketa->driver_id)->first())->id_mis;
        if (!$driverIdMis) {
            $driverIdMis = Str::uuid()->toString();
        }

        $driver = Driver::query()
            ->where('hash_id', '=', $anketa->driver_id)
            ->first();

        if (!$driver) {
            throw new DriverNotFoundException();
        }

        DB::transaction(function () use ($driverIdMis, $driver, $anketa) {
            $getPatientResponse = $this->soapService->getPatient($driverIdMis);

            if (!count($getPatientResponse['GetPatientResult'])) {
                $patientDto = PatientFactory::fromModel($driver, $driverIdMis);
                $this->soapService->addPatient($patientDto);

                MisDriver::create([
                    'driver_id' => $anketa->driver_id,
                    'id_mis' => $driverIdMis
                ]);
            }
        });

        DB::transaction(function () use ($anketa, $anketaId, $anketaIdMis, $driverIdMis) {
            $caseAmbDto = CaseAmbFactory::fromModel($anketa, $anketaIdMis, $driverIdMis);
            $this->soapService->addCaseAmb($caseAmbDto);

            MisAnketa::create([
                'anketa_id' => $anketaId,
                'id_mis' => $anketaIdMis
            ]);
        });
    }
}
